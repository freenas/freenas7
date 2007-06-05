<?php
/*
	services.inc
	part of FreeNAS (http://www.freenas.org)
	Copyright (C) 2005-2007 Olivier Cochard-Labbé <olivier@freenas.org>.
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

/* Start SNMPD
Return 0 if OK, 1 if erro */
function services_snmpd_configure() {
	global $config, $g;

	$result=0;

	/* kill any running snmpd */
	sigkillbypid("{$g['varrun_path']}/snmpd.pid", "TERM");

	if (isset($config['snmpd']['enable'])) {

		if ($g['booting'])
			write_console("Starting SNMP agent... ");

		/* generate snmpd.conf */
		$fd = fopen("{$g['varetc_path']}/snmpd.conf", "w");
		if (!$fd) {
			$message = "Error: Can't open snmpd.conf in services_snmpd_configure()";
			write_console($message . ".\n");
			write_log($message);
			$result=1;
		}

		$snmpdconf = <<<EOD
syslocation "{$config['snmpd']['syslocation']}"
syscontact "{$config['snmpd']['syscontact']}"
rocommunity "{$config['snmpd']['rocommunity']}"

EOD;

		fwrite($fd, $snmpdconf);
		fclose($fd);

		/* run snmpd */
		$result = mwexec("/usr/local/sbin/snmpd -c {$g['varetc_path']}/snmpd.conf" .
			" -P {$g['varrun_path']}/snmpd.pid");

		if(!$result) {
			if($g['booting'])
				write_console("done\n");
			write_log("SNMP Daemon started");
		} else {
			if($g['booting'])
				write_console("failed\n");
			write_log("Error: Failed to start SNMP Daemon");
		}
	}

	return 0;
}

/* Stop samba, Generate the samba configuration file and start samba
Return 0 if OK, 1 or error code if error */
function services_samba_configure() {
	global $config, $g;

	$result = 0;

	/* kill any running samba */
	killbyname("smbd");
	killbyname("winbindd");
	killbyname("nmbd");

	if (isset($config['samba']['enable'])) {
		if ($g['booting'])
			write_console("Starting Samba... ");

		/* generate smb.conf */
		$fd = fopen("{$g['varetc_path']}/smb.conf", "w");
		if (!$fd) {
			$message = "Error: Can't open smb.conf in services_samba_configure()";
			write_console($message . ".\n");
			write_log($message);
			$result = 1;
		}

		/* If no share configured, exit */
		if (!is_array($config['mounts']['mount'])) {
			$message = "Error: Can't start CIFS (samba). No mount points configured.";
			write_log($message);
			return 0;
		}

		$sambaconf = <<<EOD
[global]
encrypt passwords = yes
netbios name = {$config['samba']['netbiosname']}
workgroup = {$config['samba']['workgroup']}
server string = {$config['samba']['serverdesc']}
load printers = no
security = {$config['samba']['security']}
dns proxy = no
# Settings to enhance performance:
use sendfile = yes
strict locking = no
read raw = yes
write raw = yes
oplocks = yes
max xmit = 65535
deadtime = 15
getwd cache = yes
socket options = IPTOS_LOWDELAY TCP_NODELAY
EOD;

		if ($config['samba']['sndbuf']) {
			$sambaconf .= <<<EOD
 SO_SNDBUF={$config['samba']['sndbuf']}
EOD;
		}
		else 	{
			$sambaconf .= <<<EOD
 SO_SNDBUF=16384
EOD;
		}

		if ($config['samba']['rcvbuf']) {
			$sambaconf .= <<<EOD
 SO_RCVBUF={$config['samba']['rcvbuf']}
EOD;
		}
		else 	{
			$sambaconf .= <<<EOD
 SO_RCVBUF=16384
EOD;
		}

		$sambaconf .= <<<EOD

# End of performance section

EOD;

		if ($config['samba']['winssrv']) {
			$sambaconf .= <<<EOD
wins server = {$config['samba']['winssrv']}

EOD;
		}

		if (isset ($config['samba']['unixcharset'])) {
			$sambaconf .= <<<EOD
unix charset = {$config['samba']['unixcharset']}

EOD;
		} else {
			$sambaconf .= <<<EOD
unix charset = UTF-8

EOD;
		}

		if (!isset($config['samba']['largereadwrite'])) {
			$sambaconf .= <<<EOD
large readwrite = no

EOD;
		}

		if (isset($config['samba']['easupport'])) {
			$sambaconf .= <<<EOD
ea support = yes

EOD;
		}

		$sambaconf .= <<<EOD
local master = {$config['samba']['localmaster']}
time server = {$config['samba']['timesrv']}
guest account = ftp
display charset = UTF-8
max log size = 10
syslog only = yes
syslog = {$config['samba']['loglevel']}
load printers = no
printing = bsd
printcap name = /dev/null
disable spoolss = Yes
log level = {$config['samba']['loglevel']}
dos charset = {$config['samba']['doscharset']}
smb passwd file = {$g['varetc_path']}/private/smbpasswd
private dir = {$g['varetc_path']}/private

EOD;

	switch ($config['samba']['security'])	{
		case "share":
			$createmask = "0666";
			$directorymask = "0777";

			if (!empty($config['samba']['createmask'])) {
				$createmask = $config['samba']['createmask'];
			}

			if (!empty($config['samba']['directorymask'])) {
				$directorymask = $config['samba']['directorymask'];
			}

			$sambaconf .= <<<EOD
passdb backend = smbpasswd
create mask = {$createmask}
directory mask = {$directorymask}
force user = ftp
force group = ftp

EOD;

			$guestmode = <<<EOD
guest ok = yes
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

			$guestmode = <<<EOD
guest ok = no
EOD;
			break;

		case "user":
			$sambaconf .= <<<EOD
passdb backend = smbpasswd

EOD;
			$guestmode = <<<EOD
guest ok = no
EOD;
			break;
		}

		foreach ($config['mounts']['mount'] as $mountent) {
			$sambaconf .= <<<EOD

[{$mountent['sharename']}]
comment = {$mountent['desc']}
path = /mnt/{$mountent['sharename']}
writeable = yes
printable = no
veto files = /.snap/
hide dot files = yes
{$guestmode}

EOD;

			if ((is_array($config['samba']['hidemount']) && in_array($mountent['sharename'],$config['samba']['hidemount']))) {
				$sambaconf .= <<<EOD
browseable = no

EOD;
			}

			if (isset($config['samba']['recyclebin'])) {
				$sambaconf .= <<<EOD
vfs objects = recycle
recycle:keeptree = Yes
recycle:versions = Yes

EOD;
			}
			if (isset($config['samba']['readahead'])) {
				$sambaconf .= <<<EOD
vfs objects = readahead

EOD;
			}
			if (isset($config['afp']['enable'])) 	{
				$sambaconf .= <<<EOD
vfs objects = netatalk

EOD;
			}
		}

		fwrite($fd, $sambaconf);
		fclose($fd);

		/* Run nmbd and nmbd if no previous error. */
		if(!$result) {
			if (isset($config['ad']['enable'])) {
				$result |= mwexec("/usr/local/bin/net rpc join -S {$config['ad']['ad_srv_name']} -U {$config['ad']['admin_name']}%{$config['ad']['admin_pass']}");
			}

			$result |= mwexec("/usr/local/sbin/nmbd -D -s {$g['varetc_path']}/smb.conf");

			if (isset($config['ad']['enable'])) {
				mwexec("/usr/local/sbin/winbindd -s {$g['varetc_path']}/smb.conf");
				//mwexec("/usr/local/bin/wbinfo --set-auth-user {$config['ad']['admin_name']}%{$config['ad']['admin_pass']}");
			}

			$result |= mwexec("/usr/local/sbin/smbd -D -s {$g['varetc_path']}/smb.conf");

			/* TEST: Wait for samba starting */
			sleep(2);

			/* Generate the samba password file */
			$result |= system_create_smbpasswd();

			if(!$result) {
				if($g['booting'])
					write_console("done\n");
				write_log("Samba started");
			} else {
				if($g['booting'])
					write_console("failed\n");
				write_log("Error: Failed to start Samba");
			}
		}
	}

	return $result;
}

