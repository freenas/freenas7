<?php
/*
	services.inc
	part of FreeNAS (http://www.freenas.org)
	Copyright (C) 2005-2006 Olivier Cochard-Labbé <olivier@freenas.org>.
	All rights reserved.
	
	Based on m0n0wall (http://m0n0.ch/wall)
	Copyright (C) 2003-2006 Manuel Kasper <mk@neon1.net>.
	All rights reserved.
	
	Redistribution and use in source and binary forms, with or without
	modification, are permitted provided that the following conditions are met:
	
	1. Redistributions of source code must retain the above copyright notice,
	   this list of conditions and the following disclaimer.
	
	2. Redistributions in binary form must reproduce the above copyright
	   notice, this list of conditions and the following disclaimer in the
	   documentation and/or other materials provided with the distribution.
	
	THIS SOFTWARE IS PROVIDED ``AS IS'' AND ANY EXPRESS OR IMPLIED WARRANTIES,
	INCLUDING, BUT NOT LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY
	AND FITNESS FOR A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE
	AUTHOR BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY,
	OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF
	SUBSTITUTE GOODS OR SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS
	INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN
	CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE)
	ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE
	POSSIBILITY OF SUCH DAMAGE.
*/

/* include all configuration functions */
require_once("functions.inc");

function services_snmpd_configure() {
	global $config, $g;

	/* kill any running snmpd */
	sigkillbypid("{$g['varrun_path']}/snmpd.pid", "TERM");

	if (isset($config['snmpd']['enable'])) {
	
		if ($g['booting'])
			echo "Starting SNMP agent... ";

		/* generate snmpd.conf */
		$fd = fopen("{$g['varetc_path']}/snmpd.conf", "w");
		if (!$fd) {
			printf("Error: cannot open snmpd.conf in services_snmpd_configure().\n");
			return 1;
		}
			
		$snmpdconf = <<<EOD
syslocation "{$config['snmpd']['syslocation']}"
syscontact "{$config['snmpd']['syscontact']}"
rocommunity "{$config['snmpd']['rocommunity']}"

EOD;

		fwrite($fd, $snmpdconf);
		fclose($fd);

		/* run snmpd */
		mwexec("/usr/local/sbin/snmpd -c {$g['varetc_path']}/snmpd.conf" .
			" -P {$g['varrun_path']}/snmpd.pid");

		if ($g['booting'])
			echo "done\n";
	}

	return 0;
}

function services_samba_configure() {
	global $config, $g;

	/* kill any running samba */
	
	killbyname("smbd");
	killbyname("winbindd");
	killbyname("nmbd");

	if (isset($config['samba']['enable']))
	{
	
		if ($g['booting'])
			echo "Starting Samba... ";

		/* generate smb.conf */
		$fd = fopen("{$g['varetc_path']}/smb.conf", "w");
		if (!$fd) {
			printf("Error: cannot open smb.conf in services_samba_configure().\n");
			return 1;
		}
		
		/* If no share configured, exit */
		if (!is_array($config['mounts']['mount']))
			return 0;

			
		$sambaconf = <<<EOD
[global]
encrypt passwords = yes
netbios name = {$config['samba']['netbiosname']}
workgroup = {$config['samba']['workgroup']}
server string = {$config['samba']['serverdesc']}
load printers = no
security = {$config['samba']['security']}
dns proxy = no
socket options = IPTOS_LOWDELAY TCP_NODELAY
EOD;

		if ($config['samba']['sndbuf']) 	{
			$sambaconf .= <<<EOD
 SO_SNDBUF={$config['samba']['sndbuf']}
EOD;
		}
		else 	{
			$sambaconf .= <<<EOD
 SO_SNDBUF=16384
EOD;
		}
		
		if ($config['samba']['rcvbuf']) 	{
			$sambaconf .= <<<EOD
 SO_RCVBUF={$config['samba']['rcvbuf']}
EOD;
		}
		else 	{
			$sambaconf .= <<<EOD
 SO_RCVBUF=16384
EOD;
		}
		
		if ($config['samba']['winssrv']) 	{
			$sambaconf .= <<<EOD

wins server = {$config['samba']['winssrv']}

EOD;
		}
		
		if (isset ($config['samba']['unixcharset'])) 		{
			$sambaconf .= <<<EOD

unix charset = {$config['samba']['unixcharset']}

EOD;
		}
		else {
			$sambaconf .= <<<EOD

unix charset = UTF-8

EOD;
		
		}
		
		
		
		$sambaconf .= <<<EOD

local master = {$config['samba']['localmaster']}
time server = {$config['samba']['timesrv']}
guest account = ftp
display charset = UTF-8
max log size = 100
#syslog only = yes
syslog = 3
load printers = no
printing = bsd
printcap name = /dev/null
disable spoolss = Yes
log level = {$config['samba']['loglevel']}
dos charset = {$config['samba']['doscharset']}
smb passwd file = {$g['varetc_path']}/private/smbpasswd
private dir = {$g['varetc_path']}/private

EOD;

	switch ($config['samba']['security']) 	{
	case "share":
		$sambaconf .= <<<EOD
create mask = 0666
directory mask = 0777
passdb backend = smbpasswd

EOD;
		
		break;
	case "domain":
		$sambaconf .= <<<EOD
passdb backend = smbpasswd
allow trusted domains = No
dns proxy = No
ldap ssl = no
idmap backend = rid:{$config['samba']['workgroup']}=10000-20000
idmap uid = 10000-20000
idmap gid = 10000-20000
winbind enum groups = yes
winbind enum users = yes
winbind use default domain = Yes
template homedir = /mnt
template shell = /bin/sh

EOD;
	
		break;
	case "user":
		$sambaconf .= <<<EOD
passdb backend = smbpasswd

EOD;
		
		break;
	}
	
	
	
			foreach ($config['mounts']['mount'] as $mountent) {
				/* Unmount filesystem if not booting mode*/
				
				$sambaconf .= <<<EOD
			
[{$mountent['sharename']}]
comment = {$mountent['desc']}
path = /mnt/{$mountent['sharename']}
public = yes
writeable = yes
printable = no
veto files = /.snap/

EOD;

			if ( (is_array($config['samba']['hidemount']) && in_array($mountent['sharename'],$config['samba']['hidemount'])))
			
			$sambaconf .= <<<EOD
browseable = no

EOD;

			}


		fwrite($fd, $sambaconf);
		fclose($fd);

		/* run nmbd and nmbd */
		
		if (isset($config['ad']['enable'])) 	{
			mwexec("/usr/local/bin/net rpc join -S {$config['ad']['ad_srv_name']} -U {$config['ad']['admin_name']}%{$config['ad']['admin_pass']}");
		}

		mwexec("/usr/local/sbin/nmbd -D -s {$g['varetc_path']}/smb.conf");
		
		if (isset($config['ad']['enable'])) 	{
			mwexec("/usr/local/sbin/winbindd -s {$g['varetc_path']}/smb.conf");
			//mwexec("/usr/local/sbin/wbinfo --set-auth-user {$config['ad']['admin_name']}%{$config['ad']['admin_pass']}");
		}
		
		mwexec("/usr/local/sbin/smbd -D -s {$g['varetc_path']}/smb.conf");
		
		
		/* TEST: Wait for samba starting */
		sleep(2);

		/* Generate the samba password file */
		system_user_samba();

		if ($g['booting'])
			echo "done\n";
	}

	return 0;
}

function services_rsyncd_configure() {
	global $config, $g;

	/* kill any running rsync daemon */

	sigkillbypid("{$g['varrun_path']}/rsyncd.pid", "TERM");

	if (isset($config['rsyncd']['enable'])) {
	
		if ($g['booting'])
			echo "Starting RSYNC daemon... ";
		
		/* If no share configured, exit */
		if (!is_array($config['mounts']['mount']))
			return 1;

		/* generate rsyncd.conf */
		$fd = fopen("{$g['varetc_path']}/rsyncd.conf", "w");
		if (!$fd) 	{
			printf("Error: cannot open rsyncd.conf in services_rsyncd_configure().\n");
			return 1;
		}
			
		$rsyncdconf = <<<EOD
read only = {$config['rsyncd']['readonly']}
syslog facility = local4
list = yes
port = {$config['rsyncd']['port']}
pid file = {$g['varrun_path']}/rsyncd.pid

EOD;

		if (isset($config['rsyncd']['rsyncd_user'])) 	{
			$rsyncdconf .= <<<EOD
uid = {$config['rsyncd']['rsyncd_user']}

EOD;

		}
		else 	{
			$rsyncdconf .= <<<EOD
uid = ftp
gid = ftp

EOD;
		}


	
		foreach ($config['mounts']['mount'] as $mountent) 	{
			/* Unmount filesystem if not booting mode*/
				
			$rsyncdconf .= <<<EOD
			
[{$mountent['sharename']}]
comment = {$mountent['desc']}
path = /mnt/{$mountent['sharename']}

EOD;
		}

		fwrite($fd, $rsyncdconf);
		fclose($fd);

		/* run rsync Daemon */
		mwexec("/usr/local/bin/rsync --daemon --config={$g['varetc_path']}/rsyncd.conf");


		if ($g['booting'])
			echo "done\n";
	}

	return 0;
}