/* Stop rsync, Generate the rsync configuration file and start rsync
Return 0 if OK, 1 or error code if error */
function services_rsyncd_configure() {
	global $config, $g;

	$result = 0;

	/* kill any running rsync daemon */
	sigkillbypid("{$g['varrun_path']}/rsyncd.pid", "TERM");

	if (isset($config['rsyncd']['enable'])) {

		if ($g['booting'])
			write_console("Starting RSYNC daemon... ");


		/* If no share configured, exit */
		if (!is_array($config['mounts']['mount'])) {
			$message = "Error: No mount point configured, Can't start rsyncd";
			write_log($message);
			return 0;
		}

		/* generate rsyncd.conf */
		$fd = fopen("{$g['varetc_path']}/rsyncd.conf", "w");
		if (!$fd) 	{
			$message = "Error: Can't open rsyncd.conf in services_rsyncd_configure()";
			write_console($message . ".\n");
			write_log($message);
			$result = 1;
		}

		$rsyncdconf = <<<EOD
read only = {$config['rsyncd']['readonly']}
syslog facility = local4
list = yes
port = {$config['rsyncd']['port']}
pid file = {$g['varrun_path']}/rsyncd.pid

EOD;

		if (!empty($config['rsyncd']['maxcon'])) {
		$rsyncdconf .= <<<EOD
max connections = {$config['rsyncd']['maxcon']}

EOD;
		}

		if (!empty($config['rsyncd']['motd'])) {
		$rsyncdconf .= <<<EOD
motd file = {$g['varetc_path']}/rsyncd.motd

EOD;
		}

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

		// Generate MOTD file if configured
		if (!empty($config['rsyncd']['motd'])) {
			$fd = fopen("{$g['varetc_path']}/rsyncd.motd", "w");
			if (!$fd) 	{
				$message = "Error: Can't open rsyncd.motd in services_rsyncd_configure()";
				write_console($message . ".\n");
				write_log($message);
				$result = 1;
			}

			$motd  = <<<EOD
{$config['rsyncd']['motd']}

EOD;

			fwrite($fd, $motd);
			fclose($fd);
		}

		/* Run rsync daemon if no previous error. Preserve times when synchronizing. */
		if (!$result) {
			$result = mwexec("/usr/local/bin/rsync --daemon --config={$g['varetc_path']}/rsyncd.conf");
		}

		if(!$result) {
			if($g['booting'])
				write_console("done\n");
			write_log("RSYNC Daemon started");
		} else {
			if($g['booting'])
				write_console("failed\n");
			write_log("Error: Failed to start RSYNC Daemon");
		}
	}

	return $result;
}