function services_nfs_configure() {
	global $config, $g;
	
	global $config, $g;

	/* kill any running nfsd */
	
	killbyname("rpc.statd");
	killbyname("rpc.lockd");
	killbypid("{$g['varrun_path']}/mountd.pid");
	forcekillbyname("nfsd");
	killbyname("rpcbind");

	
	if (isset($config['nfs']['enable']))
	{
	
		if ($g['booting'])
			echo "Starting NFS... ";
		
		/* If no share configured, exit */
		if (!is_array($config['mounts']['mount']))
			return 1;

		/* generate exports */
		$fd = fopen("{$g['varetc_path']}/exports", "w");
		if (!$fd) {
			printf("Error: cannot open exports in services_nfs_configure().\n");
			return 1;
		}
		
		list($network,$subnet) = 
		explode('/', $config['nfs']['nfsnetwork']);
		
		$subnet=gen_subnet_mask($subnet);
		
		$a_mount = &$config['mounts']['mount'];
		
		foreach ($a_mount as $mount)
		{
			$nfsconf .= <<<EOD
/mnt/{$mount['sharename']} -alldirs -maproot=root 
EOD;

			if ($config['nfs']['mapall'] == "yes")
			{
			
			$nfsconf .= <<<EOD
-mapall=root 
EOD;
			
			}
			
			$nfsconf .= <<<EOD
-network {$network} -mask $subnet

EOD;
			
		}
		fwrite($fd, $nfsconf);
		fclose($fd);

		/* run rpcbind, nfsd and mountd */
		mwexec("/usr/sbin/rpcbind");
		mwexec("/usr/sbin/nfsd -u -t -n 4");
		mwexec("/usr/sbin/mountd -r {$g['varetc_path']}/exports");
		mwexec("/usr/sbin/rpc.lockd");
		mwexec("/usr/sbin/rpc.statd");


		if ($g['booting'])
			echo "done\n";
	}

	return 0;
	
}

function services_vsftpd_configure() {
	global $config, $g;

	/* kill any VSFTPD */
	killbyname("vsftpd");

	if (isset($config['ftp']['enable'])) {
	
		if ($g['booting'])
			echo "Starting FTP server... ";

		/* generate vfstpd.conf */
		$fd = fopen("{$g['varetc_path']}/vsftpd.conf", "w");
		if (!$fd) {
			printf("Error: cannot open vsftpd.conf in services_vsftpd_configure().\n");
			return 1;
		}
			
		$ftpconf = <<<EOD
# Standalone mode
listen=YES
background=YES
listen_port={$config['ftp']['port']}
max_clients={$config['ftp']['numberclients']}
max_per_ip={$config['ftp']['maxconperip']}
xferlog_enable=YES
#vsftpd_log_file=/var/log/ftp.log
syslog_enable=YES
# Access rights
anonymous_enable={$config['ftp']['anonymous']}
local_enable={$config['ftp']['localuser']}
write_enable=YES
hide_file=.snap
anon_other_write_enable=YES
anon_upload_enable=YES
anon_mkdir_write_enable=YES
chroot_local_user=YES
local_umask=066
anon_umask=066
chmod_enable=YES
pasv_address={$config['ftp']['pasv_address']}
# Features
ls_recurse_enable=YES
# Performance
idle_session_timeout={$config['ftp']['timeout']}

EOD;
		if (isset($config['ftp']['banner'])) 	{
			$ftpconf .= <<<EOD
ftpd_banner={$config['ftp']['banner']}

EOD;
		}
		else {
		$ftpconf .= <<<EOD
ftpd_banner=Welcome to FreeNAS FTP service

EOD;
		}
		
		if ($config['ftp']['pasv_min_port'] && $config['ftp']['pasv_max_port']) {
			$ftpconf .= <<<EOD
pasv_max_port={$config['ftp']['pasv_max_port']}
pasv_min_port={$config['ftp']['pasv_min_port']}

EOD;
		}
		
		if ($config['ftp']['pasv_address']) {
			$ftpconf .= <<<EOD
pasv_address={$config['ftp']['pasv_address']}

EOD;
		}
		
		fwrite($fd, $ftpconf);
		fclose($fd);

		/* run vsftpd */
		mwexec("/usr/local/sbin/vsftpd {$g['varetc_path']}/vsftpd.conf");


		if ($g['booting'])
			echo "done\n";
	}

	return 0;
}