/* Stop nfs processes, Generate the nfs configuration file and start nfsd processes
Return 0 if OK, 1 or error code if error */
function services_nfs_configure() {
	global $config, $g;

	$result = 0;

	/* kill any running nfsd */
	killbyname("rpc.statd");
	killbyname("rpc.lockd");
	killbypid("{$g['varrun_path']}/mountd.pid");
	forcekillbyname("nfsd");
	killbyname("rpcbind");

	if (isset($config['nfs']['enable'])) {

		if ($g['booting']) {
			write_console("Starting NFS... ");
		}

		/* If no share configured, exit */
		if (!is_array($config['mounts']['mount'])) {
			$result = 1;
		}

		/* generate exports */
		$fd = fopen("{$g['varetc_path']}/exports", "w");
		if (!$fd) {
			$message = "Error: Can't open exports in services_nfs_configure()";
			write_console($message . ".\n");
			write_log($message);
			$result = 1;
		}
		$a_net = &$config['nfs']['nfsnetworks'];
		$a_mount = &$config['mounts']['mount'];

		foreach ($a_net as $net) {

			list($network,$subnet) = explode('/', $net);

			$subnet=gen_subnet_mask($subnet);

			foreach ($a_mount as $mount) {
				$nfsconf .= <<<EOD
/mnt/{$mount['sharename']} -alldirs
EOD;

				if (strcmp($config['nfs']['mapall'],"yes") == 0 ) 	{

				$nfsconf .= <<<EOD
-mapall=root
EOD;

				} else {
				$nfsconf .= <<<EOD
-maproot=root
EOD;

				}

				$nfsconf .= <<<EOD
-network {$network} -mask $subnet

EOD;

			}
		}
		fwrite($fd, $nfsconf);
		fclose($fd);

		/* run rpcbind, nfsd and mountd if no previous error*/

		if (!$result) {
			$result = mwexec("/usr/sbin/rpcbind");
			$result |= mwexec("/usr/sbin/nfsd -u -t -n 4");
			$result |= mwexec("/usr/sbin/mountd -r {$g['varetc_path']}/exports");
			$result |= mwexec("/usr/sbin/rpc.lockd");
			$result |= mwexec("/usr/sbin/rpc.statd");

			if(!$result) {
				if($g['booting'])
					write_console("done\n");
				write_log("NFS Daemon started");
			} else {
				if($g['booting'])
					write_console("failed\n");
				write_log("Error: Failed to start NFS Daemon");
			}
		}
	}

	return $result;
}

// Start/Stop/Restart pure-ftpd process.
// Return 0 if OK, 1 or error code if error
function services_ftpd_configure() {
	global $config, $g;

	$result = 0;

	/* kill any Pure-FTPD */
	killbypid("{$g['varrun_path']}/pure-ftpd.pid");

	if (isset($config['ftp']['enable'])) {
		if ($g['booting'])
			write_console("Starting FTP server... ");

		/* Generate the banner file */
		$fd = fopen("/mnt/.banner", "w");
		if (!$fd) {
			$message = "Error: Can't open /mnt/.banner in services_pureftpd_configure()";
			write_console($message . ".\n");
			write_log($message);
			$result = 1;
		}

		if (empty($config['ftp']['banner'])) {
			$productname = get_product_name();
			$banner = <<<EOD
Welcome to {$productname} FTP service

EOD;
		} else {
			$banner = <<<EOD
{$config['ftp']['banner']}

EOD;
		}

		fwrite($fd, $banner);
		fclose($fd);

		/* run pure-ftpd if no previous error */
		if (!$result) {
			$cmd = "/usr/local/sbin/pure-ftpd -S {$config['ftp']['port']} -c {$config['ftp']['numberclients']} -I {$config['ftp']['timeout']} -M -U 077:077 -B -b ";

			if (0 < $config['ftp']['maxconperip']) {
				$cmd .= "-C {$config['ftp']['maxconperip']} ";
			}

			if ( (isset($config['ftp']['anonymous'])) && (!isset($config['ftp']['localuser']))) {
				$cmd .= "-e ";
			}

			if ( (!isset($config['ftp']['anonymous'])) && (isset($config['ftp']['localuser']))) {
				$cmd .= "-E ";
			}

			if ($config['ftp']['pasv_min_port'] && $config['ftp']['pasv_max_port']) {
				$cmd .= "-p {$config['ftp']['pasv_min_port']}:{$config['ftp']['pasv_max_port']} ";
			}

			if ($config['ftp']['pasv_address']) {
				$cmd .= "-P {$config['ftp']['pasv_address']} ";
			}

			if (isset($config['ftp']['natmode'])) {
				$cmd .= "-N ";
			}

			if (isset($config['ftp']['fxp'])) {
				$cmd .= "-W ";
			}

			if (isset($config['ftp']['keepallfiles'])) {
				# Enable resume mode.
				$cmd .= "-K ";
			}

			if (!isset($config['ftp']['permitrootlogin'])) {
				# Permit root login.
				$cmd .= "-u 1 ";
			}

			$result = mwexec($cmd);

			if (0 == $result) {
				if($g['booting'])
					write_console("done\n");
				write_log("FTP Daemon started");
			} else {
				if($g['booting'])
					write_console("failed\n");
				write_log("Error: Failed to start FTP Daemon");
			}
		}
  }

	return $result;
}

/* Stop smartd, Generate the configuration file and start smartd
Return 0 if OK, 1 or error code if error */
function services_smart_configure() {
	global $config, $g;

	$result = 0 ;

	/* kill any running smartd */
	killbypid("{$g['varrun_path']}/smartd.pid");

	if (isset($config['system']['smart'])) {
		if ($g['booting'])
			write_console("Starting smartd... ");

		/* run smartd */
		$result = mwexec("/usr/local/sbin/smartd --pidfile={$g['varrun_path']}/smartd.pid --logfacility=local5");

		if(!$result) {
			if($g['booting'])
				write_console("done\n");
			write_log("S.M.A.R.T Daemon started");
		} else {
			if($g['booting'])
				write_console("failed\n");
			write_log("Error: Failed to start S.M.A.R.T Daemon");
		}
	}

	return $result;
}

/* Start/Stop system power control utility
Return 0 if OK, 1 or error code if error */
function services_powerd_configure() {
	global $config, $g;

	$result = 0 ;

	/* kill any running powerd */
	killbypid("{$g['varrun_path']}/powerd.pid");

	if (isset($config['system']['powerd'])) {
		if ($g['booting'])
			write_console("Starting powerd... ");

		/* run powerd */
		$result = mwexec("/usr/sbin/powerd -a adaptive -b adaptive");

		if (!$result) {
			if ($g['booting'])
				write_console("done\n");
			write_log("Power Daemon started");
		} else {
			if ($g['booting'])
				write_console("failed\n");
			write_log("Error: Failed to start Power Daemon");
		}
	}

	return $result;
}