function services_pureftpd_configure() {

	// Stop the pure-ftpd process and re-start it
	
	global $config, $g;

	/* kill any Pure-FTPD */
	killbyname("pure-ftpd");

	if (isset($config['ftp']['enable'])) {
	
		if ($g['booting'])
			echo "Starting FTP server... ";
			
		/* Generate the banner file */
			
		$fd = fopen("/mnt/.banner", "w");
		if (!$fd) {
			printf("Error: cannot open .banner in services_pureftpd_configure().\n");
			return 1;
		}
		
		if (empty($config['ftp']['banner'])) {
			$banner = <<<EOD
Welcome to FreeNAS FTP service
	
EOD;
		} else {
			$banner = <<<EOD
{$config['ftp']['banner']}
	
EOD;
		}
			
		fwrite($fd, $banner);
		fclose($fd);
			
			
		/* run pure-ftpd */
		
		$cmd = "/usr/local/sbin/pure-ftpd -S {$config['ftp']['port']} -c {$config['ftp']['numberclients']} -C {$config['ftp']['maxconperip']} -I {$config['ftp']['timeout']} -M U 077:077 -B -b ";
		
		if ($config['ftp']['anonymous'] == "no")
			$cmd .= "-E ";
		
		if ($config['ftp']['pasv_min_port'] && $config['ftp']['pasv_max_port'])
			$cmd .= "-p {$config['ftp']['pasv_min_port']}:{$config['ftp']['pasv_max_port']} ";
			
		if ($config['ftp']['pasv_address'])
			$cmd .= "-P {$config['ftp']['pasv_address']} ";
		
		mwexec($cmd);


		if ($g['booting'])
			echo "done\n";
	}
			
		
	return 0;
}

function services_smart_configure() {
	global $config, $g;
	
	/* kill any running smartd */
	killbyname("smartd");

	if (isset($config['system']['smart'])) {
	
		if ($g['booting'])
			echo "Starting smartd... ";

		/* run smartd */
		mwexec("/usr/local/sbin/smartd --logfacility=local5");

		if ($g['booting'])
			echo "done\n";
	}

	return 0;
}


function services_sshd_configure() {
	global $config, $g;

	/* kill any SSHD */
	sigkillbypid("{$g['varrun_path']}/sshd.pid", "TERM");

	if (isset($config['sshd']['enable'])) {
	
		if ($g['booting'])
			echo "Starting SSH server... ";

		/* generate /ssh/sshd_config */
		$fd = fopen("{$g['varetc_path']}/ssh/sshd_config", "w");
		if (!$fd) {
			printf("Error: cannot open /ssh/sshd_config in services_sshd_configure().\n");
			return 1;
		}
			
		$sshconf = <<<EOD
SyslogFacility LOCAL3
UseDNS no
Subsystem       sftp    /usr/libexec/sftp-server

EOD;

	if (isset($config['sshd']['port'])) {
	
		$sshconf .= <<<EOD
Port {$config['sshd']['port']}

EOD;
	
	}

		fwrite($fd, $sshconf);
		fclose($fd);
		
		
		if ($config['sshd']['private-key']) {
			$key = base64_decode($config['sshd']['private-key']);
		} else {
			/* default certificate/key */
			
			$key = <<<EOD
-----BEGIN DSA PRIVATE KEY-----
MIIDPwIBAAKCAQEA8fkdytzZDMiLspAt/Xs35pS4V/Bvu4rG6iPOY7eTJvecx2fe
z6t9MIktZMl4cJ4pvrIykMXVcZdnBU36iZZXwKwD22K84djNnCzdvRAH8kQ2NiaE
ds2QqKOboRZdLK+wnZG4WPQ8Lz5y/vtkXHtVOevBmToTFy/u/nWP6qjkF6MsvVnJ
shKV3+feRBYT4YmoFgn3On/blcSXuZpwfTajnyVGF+rKC6cOUqbjKKrfil+QDsVL
h4c3Nxca+a1CUAd4O2zUA9sqrw4ChijIKjSCMr2dsDBt+bTKoznX54ijcfrEtaNI
F0wj2C6qd00lamz0+kodrdu8zXEUHe9+hBnqbwIVAKf8ZCwMgP4ZpqwwNw4vIn1A
uLnfAoIBAQCVfUrpUWFvf/TXPucJde4CuAmtoMOrjpepAiXK7N9dwGyq/PbVxr4t
nJ/RTyNGOFmBroc6/n0MnxR0qmkQPJNtM/Yz+kk+BCgwsyu2uenVOIX/eJFuQPQY
iUdktTcgAyChMp99WF4yfKKgv1CDdMkpFi8xgBEN03s1sOKCRNwJ5rlpTNqh9Lat
uRyzWOIjNd7atkEYIQK92idJgqSmleo+UhJFfoOGjYlRbsnRVbvfqh7GVd7SSydh
Khdb2eZjj2J8eMBwHNl1FLtqt02cnFW3FQDdXPbYYakN25z3F3sex/CPuBGJ0HRG
q+y/Ynj/m99TPq9vLkzSUQPR4MmQ5feoAoIBAG5L9ffMc/8T9dTeF7FEPlS54ka7
3M+pNY/5ehMykrrS9CVjFmvpeclnxkBpvjt3G5IlvkSsjUEE6kMk7mW9EV+USL0T
TU/LavxXD8fLCSiIwResfLDRxjixjxVI1ouZeKNQ6B3tPOWOEIKR5nPlc7iy435n
S77/NM3yBFH0KGdepr+3ZmdgWAjDLKjQhNyCz4Joc1IH1Vf5Ccvb6rsaJ91ajiq2
9iI2ZpLXXIQsS1ZYzO1Gr9xBTNgmzEmeLqFMcxDSJ+rLMF4VDjRdL2zz5BSmv/Ff
j2nICMgv/gj3zzuk7zcMpnbvGyA3W8VWb6IjJDvww4rJ21Q2gHBC5XCohJsCFQCD
54IUfvQ56Flj87DvLXtOxoffWA==
-----END DSA PRIVATE KEY-----

EOD;
		}
		
		$fd = fopen("{$g['varetc_path']}/ssh/ssh_host_dsa_key", "w");
		if (!$fd) {
			printf("Error: cannot open /ssh/ssh_host_dsa_key in service_sshd_configure().\n");
			return 1;
		}
		
		fwrite($fd, $key);
		fclose($fd);
		
		/* Restrict right on the SSH key */
		exec("/bin/chmod 600 {$g['varetc_path']}/ssh/ssh_host_dsa_key");

		/* run sshd */
		mwexec("/usr/sbin/sshd");


		if ($g['booting'])
			echo "done\n";
	}

	return 0;
}