/* Stop sshd, Generate the configuration file and start it
Return 0 if OK, 1 or error code if error */
function services_sshd_configure() {
	global $config, $g;

	$result = 0 ;

	/* kill any SSHD */
	sigkillbypid("{$g['varrun_path']}/sshd.pid", "TERM");

	if (isset($config['sshd']['enable'])) {

		if ($g['booting'])
			write_console("Starting SSH server... ");

		/* generate /ssh/sshd_config */
		$fd = fopen("{$g['varetc_path']}/ssh/sshd_config", "w");
		if (!$fd) {
			$message = "Error: Can't open /ssh/sshd_config in services_sshd_configure()";
			write_console($message . ".\n");
			write_log($message);
			$result = 1 ;
		}

		$sshconf = <<<EOD
SyslogFacility LOCAL3
Protocol 2
UseDNS no
PasswordAuthentication yes
PubkeyAuthentication yes
Subsystem       sftp    /usr/libexec/sftp-server

EOD;

	if (isset($config['sshd']['port'])) {
		$sshconf .= <<<EOD
Port {$config['sshd']['port']}

EOD;
	}

  if (isset($config['sshd']['permitrootlogin'])) {
		$sshconf .= <<<EOD
PermitRootLogin yes

EOD;
	}
if (isset($config['sshd']['tcpforwarding'])) {
		$sshconf .= <<<EOD
AllowTcpForwarding yes

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
			$message = "Error: Can't open /ssh/ssh_host_dsa_key in service_sshd_configure()";
			write_console($message . ".\n");
			write_log($message);
			$result = 1 ;
		}

		fwrite($fd, $key);
		fclose($fd);

		/* Restrict right on the SSH key */
		mwexec("/bin/chmod 0600 {$g['varetc_path']}/ssh/ssh_host_dsa_key");

		/* run sshd if no previous error*/
		if (!$result) {
			$result = mwexec("/usr/sbin/sshd -f {$g['varetc_path']}/ssh/sshd_config -h {$g['varetc_path']}/ssh/ssh_host_dsa_key");
		}

		if(!$result) {
			if($g['booting'])
				write_console("done\n");
			write_log("SSH Daemon started");
		} else {
			if($g['booting'])
				write_console("failed\n");
			write_log("Error: Failed to start SSH Daemon");
		}
	}

	return $result;
}

/* Generate the unison configuration
Return 0 if OK, 1 or error code if error */
function services_unison_configure() {
	global $config, $g;

	$result = 0 ;

	if (isset($config['unison']['enable'])) {
		if ($g['booting'])
			write_console("Configuring unison... ");

		/* ensure unison workdir exists */
		$workdir = "/mnt/{$config['unison']['share']}/{$config['unison']['workdir']}";
		if (isset($config['unison']['makedir']) && !file_exists($workdir)) {
			if (!mkdir($workdir)) {
				$message = "Error: Can't mkdir '{$workdir}' in services_unison_configure()";
				write_console($message . ".\n");
				write_log($message);
			}
			mwexec("/bin/chmod 0775 '{$workdir}'");
		}
		if (!file_exists($workdir)) {
			$message = "Error: Can't open '{$workdir}' in services_unison_configure()";
			write_console($message . ".\n");
			write_log($message);
			$result = 1 ;
		}

		/* add workdir to login.conf UNISON environment variable */
		$logfile = "{$g['etc_path']}/login.conf";
		if (!file_exists($logfile)) {
			$message = "Error: '{$logfile}' does not exist, in services_unison_configure()";
			write_console($message . ".\n");
			write_log($message);
			$result = 1 ;
		}
		$tmp = file_get_contents($logfile);
		$search = "/UNISON=.*?(?=[,:])/";
		if (!preg_match($search, $tmp)) {
			$message = "Error: Can't find UNISON in '{$logfile}', in services_unison_configure()";
			write_console($message . ".\n");
			write_log($message);
			$result = 1 ;
		}
		$tmp = preg_replace($search, "UNISON={$workdir}", $tmp);
		if (!file_put_contents($logfile, $tmp)) {
			$message = "Error: Couldn't write '{$logfile}', in services_unison_configure()";
			write_console($message . ".\n");
			write_log($message);
			$result = 1 ;
		}
		$tmp = "";

		if(!$result) {
			if($g['booting'])
				write_console("done\n");
			write_log("Unison started");
		} else {
			if($g['booting'])
				write_console("failed\n");
			write_log("Error: Failed to start Unison");
		}
	}

	return $result;
}

/* Stop and start UPnP server
Return 0 if OK, 1 or error code if error */
function services_upnp_configure() {
	global $config, $g;

	$result = 0;

	/* Kill any running uShare */
	killbyname("ushare");

	/* Delete multicast route. */
	mwexec("/sbin/route delete -net 239.0.0.0");

	if(isset($config['upnp']['enable'])) {
		if($g['booting'])
			write_console("Starting UPnP server... ");

		$fd = fopen("{$g['varetc_path']}/ushare.conf", "w");
		if (!$fd) {
			$message = "Error: Can't open ushare.conf in services_upnp_configure()";
			write_console($message . ".\n");
			write_log($message);
			return 1;
		}

		/* Setup a multicast route for UPnP messages. */
		$result = mwexec("/sbin/route add -net 239.0.0.0 -netmask 255.0.0.0 -interface " . escapeshellarg($config['upnp']['if']));

		$ushareconf = <<<EOD
USHARE_NAME={$config['upnp']['name']}
USHARE_IFACE={$config['upnp']['if']}
USHARE_OVERRIDE_ICONV_ERR=YES

EOD;

		if ($config['upnp']['port']) {
$ushareconf .= <<<EOD
USHARE_PORT={$config['upnp']['port']}

EOD;
		}

		$content = "";
		foreach($config['upnp']['content'] as $contentv) {
			$content .= "\"$contentv\" ";
		}

		$ushareconf .= <<<EOD
USHARE_DIR={$content}

EOD;

		fwrite($fd, $ushareconf);
		fclose($fd);

		$options = "--daemon --xbox";
		if (!isset($config['upnp']['web'])) {
			$options .= " --no-web";
		}

		$result = mwexec("/usr/local/bin/ushare {$options} --cfg={$g['varetc_path']}/ushare.conf");

		if(!$result) {
			if($g['booting'])
				write_console("done\n");
			write_log("UPnP Server started");
		} else {
			if($g['booting'])
				write_console("failed\n");
			write_log("Error: Failed to start UPnP Server");
		}
	}

	return $result;
}

/* Stop and start Dynamic DNS client
Return 0 if OK, 1 or error code if error */
function services_dynamicdns_configure() {
	global $config, $g;

	$result = 0;

	/* Kill any running inadyn */
	killbyname("inadyn");

	if(isset($config['dynamicdns']['enable'])) {
		if($g['booting'])
			write_console("Starting Dynamic DNS service... ");

		$fd = fopen("{$g['varetc_path']}/inadyn.conf", "w");
		if (!$fd) {
			$message = "Error: Can't open inadyn.conf in services_dynamicdns_configure()";
			write_console($message . ".\n");
			write_log($message);
			return 1;
		}

		/* Get the service type */
		$servicetype = "";
		switch($config['dynamicdns']['provider']) {
			case "dyndns.org":					$servicetype = "dyndns@dyndns.org";
																	break;
			case "freedns.afraid.org":	$servicetype = "default@freedns.afraid.org";
																	break;
			case "zoneedit.com":				$servicetype = "default@zoneedit.com";
																	break;
			case "no-ip.com":						$servicetype = "default@no-ip.com";
																	break;
		}

		/* Set command parameters. */
		$inadynconf = <<<EOD
--background
--syslog
--dyndns_system {$servicetype}
--alias {$config['dynamicdns']['domainname']}
--username "{$config['dynamicdns']['username']}"
--password "{$config['dynamicdns']['password']}"

EOD;

		/* Set additional command parameters. */
		if ($config['dynamicdns']['updateperiod']) {
			$inadynconf .= <<<EOD
--update_period_sec {$config['dynamicdns']['updateperiod']}

EOD;
		}

		if ($config['dynamicdns']['forcedupdateperiod']) {
			$inadynconf .= <<<EOD
--forced_update_period {$config['dynamicdns']['forcedupdateperiod']}

EOD;
		}

		fwrite($fd, $inadynconf);
		fclose($fd);

		/* Run inadyn. */
		$result = mwexec("/usr/local/bin/inadyn --input_file {$g['varetc_path']}/inadyn.conf");

		if(!$result) {
			if($g['booting'])
				write_console("done\n");
			write_log("Dynamic DNS service started");
		} else {
			if($g['booting'])
				write_console("failed\n");
			write_log("Error: Failed to start Dynamic DNS service");
		}
	}

	return $result;
}

function services_cron_configure() {
	global $config, $g;

	$result = 0;

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
		$message = "Error: Can't open crontab in services_cron_configure()";
		write_console($message . ".\n");
		write_log($message);
		return 1;
	}

	// RSYNC CLIENT PART
	if (is_array($config['rsync']['rsyncclient'])) {
		$a_rsyncclient = &$config['rsync']['rsyncclient'];

		if ($g['booting'])
			write_console("Configuring cron for rsync client... ");

		$i=0;
		foreach($a_rsyncclient as $rsyncclient) {

			if ($rsyncclient['all_mins'] == "0") 	{
				foreach ($rsyncclient['minute'] as $minutev) 	{
					if (strlen($cron_min) > 0)
						$cron_min = $cron_min . "," . $minutev;
					else
						$cron_min = $minutev;
				}
			}
			else
				$cron_min = "*";


			if ($rsyncclient['all_hours'] == "0") {
				foreach ($rsyncclient['hour'] as $hourv) 	{
					if (strlen($cron_hour) > 0)
						 $cron_hour = $cron_hour . "," . $hourv;
					else
						 $cron_hour = $hourv;
				}
			}
			else
				$cron_hour = "*";

			if ($rsyncclient['all_days'] == "0") 	{
				foreach ($rsyncclient['day'] as $dayv) 	{
					if (strlen($cron_day) > 0)
						$cron_day = $cron_day . "," . $dayv;
					else
						$cron_day = $dayv;
				}
			}
			else
				$cron_day = "*";

			if ($rsyncclient['all_months'] == "0") 	{
			  foreach ($rsyncclient['month'] as $monthv) 	  {
				if (strlen($cron_month) > 0)
					$cron_month = $cron_month . "," . $monthv;
				else
					$cron_month = $monthv;
			  }
			}
			else
				$cron_month = "*";


			if ($rsyncclient['all_weekdays'] == "0") 	{
				foreach ($rsyncclient['weekday'] as $weekdayv) 	{
					if (strlen($cron_weekday) > 0)
						 $cron_weekday = $cron_weekday . "," . $weekdayv;
					else
						 $cron_weekday = $weekdayv;
				}
			}
			else
				$cron_weekday = "*";

			$crontabconf .= <<<EOD
{$cron_min}    {$cron_hour}        {$cron_day}        {$cron_month}        {$cron_weekday}       root    {$g['varrun_path']}/rsync_client{$i}.sh

EOD;

			// Erase all variable used previously
			unset ($cron_min, $cron_hour, $cron_day, $cron_month, $cron_weekday);
			$i++;
		}

		if ($g['booting'])
			write_console("done\n");

	}

	// RSYNC LOCAL PART
	if (is_array($config['rsync']['rsynclocal'])) {
		$a_rsynclocal = &$config['rsync']['rsynclocal'];

		if ($g['booting'])
			write_console("Configuring cron for local rsync... ");

		$i=0;
		foreach($a_rsynclocal as $rsynclocal) {

			if ($rsynclocal['all_mins'] == "0") 	{
				foreach ($rsynclocal['minute'] as $minutev) 	{
					if (strlen($cron_min) > 0)
						$cron_min = $cron_min . "," . $minutev;
					else
						$cron_min = $minutev;
				}
			}
			else
				$cron_min = "*";


			if ($rsynclocal['all_hours'] == "0") {
				foreach ($rsynclocal['hour'] as $hourv) 	{
					if (strlen($cron_hour) > 0)
						 $cron_hour = $cron_hour . "," . $hourv;
					else
						 $cron_hour = $hourv;
				}
			}
			else
				$cron_hour = "*";

			if ($rsynclocal['all_days'] == "0") 	{
				foreach ($rsynclocal['day'] as $dayv) 	{
					if (strlen($cron_day) > 0)
						$cron_day = $cron_day . "," . $dayv;
					else
						$cron_day = $dayv;
				}
			}
			else
				$cron_day = "*";

			if ($rsynclocal['all_months'] == "0") 	{
			  foreach ($rsynclocal['month'] as $monthv) 	  {
				if (strlen($cron_month) > 0)
					$cron_month = $cron_month . "," . $monthv;
				else
					$cron_month = $monthv;
			  }
			}
			else
				$cron_month = "*";


			if ($rsynclocal['all_weekdays'] == "0") 	{
				foreach ($rsynclocal['weekday'] as $weekdayv) 	{
					if (strlen($cron_weekday) > 0)
						 $cron_weekday = $cron_weekday . "," . $weekdayv;
					else
						 $cron_weekday = $weekdayv;
				}
			}
			else
				$cron_weekday = "*";

			$crontabconf .= <<<EOD
{$cron_min}    {$cron_hour}        {$cron_day}        {$cron_month}        {$cron_weekday}       root    {$g['varrun_path']}/rsync_local{$i}.sh

EOD;

			// Erase all variable used previously
			unset ($cron_min, $cron_hour, $cron_day, $cron_month, $cron_weekday);
			$i++;

		}

		if ($g['booting'])
			write_console("done\n");
	}

	// SHUTDOWN PART
	if (isset($config['shutdown']['enable'])) {
		if ($g['booting'])
			write_console("Configuring Cron for shutdown... ");

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


		if ($config['shutdown']['all_weekdays'] == "0") 	{
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
			write_console("done\n");
	}

	fwrite($fd, $crontabconf);
	fclose($fd);

	/* run cron */
	$result = mwexec("/usr/sbin/cron -s");

	if (!$result) {
		write_log("Cron started");
	} else {
		write_log("Error: Failed to start Cron");
	}

	return $result;
}