function services_cron_configure() {
	global $config, $g;
	
	/* kill any running cron */
	killbyname("cron");
			
	/* generate crontab */
	$fd = fopen("{$g['varetc_path']}/crontab", "w");
	
	$crontabconf = <<<EOD
SHELL=/bin/sh
PATH=/etc:/bin:/sbin:/usr/bin:/usr/sbin
HOME=/var/log
#
#minute hour    mday    month   wday    who     command
#

EOD;

	if (!$fd) 	{
		printf("Error: cannot open crontab in services_cron_configure().\n");
		return 1;
	}

	if (isset($config['rsyncclient']['enable'])) {
		
		if ($g['booting'])
			echo "Configuring cron for rsync client... ";
		
		if ($config['rsyncclient']['all_mins'] == "0") 	{ 
			foreach ($config['rsyncclient']['minute'] as $minutev) 	{
				if (strlen($cron_min) > 0)
					$cron_min = $cron_min . "," . $minutev;
				else
					$cron_min = $minutev;
			}
		}
		else
			$cron_min = "*";
	
	
		if ($config['rsyncclient']['all_hours'] == "0") { 
			foreach ($config['rsyncclient']['hour'] as $hourv) 	{
				if (strlen($cron_hour) > 0)
					 $cron_hour = $cron_hour . "," . $hourv;
				else
					 $cron_hour = $hourv;
			}
		}
		else
			$cron_hour = "*";
		
		if ($config['rsyncclient']['all_days'] == "0") 	{ 
			foreach ($config['rsyncclient']['day'] as $dayv) 	{
				if (strlen($cron_day) > 0)
					$cron_day = $cron_day . "," . $dayv;
				else
					$cron_day = $dayv;
			}
		}
		else
			$cron_day = "*";
		
		if ($config['rsyncclient']['all_months'] == "0") 	{
		  foreach ($config['rsyncclient']['month'] as $monthv) 	  {
			if (strlen($cron_month) > 0)
				$cron_month = $cron_month . "," . $monthv;
			else
				$cron_month = $monthv;
		  }
		}
		else
			$cron_month = "*";
		
		
		if ($config['rsyncclient']['all_weekday'] == "0") 	{
			foreach ($config['rsyncclient']['weekday'] as $weekdayv) 	{
				if (strlen($cron_weekday) > 0)
					 $cron_weekday = $cron_weekday . "," . $weekdayv;
				else
					 $cron_weekday = $weekdayv;
			}
		}
		else
			$cron_weekday = "*";		
		
		$crontabconf .= <<<EOD
{$cron_min}    {$cron_hour}        {$cron_day}        {$cron_month}        {$cron_weekday}       root    {$g['varrun_path']}/sync.sh

EOD;

		if ($g['booting'])
			echo "done\n";
	}
	
	// Erase all variable used previously
	unset ($cron_min, $cron_hour, $cron_day, $cron_month, $cron_weekday);
		
	if (isset($config['shutdown']['enable'])) {
		
		if ($g['booting'])
			echo "Configuring cron for shutdown... ";
		
		if ($config['shutdown']['all_mins'] == "0") { 
			foreach ($config['shutdown']['minute'] as $minutev) 	{
				if (strlen($cron_min) > 0)
					$cron_min = $cron_min . "," . $minutev;
				else
					$cron_min = $minutev;
			}
		}
		else
			$cron_min = "*";
	
	
		if ($config['shutdown']['all_hours'] == "0") 	{ 
			foreach ($config['shutdown']['hour'] as $hourv) 	{
				if (strlen($cron_hour) > 0)
					 $cron_hour = $cron_hour . "," . $hourv;
				else
					 $cron_hour = $hourv;
			}
		}
		else
			$cron_hour = "*";
		
		if ($config['shutdown']['all_days'] == "0") { 
			foreach ($config['shutdown']['day'] as $dayv) 	{
				if (strlen($cron_day) > 0)
					$cron_day = $cron_day . "," . $dayv;
				else
					$cron_day = $dayv;
			}
		}
		else
			$cron_day = "*";
		
		if ($config['shutdown']['all_months'] == "0") 	{
			foreach ($config['shutdown']['month'] as $monthv)   {
				if (strlen($cron_month) > 0)
					$cron_month = $cron_month . "," . $monthv;
				else
					$cron_month = $monthv;
			}
		}
		else
			$cron_month = "*";
		
		
		if ($config['shutdown']['all_weekday'] == "0") 	{
			foreach ($config['shutdown']['weekday'] as $weekdayv) 		{
				if (strlen($cron_weekday) > 0)
					 $cron_weekday = $cron_weekday . "," . $weekdayv;
				else
					 $cron_weekday = $weekdayv;
			}
		}
		else
			$cron_weekday = "*";		
		
		$crontabconf .= <<<EOD
{$cron_min}    {$cron_hour}        {$cron_day}        {$cron_month}        {$cron_weekday}       root    /etc/rc.shutdown

EOD;

		if ($g['booting'])
			echo "done\n";
	}
				
	fwrite($fd, $crontabconf);
	fclose($fd);

	/* run cron */
	mwexec("/usr/sbin/cron -s");
	
	return 0;
	
}

function services_rsyncclient_configure()
{
	global $config, $g;

	// Generate a shell script that is used by cron
	// Generate crontab works
	
	if (isset($config['rsyncclient']['enable']))
	{	
	
		if ($g['booting'])
			echo "Generating synchronizing cron script... ";
		
		/* generate /var/run/sync.sh script */
		$fd = fopen("{$g['varrun_path']}/sync.sh", "w");
		if (!$fd)
		{
			printf("Error: cannot open /var/run/sync.sh in services_rsyncclient_configure().\n");
			return 1;
		}

	$syncscript = <<<EOD
#!/bin/sh

if [ -r /var/run/rsync-running ]; then
exit
fi
/usr/bin/touch /var/run/rsync-running

EOD;

		$opt_delete="";
		if (isset($config['rsyncclient']['opt_delete']))
			$opt_delete = "--delete --delete-after ";
	
		foreach ($config['rsyncclient']['sharetosync'] as $sharek => $sharev)
		{
			$syncscript .= <<<EOD
/usr/local/bin/rsync -rtz {$opt_delete}rsync://{$config['rsyncclient']['rsyncserverip']}/{$sharev} /mnt/{$sharev}

EOD;
		}
	
		$syncscript .= <<<EOD
/bin/rm -f /var/run/rsync-running

EOD;

		fwrite($fd,$syncscript);
		fclose($fd);
		
		/* Add exectutive right to the script */
		exec("/bin/chmod 770 {$g['varrun_path']}/sync.sh");
		
		if ($g['booting'])
			echo "done\n";
		return 0;
	}
	
	return 0;
}