function services_rsyncclient_configure()
{
	global $config, $g;

	// Generate a shell script that is used by cron
	// Generate crontab works
	if (is_array($config['rsync']['rsyncclient'])) {
		$a_rsyncclient = &$config['rsync']['rsyncclient'];

		if ($g['booting'])
			write_console("Generating synchronizing cron script... ");

		$i=0;
		foreach($a_rsyncclient as $rsyncclient) {

			/* generate /var/run/rsync_client'ID'.sh script */
			$fd = fopen("{$g['varrun_path']}/rsync_client{$i}.sh", "w");
			if (!$fd) {
				$message = "Error: Can't open rsync_client{$i}.sh in services_rsyncclient_configure()";
				write_console($message . ".\n");
				write_log($message);
				return 1;
			}

			$syncscript = <<<EOD
#!/bin/sh
/usr/bin/logger -p local4.notice "Remote RSYNC synchronization from share {$rsyncclient['remoteshare']} on {$rsyncclient['rsyncserverip']} to {$rsyncclient['localshare']}"
if [ -r {$g['varrun_path']}/rsync_client-running{$i} ]; then
/usr/bin/logger -p local4.notice "Previous client synchronization still running...exiting"
exit
fi
/usr/bin/touch {$g['varrun_path']}/rsync_client-running{$i}

EOD;

			$opt_delete="";
			if (isset($rsyncclient['opt_delete'])) {
				$opt_delete = "--delete --delete-after ";
			}

			$syncscript .= <<<EOD
/usr/local/bin/rsync -rtz {$opt_delete}rsync://{$rsyncclient['rsyncserverip']}/{$rsyncclient['remoteshare']} /mnt/{$rsyncclient['localshare']}

EOD;

			$syncscript .= <<<EOD
/bin/rm -f {$g['varrun_path']}/rsync_client-running{$i}
/usr/bin/logger -p local4.notice "End of remote RSYNC synchronization from share {$rsyncclient['remoteshare']} on {$rsyncclient['rsyncserverip']} to {$rsyncclient['localshare']}"

EOD;

			fwrite($fd,$syncscript);
			fclose($fd);

			/* Add exectutive right to the script */
			mwexec("/bin/chmod 0770 {$g['varrun_path']}/rsync_client{$i}.sh");

			$i++;
		}

		if ($g['booting'])
			write_console("done\n");

		return 0;
	}

	return 0;
}