function services_radius_configure()
{
	global $config, $g;

	if (isset($config['radius']['enable']))
	{
	
		/* generate radius.conf */
		$fd = fopen("{$g['varetc_path']}/radius.conf", "w");
		if (!$fd) {
			printf("Error: cannot open radius.conf in services_radius_configure().\n");
			return 1;
		}
			
		$radiusconf = <<<EOD
auth  {$config['radius']['radiusip']}:{$config['radius']['port']} {$config['radius']['secret']} {$config['radius']['timeout']} {$config['radius']['maxretry']}

EOD;

		fwrite($fd, $radiusconf);
		fclose($fd);
	}

	return 0;
}

function services_afpd_configure()
{
	global $config, $g;

	/* kill any VSFTPD */
	killbyname("afpd");

	if (isset($config['afp']['enable'])) 	{
	
		/* If no share configured, exit */
		if (!is_array($config['mounts']['mount']))
			return 1;
			
		if ($g['booting'])
			echo "Starting AFP server... ";
			
		if (empty($config['afp']['afpname']))
			$afpname="-";
		else
			$afpname=$config['afp']['afpname'];
			
		/* generate afpd.conf */
		$fd = fopen("{$g['varetc_path']}/afpd.conf", "w");
		if (!$fd) 		{
			printf("Error: cannot open afpd.conf in services_afpd_configure().\n");
			return 1;
		}
			
		$afpconf = <<<EOD
{$afpname} -transall -uamlist
EOD;

		if (isset($config['afp']['guest']) && isset($config['afp']['local']) )
			$afpconf .= <<<EOD
 uams_guest.so,uams_clrtxt.so,uams_dhx.so
EOD;
		else if (isset($config['afp']['guest']))
			$afpconf .= <<<EOD
 uams_guest.so
EOD;
		else if (isset($config['afp']['local']))
			$afpconf .= <<<EOD
 uams_clrtxt.so,uams_dhx.so
EOD;
		
		$afpconf .= <<<EOD
 -nosavepassword -defaultvol {$g['varetc_path']}/AppleVolumes.default -systemvol {$g['varetc_path']}/AppleVolumes.system -uampath /etc/uams -guestname ftp

EOD;
		fwrite($fd, $afpconf);
		fclose($fd);
		
		/* generate AppleVolumes.default */
		$fd = fopen("{$g['varetc_path']}/AppleVolumes.default", "w");
		if (!$fd)
		{
			printf("Error: cannot open AppleVolumes.default in services_afpd_configure().\n");
			return 1;
		}
		
		$a_mount = &$config['mounts']['mount'];
		foreach ($a_mount as $mount)
		{
			$AppleVolumes .= <<<EOD
/mnt/{$mount['sharename']} "{$mount['sharename']}" 

EOD;
		
		}
		
		fwrite($fd, $AppleVolumes);
		fclose($fd);


		/* run afpdpd */
		mwexec("/usr/local/sbin/afpd -F {$g['varetc_path']}/afpd.conf");


		if ($g['booting'])
			echo "done\n";
	}

	return 0;
}

function services_nis_configure()
{
	global $config, $g;

	return 0;
}

function services_iscsi_configure()
{
	global $config, $g;
	
	if (isset($config['iscsi']['enable']))
	{
		if ($g['booting'])
			echo "Starting iSCSI driver... ";
			
		mwexec("/sbin/kldload kdload /boot/kernel/iscsi_initiator.ko");
		mwexec("/sbin/sysctl debug.iscsi=0");
		mwexec("/usr/local/sbin/iscontrol targetaddress={$config['iscsi']['targetaddress']} TargetName={$config['iscsi']['targetname']}");
		
		if ($g['booting'])
			echo "done\n";
	}
	
	return 0;
}

function services_howl_configure()
{
	global $config, $g;
	
	
	/* kill any mDNSResponder */
	killbyname("mDNSResponder");
	
	if (isset($config['system']['howl_disable']))
		return 0;
	
	$fd = fopen("{$g['varetc_path']}/mDNSResponder.conf", "w");
	if (!$fd)
	{
		printf("Error: cannot open mDNSResponder.conf in services_howl_configure().\n");
		return 1;
	}
	
	if ($config['system']['webgui']['port'])
	{
	$mDNSResponder = <<<EOD
"{$config['system']['hostname']} Web Admin"	_http._tcp	local.	{$config['system']['webgui']['port']}

EOD;
	}
	else
	{
	$mDNSResponder = <<<EOD
"{$config['system']['hostname']} Web Admin"	_http._tcp	local.	80

EOD;
	}
	
	
	if (isset($config['afp']['enable']))
	{				
		$mDNSResponder .= <<<EOD
"{$config['system']['hostname']} AFP Server"	_afpovertcp._tcp	local.	548

EOD;
	}
	
	if (isset($config['rsyncd']['enable']))
	{				
		$mDNSResponder .= <<<EOD
"{$config['system']['hostname']} RSYNC Server"	_rsync._tcp.	local.	{$config['rsyncd']['port']}

EOD;
	}
	
	if (isset($config['sshd']['enable']))
	{				
		$mDNSResponder .= <<<EOD
"{$config['system']['hostname']} SSH Server"	_ssh._tcp	local.	{$config['sshd']['port']}

EOD;
	}
	
	if (isset($config['ftp']['enable']))
	{				
		$mDNSResponder .= <<<EOD
"{$config['system']['hostname']} FTP Server"	_ftp._tcp	local.	{$config['ftp']['port']}

EOD;
	}
	
	if (isset($config['samba']['enable']))
	{				
		$mDNSResponder .= <<<EOD
"{$config['system']['hostname']} Samba Server"	_smb._tcp	local.	139

EOD;
	}
	
	if (isset($config['nfs']['enable']))
	{				
		
		/* If no share configured, exit */
		if (!is_array($config['mounts']['mount']))
			break;
		
		$a_mount = &$config['mounts']['mount'];
		
		foreach ($a_mount as $mount)
		{
		$mDNSResponder .= <<<EOD
"{$config['system']['hostname']} NFS: {$mount['sharename']}"	_nfs._tcp	local.	2049 path=/mnt/{$mount['sharename']}

EOD;
		}
	}
	
	fwrite($fd, $mDNSResponder);
	fclose($fd);


	/* run mDNSResponder */
	mwexec("/usr/local/sbin/mDNSResponder -f {$g['varetc_path']}/mDNSResponder.conf");

	if ($g['booting'])
		echo "done\n";
	
	return 0;
}

function services_mdnsresponder_configure()
{
	global $config, $g;
	
	
	/* kill any running snmpd */
	sigkillbypid("{$g['varrun_path']}/mDNSResponder.pid", "TERM");

	
	if (isset($config['system']['howl_disable']))
		return 0;
	
	$fd = fopen("{$g['varetc_path']}/mDNSResponder.conf", "w");
	if (!$fd) 	{
		printf("Error: cannot open mDNSResponder.conf in services_bonjour_configure().\n");
		return 1;
	}
	
	if ($config['system']['webgui']['port']) 	{
	$mDNSResponder = <<<EOD
"{$config['system']['hostname']} Web Admin"
_http._tcp local.
{$config['system']['webgui']['port']}

EOD;
	}
	else 	{
	$mDNSResponder = <<<EOD
"{$config['system']['hostname']} Web Admin"
_http._tcp local.
80

EOD;
	}
	
	
	if (isset($config['afp']['enable'])) 	{				
		$mDNSResponder .= <<<EOD
"{$config['system']['hostname']} AFP Server"
_afpovertcp._tcp local.
548

EOD;
	}
	
	if (isset($config['rsyncd']['enable'])) 	{				
		$mDNSResponder .= <<<EOD
"{$config['system']['hostname']} RSYNC Server"
_rsync._tcp. local.
{$config['rsyncd']['port']}

EOD;
	}
	
	if (isset($config['sshd']['enable'])) 	{				
		$mDNSResponder .= <<<EOD
"{$config['system']['hostname']} SSH Server"
_ssh._tcp local.
{$config['sshd']['port']}

EOD;
	}
	
	if (isset($config['ftp']['enable'])) 	{				
		$mDNSResponder .= <<<EOD
"{$config['system']['hostname']} FTP Server"
_ftp._tcp local.
{$config['ftp']['port']}

EOD;
	}
	
	if (isset($config['samba']['enable'])) 	{				
		$mDNSResponder .= <<<EOD
"{$config['system']['hostname']} Samba Server"
_smb._tcp local.	
139

EOD;
	}
	
	if (isset($config['nfs']['enable'])) 	{				
		
		/* If no share configured, exit */
		if (!is_array($config['mounts']['mount']))
			break;
		
		$a_mount = &$config['mounts']['mount'];
		
		foreach ($a_mount as $mount) 		{
		$mDNSResponder .= <<<EOD
"{$config['system']['hostname']} NFS: {$mount['sharename']}"
_nfs._tcp local.
2049 path=/mnt/{$mount['sharename']}

EOD;
		}
	}
	
	fwrite($fd, $mDNSResponder);
	fclose($fd);


	/* run mDNSResponder */
	mwexec("/usr/local/sbin/mDNSResponderPosix -b -f {$g['varetc_path']}/mDNSResponder.conf");

	if ($g['booting'])
		echo "done\n";
	
	return 0;
}


?>