function services_rsync_local_configure()
{
	global $config, $g;

	// Generate a shell script that is used by cron
	// Generate crontab works
	if (is_array($config['rsync']['rsynclocal'])) {
		$a_rsynclocal = &$config['rsync']['rsynclocal'];

		if ($g['booting'])
			write_console("Generating local rsync cron script... ");

		$i=0;
		foreach($a_rsynclocal as $rsynclocal) {
			/* generate /var/run/sync_local.sh script */
			$fd = fopen("{$g['varrun_path']}/rsync_local{$i}.sh", "w");
			if (!$fd) {
				$message = "Error: Can't open /var/run/rsync_local{$i}.sh in services_rsync_local_configure()";
				write_console($message . ".\n");
				write_log($message);
				return 1;
			}

			$syncscript = <<<EOD
#!/bin/sh
/usr/bin/logger -p local4.notice "Start of local RSYNC from {$rsynclocal['source']} to {$rsynclocal['destination']}"
if [ -r {$g['varrun_path']}/rsync_local-running{$i} ]; then
/usr/bin/logger -p local4.notice "Previous local synchronization still running...exiting"
exit
fi
/usr/bin/touch {$g['varrun_path']}/rsync_local-running{$i}

EOD;

			$opt_delete="";
			if (isset($rsynclocal['opt_delete']))
				$opt_delete = "--delete --delete-after ";

			$syncscript .= <<<EOD
/usr/local/bin/rsync -a {$opt_delete} /mnt/{$rsynclocal['source']} /mnt/{$rsynclocal['destination']}

EOD;

			$syncscript .= <<<EOD
/bin/rm -f {$g['varrun_path']}/rsync_local-running{$i}
/usr/bin/logger -p local4.notice "End of local RSYNC synchronization from {$rsynclocal['source']} to {$rsynclocal['destination']}"

EOD;

			fwrite($fd,$syncscript);
			fclose($fd);

			/* Add exectutive right to the script */
			mwexec("/bin/chmod 0770 {$g['varrun_path']}/rsync_local{$i}.sh");

			$i++;
		}

		if ($g['booting'])
			write_console("done\n");

		return 0;
	}

	return 0;
}

function services_radius_configure()
{
	global $config, $g;

	if (isset($config['radius']['enable'])) {

		/* generate radius.conf */
		$fd = fopen("{$g['varetc_path']}/radius.conf", "w");
		if (!$fd) {
			$message = "Error: Can't open radius.conf in services_radius_configure()";
			write_console($message . ".\n");
			write_log($message);
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

/* Stop, Generate the config file and start AFP
Return 0 if OK, 1 or error code if error */
function services_afpd_configure()
{
	global $config, $g;

	$result = 0 ;

	/* kill any VSFTPD */
	killbyname("afpd");

	if (isset($config['afp']['enable'])) 	{

		/* If no share configured, there is a problem */
		if (!is_array($config['mounts']['mount']))
			$result = 1 ;

		if ($g['booting'])
			write_console("Starting AFP server... ");

		if (empty($config['afp']['afpname']))
			$afpname="-";
		else
			$afpname=$config['afp']['afpname'];

		/* generate afpd.conf */
		$fd = fopen("{$g['varetc_path']}/afpd.conf", "w");
		if (!$fd) {
			$message = "Error: Can't open afpd.conf in services_afpd_configure()";
			write_console($message . ".\n");
			write_log($message);
			$result = 1 ;
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
		if (!$fd) 	{
			$message = "Error: Can't open AppleVolumes.default in services_afpd_configure()";
			write_console($message . ".\n");
			write_log($message);
			$result = 1 ;
		}

		$a_mount = &$config['mounts']['mount'];
		foreach ($a_mount as $mount) {
			$AppleVolumes .= <<<EOD
/mnt/{$mount['sharename']} "{$mount['sharename']}"

EOD;
		}

		fwrite($fd, $AppleVolumes);
		fclose($fd);

		/* run afpdpd if no previous error*/
		if (!$result) {
			$result = mwexec("/usr/local/sbin/afpd -F {$g['varetc_path']}/afpd.conf");
		}

		if(!$result) {
			if($g['booting'])
				write_console("done\n");
			write_log("AFP Daemon started");
		} else {
			if($g['booting'])
				write_console("failed\n");
			write_log("Error: Failed to start AFP Daemon");
		}
	}

	return $result;
}

function services_nis_configure()
{
	global $config, $g;

	return 0;
}

/* Start iscsi initiator
Return 0 if Ok, 1 or error code if problem */

function services_iscsiinit_configure()
{
	global $config, $g;

	/* kill any running iscontrol */
	killbyname("iscontrol");

	if (is_array($config['iscsiinit']['vdisk'])) {
		if ($g['booting']) {
			write_console("Loading iSCSI kernel module... ");
		}
		/* iSCSI initiator configured, start the process */
		$result = mwexec("/sbin/kldload iscsi_initiator.ko");
		$result |= mwexec("/sbin/sysctl debug.iscsi=0");

		if ((!$result) && ($g['booting'])) {
			write_log("iSCSI kernel module loaded");
			write_console("done\n");
		} else if ($g['booting']) {
			write_log("Error: Failed to load iSCSI kernel module");
			write_console("failed\n");
		}
		if ($g['booting']) {
			write_console("Configuring initiator(s)... ");
		}

		$a_iscsiinit = &$config['iscsiinit']['vdisk'];

		if (!$result) {
			foreach ($a_iscsiinit as $iscsiinit) {
				/* run iscontrol if no previous error*/
				$result |= mwexec("/usr/local/sbin/iscontrol targetaddress={$iscsiinit['targetaddress']}  targetname={$iscsiinit['targetname']} initiatorname={$iscsiinit['initiatorname']}");
			}
		}

		if(!$result) {
			if($g['booting'])
				write_console("done\n");
			write_log("iSCSI initiator(s) started");
		} else {
			if($g['booting'])
				write_console("failed\n");
			write_log("Error: Failed to start iSCSI initiator(s)");
		}
	}

	return $result;
}

/* Start iscsi target
Return 0 if Ok, 1 or error code if problem */
function services_iscsitarget_configure()
{
	global $config, $g;

	$result = 0 ;

	/* kill any running iscsi-target */
	sigkillbypid("{$g['varrun_path']}/iscsi-target.pid", "TERM");

	if (is_array($config['iscsitarget']['vdisk'])) {
		if ($g['booting']) {
			write_console("Loading iSCSI target... ");
		}

		$a_iscsitarget = &$config['iscsitarget']['vdisk'];

		/* generate targets */
		$fd = fopen("{$g['varetc_path']}/iscsi/targets", "w");
		if (!$fd) {
			$message = "Error: Can't open iscsi/targets in services_iscsitarget_configure()";
			write_console($message . ".\n");
			write_log($message);
			$result = 1 ;
		}
		$i = 0;
		foreach ($a_iscsitarget as $target) {
			$targetsfile .= <<<EOD
extent{$i} /mnt/{$target['sharename']}/iscsi_target{$i} 0 {$target['size']}MB
target{$i} rw extent{$i} {$target['network']}

EOD;
			$i++;
		}

		fwrite($fd, $targetsfile);
		fclose($fd);

		/* run iscsi-target if no previous error*/

		if (!$result) {
			$result = mwexec("/usr/local/bin/iscsi-target");
		}

		if(!$result) {
			if($g['booting'])
				write_console("done\n");
			write_log("iSCSI-target Daemon started");
		} else {
			if($g['booting'])
				write_console("failed\n");
			write_log("Error: Failed to start iSCSI-target Daemon");
		}
	}
}

/* This special function is used by system_stop_common()
It force to stop iSCSI-target before to stop swap */
function services_iscsitarget_stop()
{
	/* kill any running iscsi-target */
	sigkillbypid("{$g['varrun_path']}/iscsi-target.pid", "TERM");
}

/* Stop, generate config and start mdnsresponder
Return 0 if Ok, 1 or error code if problem */
function services_mdnsresponder_configure()
{
	global $config, $g;

	$result = 0 ;

	/* kill any running snmpd */
	sigkillbypid("{$g['varrun_path']}/mDNSResponder.pid", "TERM");

	if (isset($config['system']['zeroconf'])) {
		if ($g['booting']) {
			write_console("Starting Bonjour Zeroconf service... ");
		}

		$fd = fopen("{$g['varetc_path']}/mDNSResponder.conf", "w");
		if (!$fd) 	{
			$message = "Error: Can't open mDNSResponder.conf in services_bonjour_configure()";
			write_console($message . ".\n");
			write_log($message);
			$result = 1 ;
		}

		// Check if WebGUI use HTTPS
		if ($config['system']['webgui']['protocol'] == "https") {
			if ($config['system']['webgui']['port']) 	{
				$mDNSResponder = <<<EOD
# mDNSResponderPosix configuration File
#
# This file contains the services to be announced to other Rendezvous aware
# machines on the .local network
#
# Entries constist of four consecutive lines as follows:
# NAME OF SERVICE
# TYPE OF SERVICE (and, if different from .local, the domain seperated by a space)
# PORT OF SERVICE
# TXT RECORD (short description of service)
#
# For types: http://www.dns-sd.org/ServiceTypes.html

{$config['system']['hostname']} WebAdmin Service
_https._tcp. local.
{$config['system']['webgui']['port']}
path=/index.php

EOD;
			} else 	{
			$mDNSResponder = <<<EOD
{$config['system']['hostname']} WebAdmin Service
_https._tcp. local.
443
path=/index.php

EOD;
			}
			// if WebGUI don't use HTTPS then:
			} else {
				if ($config['system']['webgui']['port'])	{
					$mDNSResponder = <<<EOD
{$config['system']['hostname']} WebAdmin Service
_http._tcp. local.
{$config['system']['webgui']['port']}
path=/index.php

EOD;
				} 	else 	{
					$mDNSResponder = <<<EOD
{$config['system']['hostname']} WebAdmin Service
_http._tcp. local.
80
path=/index.php

EOD;
				}
			}

		// Check if AFP service is enabled
		if (isset($config['afp']['enable'])) 	{
			$mDNSResponder .= <<<EOD

{$config['system']['hostname']} AFP Service
_afpovertcp._tcp. local.
548

EOD;
		}

		// Check if RSYNC service is enabled
		if (isset($config['rsyncd']['enable'])) 	{
			$mDNSResponder .= <<<EOD

{$config['system']['hostname']} RSYNC Service
_rsync._tcp. local.
{$config['rsyncd']['port']}

EOD;
		}

		// Check if SSH service is enabled
		if (isset($config['sshd']['enable'])) 	{
			$mDNSResponder .= <<<EOD

{$config['system']['hostname']} SSH Service
_ssh._tcp. local.
{$config['sshd']['port']}

EOD;
		}

		// Check if FTP service is enabled
		if (isset($config['ftp']['enable'])) 	{
			$mDNSResponder .= <<<EOD

{$config['system']['hostname']} FTP Service
_ftp._tcp. local.
{$config['ftp']['port']}

EOD;
		}

		// Check if Samba service is enabled
		if (isset($config['samba']['enable'])) {
			$mDNSResponder .= <<<EOD

{$config['system']['hostname']} SMB Service
_smb._tcp. local.
139

EOD;
		}

		// Check if NFS service is enabled
		if (isset($config['nfs']['enable'])) {
			/* If no share configured, exit */
			if (!is_array($config['mounts']['mount'])) {
				break;
			}

			$a_mount = &$config['mounts']['mount'];

			foreach ($a_mount as $mount) {
				$mDNSResponder .= <<<EOD

{$config['system']['hostname']} NFS Service
_nfs._tcp. local.
2049
path=/mnt/{$mount['sharename']}

EOD;
			}
		}

		// Check if UPnP service is enabled
		if (isset($config['upnp']['enable'])) {
			$mDNSResponder .= <<<EOD

{$config['system']['hostname']} UPnP Service
_upnp._tcp. local.
1900

EOD;
		}

		fwrite($fd, $mDNSResponder);
		fclose($fd);

		/* run mDNSResponder if no error*/
		if (!$result) {
			$result = mwexec("/usr/local/sbin/mDNSResponderPosix -b -f {$g['varetc_path']}/mDNSResponder.conf");
		}

		if(!$result) {
			if($g['booting'])
				write_console("done\n");
			write_log("ZeronConf service started");
		} else {
			if($g['booting'])
				write_console("failed\n");
			write_log("Error: Failed to start ZeronConf service");
		}
	}

	return $result;
}
?>
