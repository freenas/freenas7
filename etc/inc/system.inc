<?php
/*
	system.inc
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
	
function system_resolvconf_generate($dynupdate = false) {
	global $config, $g;
	
	$syscfg = $config['system'];
	
	$fd = fopen("{$g['varetc_path']}/resolv.conf", "w");
	if (!$fd) {
		printf("Error: cannot open resolv.conf in system_resolvconf_generate().\n");
		return 1;
	}
		
	$resolvconf = "domain {$syscfg['domain']}\n";
	
	$havedns = false;
	
	if (isset($syscfg['dnsallowoverride'])) {
		/* get dynamically assigned DNS servers (if any) */
		$nfd = @fopen("{$g['varetc_path']}/nameservers.conf", "r");
		if ($nfd) {
			while (!feof($nfd)) {
				$dnss = trim(fgets($nfd));
				if ($dnss) {
					$resolvconf .= "nameserver $dnss\n";
					$havedns = true;
				}
			}
			fclose($nfd);
		}
	}
	if (!$havedns && is_array($syscfg['dnsserver'])) {
		foreach ($syscfg['dnsserver'] as $ns) {
			if ($ns)
				$resolvconf .= "nameserver $ns\n";
			$havedns = true;
		}
	}
		
	fwrite($fd, $resolvconf);
	fclose($fd);
		
	return 0;
}

function system_hosts_generate() {
	global $config, $g;
	
	$syscfg = $config['system'];
	$lancfg = $config['interfaces']['lan'];
	$dnsmasqcfg = $config['dnsmasq'];

	if (!is_array($dnsmasqcfg['hosts'])) {
		$dnsmasqcfg['hosts'] = array();
	}
	$hostscfg = $dnsmasqcfg['hosts'];
	
	$fd = fopen("{$g['varetc_path']}/hosts", "w");
	if (!$fd) {
		printf("Error: cannot open hosts file in system_hosts_generate().\n");
		return 1;
	}
		
	$hosts = <<<EOD
127.0.0.1	localhost localhost.{$syscfg['domain']}
{$lancfg['ipaddr']}	{$syscfg['hostname']}.{$syscfg['domain']} {$syscfg['hostname']}

EOD;
	
	foreach ($hostscfg as $host) {
		if ($host['host'])
			$hosts .= "{$host['ip']}	{$host['host']}.{$host['domain']} {$host['host']}\n";
		else
			$hosts .= "{$host['ip']}	{$host['domain']}\n";
	}
	fwrite($fd, $hosts);
	fclose($fd);
	
	return 0;
}

function system_hostname_configure() {
	global $config, $g;
	
	$syscfg = $config['system'];
	
	/* set hostname */
	return mwexec("/bin/hostname " .
		escapeshellarg("{$syscfg['hostname']}.{$syscfg['domain']}"));
}

function system_routing_configure() {
	global $config, $g;
	
	/* clear out old routes, if necessary */
	if (file_exists("{$g['vardb_path']}/routes.db")) {
		$fd = fopen("{$g['vardb_path']}/routes.db", "r");
		if (!$fd) {
			printf("Error: cannot open routes DB file in system_routing_configure().\n");
			return 1;		
		}
		while (!feof($fd)) {
			$oldrt = fgets($fd);
			if ($oldrt)
				mwexec("/sbin/route delete " . escapeshellarg($oldrt));
		}
		fclose($fd);
		unlink("{$g['vardb_path']}/routes.db");
	}
	
	if (is_array($config['staticroutes']['route'])) {
		
		$fd = fopen("{$g['vardb_path']}/routes.db", "w");
		if (!$fd) {
			printf("Error: cannot open routes DB file in system_routing_configure().\n");
			return 1;		
		}
		
		foreach ($config['staticroutes']['route'] as $rtent) {
			mwexec("/sbin/route add " . escapeshellarg($rtent['network']) . 
				" " . escapeshellarg($rtent['gateway']));
			
			/* record route so it can be easily removed later (if necessary) */
			fwrite($fd, $rtent['network'] . "\n");
		}
		
		fclose($fd); 
	}
	
	return 0;
}

function system_syslogd_start() {
	global $config, $g;
	
	$syslogcfg = $config['syslog'];

	if ($g['booting']) 
		echo "Starting syslog service... ";
	else
		killbypid("{$g['varrun_path']}/syslog.pid");
			
	if (isset($syslogcfg['enable']))
	{

		/* write syslog.conf */
		$fd = fopen("{$g['varetc_path']}/syslog.conf", "w");
		if (!$fd) {
			printf("Error: cannot open syslog.conf in system_syslogd_start().\n");
			return 1;
		}
		
		$syslogconf = <<<EOD
local3.*					%/var/log/sshd.log
local4.*					%/var/log/rsyncd.log
local5.*                    %/var/log/smartd.log
ftp.*   					%/var/log/ftp.log
daemon.*   					%/var/log/daemon.log
*.notice;kern.debug;lpr.info;mail.crit;news.err;local0.none;local7.none %/var/log/system.log
security.*					%/var/log/system.log
auth.info;authpriv.info		%/var/log/system.log
*.emerg						*

EOD;

		
		if (isset($syslogcfg['ftp'])) {
			$syslogconf .= <<<EOD
ftp.*					@{$syslogcfg['remoteserver']}

EOD;
		}
		
		if (isset($syslogcfg['sshd'])) {
			$syslogconf .= <<<EOD
local3.*					@{$syslogcfg['remoteserver']}

EOD;
		}
		
		if (isset($syslogcfg['rsyncd'])) {
			$syslogconf .= <<<EOD
local4.*					@{$syslogcfg['remoteserver']}

EOD;
		}
		
		if (isset($syslogcfg['smartd'])) {
			$syslogconf .= <<<EOD
local5.*					@{$syslogcfg['remoteserver']}

EOD;
		}
		
		if (isset($syslogcfg['daemon'])) {
			$syslogconf .= <<<EOD
daemon.*  					@{$syslogcfg['remoteserver']}

EOD;
		}
		
		
		if (isset($syslogcfg['system'])) {
			$syslogconf .= <<<EOD
*.notice;kern.debug;lpr.info;mail.crit;news.err;local0.none;local7.none @{$syslogcfg['remoteserver']}
security.*					@{$syslogcfg['remoteserver']}
auth.info;authpriv.info		@{$syslogcfg['remoteserver']}
*.emerg						@{$syslogcfg['remoteserver']}

EOD;
		}

		fwrite($fd, $syslogconf);
		fclose($fd);
		
		$retval = mwexec("/usr/sbin/syslogd -s -f {$g['varetc_path']}/syslog.conf");

	} else {
		$retval = mwexec("/usr/sbin/syslogd -ss");
	}
	
	if ($g['booting'])
		echo "done\n";
		
	return $retval;
}

function system_pccard_start() {
	global $config, $g;
	
	if ($g['booting'])
		echo "Initializing PC cards... ";
	
	/* kill any running pccardd */
	killbypid("{$g['varrun_path']}/pccardd.pid");
	
	/* fire up pccardd */
	$res = mwexec("/usr/sbin/pccardd -z -f {$g['etc_path']}/pccard.conf");
	
	if ($g['booting']) {
		if ($res == 0)
			echo "done\n";
		else
			echo "failed (probably no PC card controller present)\n";
	}
		
	return $res;
}

function system_webgui_start() {
	global $config, $g;
	
	if ($g['booting'])
		echo "Starting webGUI... ";
	
	/* kill any running lighttpd */
	killbypid("{$g['varrun_path']}/lighttpd.pid");
	
	/* generate password file */
	system_password_configure();
	
	chdir($g['www_path']);
	
	/* generate lightppd.conf */
	$fd = fopen("{$g['varetc_path']}/lighttpd.conf", "w");
	if (!$fd) {
		printf("Error: cannot open lightppd.conf in system_webgui_start().\n");
		return 1;
	}
	
	$lighttpdconf = <<<EOD
server.modules              = (
                                "mod_access",
                                "mod_auth",
                                "mod_cgi" )
#                               "mod_accesslog" )
server.document-root        = "{$g['www_path']}"
server.errorlog             = "{$g['varlog_path']}/lighttpd.error.log" 
index-file.names            = ( "index.php" )
mimetype.assign             = (
  ".pdf"          =>      "application/pdf",
  ".sig"          =>      "application/pgp-signature",
  ".spl"          =>      "application/futuresplash",
  ".class"        =>      "application/octet-stream",
  ".ps"           =>      "application/postscript",
  ".torrent"      =>      "application/x-bittorrent",
  ".dvi"          =>      "application/x-dvi",
  ".gz"           =>      "application/x-gzip",
  ".pac"          =>      "application/x-ns-proxy-autoconfig",
  ".swf"          =>      "application/x-shockwave-flash",
  ".tar.gz"       =>      "application/x-tgz",
  ".tgz"          =>      "application/x-tgz",
  ".tar"          =>      "application/x-tar",
  ".zip"          =>      "application/zip",
  ".mp3"          =>      "audio/mpeg",
  ".m3u"          =>      "audio/x-mpegurl",
  ".wma"          =>      "audio/x-ms-wma",
  ".wax"          =>      "audio/x-ms-wax",
  ".ogg"          =>      "application/ogg",
  ".wav"          =>      "audio/x-wav",
  ".gif"          =>      "image/gif",
  ".jpg"          =>      "image/jpeg",
  ".jpeg"         =>      "image/jpeg",
  ".png"          =>      "image/png",
  ".xbm"          =>      "image/x-xbitmap",
  ".xpm"          =>      "image/x-xpixmap",
  ".xwd"          =>      "image/x-xwindowdump",
  ".css"          =>      "text/css",
  ".html"         =>      "text/html",
  ".htm"          =>      "text/html",
  ".js"           =>      "text/javascript",
  ".asc"          =>      "text/plain",
  ".c"            =>      "text/plain",
  ".cpp"          =>      "text/plain",
  ".log"          =>      "text/plain",
  ".conf"         =>      "text/plain",
  ".text"         =>      "text/plain",
  ".txt"          =>      "text/plain",
  ".dtd"          =>      "text/xml",
  ".xml"          =>      "text/xml",
  ".mpeg"         =>      "video/mpeg",
  ".mpg"          =>      "video/mpeg",
  ".mov"          =>      "video/quicktime",
  ".qt"           =>      "video/quicktime",
  ".avi"          =>      "video/x-msvideo",
  ".asf"          =>      "video/x-ms-asf",
  ".asx"          =>      "video/x-ms-asf",
  ".wmv"          =>      "video/x-ms-wmv",
  ".bz2"          =>      "application/x-bzip",
  ".tbz"          =>      "application/x-bzip-compressed-tar",
  ".tar.bz2"      =>      "application/x-bzip-compressed-tar"
 )
#accesslog.filename          = "{$g['varlog_path']}/access.log"
url.access-deny             = ( "~", ".inc" )
static-file.exclude-extensions = ( ".php", ".pl", ".fcgi" )
server.pid-file            = "{$g['varrun_path']}/lighttpd.pid"
auth.backend               = "htpasswd"
auth.backend.htpasswd.userfile = "/usr/local/www/.htpasswd"
cgi.assign = (".php" => "/usr/local/bin/php")
auth.require = ( "/" =>
					(	
						"method"  => "basic",
						"realm"   => "FreeNAS",
						"require" => "valid-user"
					),
				) 

EOD;

	/* non-standard port? */
	if ($config['system']['webgui']['port']) {
	$lighttpdconf .= <<<EOD
server.port                = {$config['system']['webgui']['port']}

EOD;
		
	}


	if ($config['system']['webgui']['protocol'] == "https") {
	
		if ($config['system']['webgui']['certificate'] && $config['system']['webgui']['private-key']) {
			$cert = base64_decode($config['system']['webgui']['certificate']);
			$key = base64_decode($config['system']['webgui']['private-key']);
		} else {
			/* default certificate/key */
			$cert = <<<EOD
-----BEGIN CERTIFICATE-----
MIIBlDCB/gIBADANBgkqhkiG9w0BAQQFADATMREwDwYDVQQKEwhtMG4wd2FsbDAe
Fw0wNTA1MTAxMjI0NDRaFw0wNzA1MTAxMjI0NDRaMBMxETAPBgNVBAoTCG0wbjB3
YWxsMIGfMA0GCSqGSIb3DQEBAQUAA4GNADCBiQKBgQDAShszhFz+o8lsMWTGgTxs
TMPR+v4+qL5jXDyY97MLTGFK7aqQOtpIQc+TcTc4jklgOVlHoR7oBXrsi8YrbCd+
83LPQmQoSPC0VqhfU3uYf3NzxiK8r97aPCsmWgwT2pQ6TcESTm6sF7nLprOf/zFP
C4jE2fvjkbzyVolPywBuewIDAQABMA0GCSqGSIb3DQEBBAUAA4GBAFR962c4R5tV
cTn0OQcszYoW6WC+ini9tQQh5ku5jYDAiC+00atawJEVLnL3lwAcpSKTIWlTkD20
tl3lz5br1qFgYky+Rd0kwS2nk9jRbkxSXxd6KJVnNRCKre28aw3ENzZfCSurPQsX
UPp5er+NtwMT1g7s/JDmKTC4w1rGr5/c
-----END CERTIFICATE-----

EOD;

			$key = <<<EOD
-----BEGIN RSA PRIVATE KEY-----
MIICXQIBAAKBgQDAShszhFz+o8lsMWTGgTxsTMPR+v4+qL5jXDyY97MLTGFK7aqQ
OtpIQc+TcTc4jklgOVlHoR7oBXrsi8YrbCd+83LPQmQoSPC0VqhfU3uYf3NzxiK8
r97aPCsmWgwT2pQ6TcESTm6sF7nLprOf/zFPC4jE2fvjkbzyVolPywBuewIDAQAB
AoGAbJJrQW9fQrggJuLMz/hwsYW2m31oyOBmf5u463YQtjRuSuxe/gj87weZuNqY
H2rXq2k2K+ehl8hgW+egASyUL3L7kCkEAsVREujKTEyhSqqIRDPWTxo9S/YA9Gvn
2ZnJvkrcKjqCO9aHX3rvJOK/ErYI6akctgI3KmgkYw5XNmECQQDuZU97RTWH9rmP
aQr57ysNXxgFsyhetOOqeYkPtIVwpOiNbfwE1zi5RGdtO4Ku3fG1lV4J2UoWJ9yD
awdoyYIHAkEAzn0xJ90IjPsHk+8SODEj5JGdHSZPNu1tgtrbjEi9sfGWg4K7XTxr
QW90pWb1bKKU1uh5FzW6OhnFfuQXt1kC7QJAPSthqY+onKqCEnoxhtAHi/bKgyvl
P+fKQwPMV2tKkgy+XwvJjrRqqZ8TqsOKVLQ+QQmCh6RpjiXMPyxHSmvqIQJBAKLR
HF1ucDuaBROkwx0DwmWMW/KMLpIFDQDNSaiIAuu4rxHrl4mhBoGGPNffI04RtILw
s+qVNs5xW8T+XaT4ztECQQDFHPnZeoPWE5z+AX/UUQIUWaDExz3XRzmIxRbOrlFi
CsF1s0TdJLi/wzNQRAL37A8vqCeVFR/ng3Xpg96Yg+8Z
-----END RSA PRIVATE KEY-----

EOD;
		}
		
		$fdcert = fopen("{$g['varetc_path']}/cert.pem", "w");
		if (!$fdcert) {
			printf("Error: cannot open cert.pem in system_webgui_start().\n");
			return 1;
		}
		chmod("{$g['varetc_path']}/cert.pem", 0600);
		fwrite($fdcert, $cert);
		fwrite($fdcert, "\n");
		fwrite($fdcert, $key);
		fclose($fdcert);
	
		$lighttpdconf .= <<<EOD
ssl.engine = "enable"
ssl.pemfile = "{$g['varetc_path']}/cert.pem"

EOD;
	
	}

	fwrite($fd, $lighttpdconf);
	fclose($fd);

	/* run lighttpd */
		
	$res = mwexec("/usr/local/sbin/lighttpd -f  {$g['varetc_path']}/lighttpd.conf -m /usr/local/lib/lighttpd");
	
	if ($g['booting']) {
		if ($res == 0)
			echo "done\n";
		else
			echo "failed\n";
	}
	
	return $res;
}

function system_password_configure() {
	global $config, $g;
	
	$fd = fopen("{$g['varrun_path']}/htpasswd", "w");
	if (!$fd) {
		printf("Error: cannot open htpasswd in system_password_configure().\n");
		return 1;
	}
	
	if ($config['system']['username'])
		$username = $config['system']['username'];
	else
		$username = "admin";
	
	fwrite($fd, $username . ":" . $config['system']['password'] . "\n");
	fclose($fd);
	chmod("{$g['varrun_path']}/htpasswd", 0600);
	
	return 0;
}

function system_timezone_configure() {
	global $config, $g;

	$syscfg = $config['system'];

	if ($g['booting'])
		echo "Initializing timezone... ";

	/* extract appropriate timezone file */
	$timezone = $syscfg['timezone'];
	if (!$timezone)
		$timezone = "Etc/UTC";
		
	exec("/usr/bin/tar xzfO /usr/share/zoneinfo.tgz " . 
		escapeshellarg($timezone) . " > /etc/localtime");

	if ($g['booting'])
		echo "done\n";
}

function system_ntp_configure() {
	global $config, $g;

	$syscfg = $config['system'];

	if ($g['booting'])
		echo "Starting NTP client... ";
	else {
		killbypid("{$g['varrun_path']}/runmsntp.pid");
		killbypid("{$g['varrun_path']}/msntp.pid");
	}

	/* start ntp client if needed - needs to be forced into background */
	$updateinterval = $syscfg['time-update-interval'];
	
	if ($updateinterval > 0) {
		if ($updateinterval < 6)
			$updateinterval = 6;
		
		$timeservers = "";
		foreach (explode(' ', $syscfg['timeservers']) as $ts)
			$timeservers .= " " . $ts;
		
		mwexec_bg("/usr/local/bin/runmsntp.sh " .
			escapeshellarg("{$g['varrun_path']}/runmsntp.pid") . " " .
			escapeshellarg("{$g['varrun_path']}/msntp.pid") . " " .
			escapeshellarg($updateinterval) . " " .
			escapeshellarg($timeservers));
	}
		
	if ($g['booting'])
		echo "done\n";
}

function system_reboot() {
	global $g;
	
	/* Unmount all disk */
	disks_umount_all();
	disks_raid_stop();
	system_beep_down();
	
	exec("/usr/bin/nohup /etc/rc.reboot > /dev/null 2>&1 &");
}

function system_halt() {
	global $g;
	
	/* Unmount all disk */
	disks_umount_all();
	disks_raid_stop();
	system_beep_down();
	
	exec("/sbin/halt -p > /dev/null 2>&1 &");
}

function system_reboot_sync() {
	global $g;
	
	/* Unmount all disk */
	disks_umount_all();
	disks_raid_stop();
	system_beep_down();

	exec("/etc/rc.reboot > /dev/null 2>&1");
}

function system_do_shell_commands($early = 0) {
	global $config, $g;
	
	if ($early)
		$cmdn = "earlyshellcmd";
	else
		$cmdn = "shellcmd";
	
	if (is_array($config['system'][$cmdn])) {
		
		foreach ($config['system'][$cmdn] as $cmd) {
			exec($cmd);
		}
	}
}

function system_do_extensions($early = false) {
	global $config, $g;
	
	if (!is_dir("{$g['etc_path']}/inc/ext"))
		return;
	
	$dh = @opendir("{$g['etc_path']}/inc/ext");
	if ($dh) {
		while (($extd = readdir($dh)) !== false) {
			if (($extd === ".") || ($extd === ".."))
				continue;
			$rcfile = "{$g['etc_path']}/inc/ext/" . $extd . "/" . ($early ? "rc.early" : "rc");
			if (file_exists($rcfile))
				passthru($rcfile);
		}
		closedir($dh);
	}
}

function system_console_configure() {
	global $config, $g;
	
	if (isset($config['system']['disableconsolemenu'])) {
		touch("{$g['varetc_path']}/disableconsole");
	} else {
		unlink_if_exists("{$g['varetc_path']}/disableconsole");
	}
}

function system_dmesg_save() {
	global $g;
	
	exec("/sbin/dmesg", $dmesg);
	
	/* find last copyright line (output from previous boots may be present) */
	$lastcpline = 0;
	
	for ($i = 0; $i < count($dmesg); $i++) {
		if (strstr($dmesg[$i], "Copyright (c) 1992-"))
			$lastcpline = $i;
	}
	
	$fd = fopen("{$g['varlog_path']}/dmesg.boot", "w");
	if (!$fd) {
		printf("Error: cannot open dmesg.boot in system_dmesg_save().\n");
		return 1;
	}
	
	for ($i = $lastcpline; $i < count($dmesg); $i++)
		fwrite($fd, $dmesg[$i] . "\n");
	
	fclose($fd);
	
	return 0;
}

function system_set_harddisk_standby() {
	global $g, $config;

	if ($g['platform'] != "generic-pc")
		return;

	if (isset($config['system']['harddiskstandby']) && 
			($config['system']['harddiskstandby'] > 0)) {
		if ($g['booting']) {
			echo 'Setting harddisk standby time... ';
		}

		$standby = $config['system']['harddiskstandby'];
		// Check for a numeric value
		if (is_numeric($standby)) {
			// Sync the disk(s)
			mwexec('/bin/sync');
			if (!mwexec('/sbin/sysctl hw.ata.suspend=' . ((int)$standby*60))) {
				// Reinitialize ATA-drives
				mwexec('/usr/local/sbin/atareinit');
				if ($g['booting']) {
					echo "done\n";
				}
			} else if ($g['booting']) {
				echo "failed\n";
			}
		} else if ($g['booting']) {
			echo "failed\n";
		}
	}
}

function system_beep_up()
{
	/* Test if beep is enabled and send sound */
	global $config, $g;
	if (isset($config['system']['disablebeep']))
		return 0;
	else
	{
		mwexec("/sbin/kldload speaker.ko");
		mwexec("/usr/local/bin/beep -p 500 75");
		mwexec("/usr/local/bin/beep -p 1000 75");
		mwexec("/usr/local/bin/beep -p 2000 75");
		mwexec("/usr/local/bin/beep -p 3000 75");
		return 0;
	}
	
}

function system_beep_down()
{
	/* Test if beep is enabled and send sound */
	global $config, $g;
	if (isset($config['system']['disablebeep']))
		return 0;
	else
	{		
		mwexec("/usr/local/bin/beep -p 3000 75");
		mwexec("/usr/local/bin/beep -p 2000 75");
		mwexec("/usr/local/bin/beep -p 1000 75");
		mwexec("/usr/local/bin/beep -p 500 75");
		return 0;
	}
}


function system_set_termcap()
{
	global $config;
	
	if (isset($config['diag']['ipfstatentries'])) {
		$lines = $config['diag']['ipfstatentries'] + 6;
	}
	else {
		$lines = 306;
	}
	
	$termcap = <<<EOD
cons25w|ansiw|ansi80x25-raw:\
	:am:bs:NP:ms:pt:AX:eo:bw:ut:km:\
	:co#80:li#25:pa#64:Co#8:it#8:\
	:al=\E[L:cd=\E[J:ce=\E[K:cl=\E[H\E[J:cm=\E[%i%d;%dH:\
	:dc=\E[P:dl=\E[M:do=\E[B:bt=\E[Z:ho=\E[H:ic=\E[@:cb=\E[1K:\
	:nd=\E[C:rs=\Ec:so=\E[7m:se=\E[27m:up=\E[A:cr=^M:ta=^I:\
	:AF=\E[3%dm:AB=\E[4%dm:op=\E[39;49m:sc=\E7:rc=\E8:\
	:k1=\E[M:k2=\E[N:k3=\E[O:k4=\E[P:k5=\E[Q:k6=\E[R:k7=\E[S:k8=\E[T:\
	:k9=\E[U:k;=\E[V:F1=\E[W:F2=\E[X:K2=\E[E:nw=\E[E:ec=\E[%dX:\
	:kb=^H:kh=\E[H:ku=\E[A:kd=\E[B:kl=\E[D:kr=\E[C:le=^H:sf=\E[S:sr=\E[T:\
	:kN=\E[G:kP=\E[I:@7=\E[F:kI=\E[L:kD=\\177:kB=\E[Z:\
	:IC=\E[%d@:DC=\E[%dP:SF=\E[%dS:SR=\E[%dT:AL=\E[%dL:DL=\E[%dM:\
	:DO=\E[%dB:LE=\E[%dD:RI=\E[%dC:UP=\E[%dA:cv=\E[%i%dd:ch=\E[%i%d`:\
	:mb=\E[5m:md=\E[1m:mr=\E[7m:me=\E[m:bl=^G:\
	:ve=\E[=S:vi=\E[=1S:vs=\E[=2S:
cons25|ansis|ansi80x25:\
	:ac=l\\332m\\300k\\277j\\331u\\264t\\303v\\301w\\302q\\304x\\263n\\305`^Da\\260f\\370g\\361~\\371.^Y-^Xh\\261i^U0\\333y\\363z\\362:\
	:tc=cons25w:
dumb|su|unknown:\
	:am:co#132:li#$lines:do=^J:
xterm-noapp|xterm with cursor keys in normal mode:\
	:kl=\E[D:kd=\E[B:kr=\E[C:ku=\E[A:ks=\E=:ke=\E>:ti@:te@:tc=xterm:
xterm|xterm-color|X11 terminal emulator:\
	:ti@:te@:tc=xterm-xfree86:
xterm-xfree86|XFree86 xterm:\
	:k1=\EOP:k2=\EOQ:k3=\EOR:k4=\EOS:\
	:k5=\E[15~:k6=\E[17~:k7=\E[18~:k8=\E[19~:\
	:k9=\E[20~:k;=\E[21~:F1=\E[23~:F2=\E[24~:\
	:kH=\EOF:@7=\EOF:kI=\E[2~:\
	:kh=\EOH:*6=\EOF:kP=\E[5~:kN=\E[6~:\
	:ku=\EOA:kd=\EOB:kr=\EOC:kl=\EOD:Km=\E[M:tc=xterm-basic:
xterm-basic|xterm common (XFree86):\
	:li#24:co#80:am:kn#12:km:mi:ms:xn:bl=^G:\
	:is=\E[!p\E[?3;4l\E[4l\E>:rs=\E[!p\E[?3;4l\E[4l\E>:le=^H:\
	:AL=\E[%dL:DL=\E[%dM:DC=\E[%dP:al=\E[L:dc=\E[P:dl=\E[M:\
	:UP=\E[%dA:DO=\E[%dB:LE=\E[%dD:RI=\E[%dC:\
	:ho=\E[H:cd=\E[J:ce=\E[K:cl=\E[H\E[2J:cm=\E[%i%d;%dH:cs=\E[%i%d;%dr:\
	:im=\E[4h:ei=\E[4l:ks=\E[?1h\E=:ke=\E[?1l\E>:kD=\E[3~:kb=^H:\
	:sf=\n:sr=\EM:st=\EH:ct=\E[3g:sc=\E7:rc=\E8:\
	:eA=\E(B\E)0:as=^N:ae=^O:ml=\El:mu=\Em:up=\E[A:nd=\E[C:\
	:md=\E[1m:me=\E[m^O:mr=\E[7m:so=\E[7m:se=\E[27m:us=\E[4m:ue=\E[24m:\
	:ti=\E[?1049h:te=\E[?1049l:vi=\E[?25l:ve=\E[?25h:\
	:ut:Co#8:pa#64:op=\E[39;49m:AB=\E[4%dm:AF=\E[3%dm:\

EOD;

	if (!file_exists("/usr/share/misc"))
		mkdir("/usr/share/misc");

	$fd = @fopen("/usr/share/misc/termcap", "w");
	if (!$fd) {
		printf("Error: cannot open termcap in system_set_termcap().\n");
		return 1;
	}
	chmod("/usr/share/misc/termcap", 0644);
	fwrite($fd, $termcap);
	fclose($fd);
	
	return 0;
}

function system_users_create()
{
	/* Create All the passwd file */
	global $config, $g;
	
	if ($g['booting'])
		echo "Generating user database... ";
	
	if (system_user_masterpasswd() )
		$res == 1;
	if (system_user_group() )
		$res == 1;
	if (system_user_pwdmkdb() )
		$res == 1;
	
	if ($g['booting']) {
		if ($res == 0)
			echo "done\n";
		else
			echo "failed\n";
	}
	
	return $res;
	
}

function system_user_masterpasswd()
{
	/* Create the master.passwd file*/
	global $config, $g;
	
	$masterpasswd = <<<EOD
root:{$config['system']['password']}:0:0::0:0:Charlie &:/root:/bin/tcsh
toor:*:0:0::0:0:Bourne-again Superuser:/root:
daemon:*:1:1::0:0:Owner of many system processes:/root:/usr/sbin/nologin
operator:*:2:5::0:0:System &:/:/usr/sbin/nologin
bin:*:3:7::0:0:Binaries Commands and Source:/:/usr/sbin/nologin
tty:*:4:65533::0:0:Tty Sandbox:/:/usr/sbin/nologin
kmem:*:5:65533::0:0:KMem Sandbox:/:/usr/sbin/nologin
www:*:80:80::0:0:World Wide Web Owner:/nonexistent:/usr/sbin/nologin
nobody:*:65534:65534::0:0:Unprivileged user:/nonexistent:/usr/sbin/nologin
ftp:*:21:50::0:0:FTP user:/mnt:/sbin/nologin
sshd:*:22:22::0:0:Secure Shell Daemon:/var/empty:/usr/sbin/nologin
_dhcp:*:65:65::0:0:dhcp programs:/var/empty:/usr/sbin/nologin
dhcpd:*:1002:1002::0:0:DHCP Daemon:/nonexistent:/sbin/nologin

EOD;
	
	if (is_array($config['access']['user']))
	{
		foreach ($config['access']['user'] as $user)
		{
			$password=crypt($user['password']);
			
			if (isset($user['fullshell']))
			{
				$masterpasswd .= <<<EOD
{$user['login']}:{$password}:{$user['id']}:{$user['usergroupid']}::0:0:{$user['fullname']}:/mnt:/bin/tcsh

EOD;
			}
			else
			{
				$masterpasswd .= <<<EOD
{$user['login']}:{$password}:{$user['id']}:{$user['usergroupid']}::0:0:{$user['fullname']}:/mnt:/usr/local/bin/scponly

EOD;
			}
		}

	}
	
	$fd = fopen("{$g['varetc_path']}/master.passwd", "w");
	if (!$fd)
	{
		printf("Error: cannot open master.passwd in system_user_masterpasswd().\n");
		return 1;
	}


	fwrite($fd, $masterpasswd);
	fclose($fd);
	
	return 0;
}

function system_user_group()
{
	/* Create the group file*/
	global $config, $g;
	
	$groupfile = <<<EOD
wheel:*:0:root
EOD;
	
	/* If user exist with full shell, put them on the wheel group */
	if (is_array($config['access']['user']))
		{
			foreach ($config['access']['user'] as $user)
			{
				if (isset($user['fullshell']))
				{
					$groupfile .= <<<EOD
,{$user['login']}
EOD;
				}
			}
		}

	$groupfile .= <<<EOD

daemon:*:1:
kmem:*:2:
sys:*:3:
tty:*:4:
operator:*:5:root
bin:*:7:
staff:*:20:
sshd:*:22:
guest:*:31:
ftp:*:50:
_pflogd:*:64:
_dhcp:*:65:
network:*:69:
www:*:80:
nogroup:*:65533:
nobody:*:65534:
admin:*:1000:

EOD;
	if (is_array($config['access']['group'])) 	{
		foreach ($config['access']['group'] as $group) 	{
			$groupfile .= <<<EOD
{$group['name']}:*:{$group['id']}:

EOD;

		}
	}

	$fd = fopen("{$g['varetc_path']}/group", "w");
	if (!$fd) 	{
		printf("Error: cannot open group in system_user_group().\n");
		return 1;
	}


	fwrite($fd, $groupfile);
	fclose($fd);
	
	return 0;

}

function system_user_pwdmkdb()
{
	/* Generate the db of password */
	global $config, $g;
		
	return (mwexec("/usr/sbin/pwd_mkdb -p -d {$g['varetc_path']} {$g['varetc_path']}/master.passwd"));

}

function system_user_samba()
{
	/* Generate the db of password */
	
	// MUST FIND A WAY OF USING UNIX CRYPTED PASSWORD IN THE PLACE OF CLEAR TEXT PASSWORD FOR GENERATING SAMBA DB!!!
	
	global $config, $g;
	
	if (is_array($config['access']['user'])) 	{
		
		foreach ($config['access']['user'] as $user) 		{		
			$password = escapeshellcmd($user['password']);
			$login = escapeshellcmd($user['login']);
			mwexec("(/bin/echo {$password}; /bin/echo {$password}) | /usr/local/bin/smbpasswd -s -a {$login}");
			
			//mwexec("(/bin/echo {$password}; /bin/echo {$password}) | /usr/local/bin/pdbedit -tau {$login}");
		}
	}
	
	return 0;

}

function system_pam_configure()
{
	/* Create the pam configuration files*/
	global $config, $g;
	
	if (!file_exists("{$g['varetc_path']}/pam.d"))
		mkdir("{$g['varetc_path']}/pam.d", 0744);
	
	$system = <<<EOD
	# System-wide defaults
# auth
auth            sufficient      pam_opie.so             no_warn no_fake_prompts
auth            requisite       pam_opieaccess.so       no_warn allow_local

EOD;
	
	if (isset($config['ad']['enable'])) 	{
	$system .= <<<EOD
#auth	sufficient		/usr/local/lib/pam_winbind.so	debug try_first_pass

EOD;
	} 

	$system .= <<<EOD
auth    required		pam_unix.so             no_warn try_first_pass nullok

# account

EOD;

	if (isset($config['ad']['enable'])) 	{
	$system .= <<<EOD
#account sufficient /usr/local/lib/pam_winbind.so

EOD;
	} 

	$system .= <<<EOD
account         required        pam_login_access.so
account         required        pam_unix.so

# session
session         required        pam_lastlog.so          no_fail

# password

EOD;
	
	if (isset($config['ad']['enable'])) 	{
	$system .= <<<EOD
#password 	sufficient /usr/local/lib/pam_winbind.so	debug try_first_pass

EOD;
	}
	
	$system .= <<<EOD
password        required        pam_unix.so             no_warn try_first_pass

EOD;
	
	
	$fd = fopen("{$g['varetc_path']}/pam.d/system", "w");
	if (!$fd)
	{
		printf("Error: cannot open /pam.d/system in system_pam_configure().\n");
		return 1;
	}
	
	fwrite($fd, $system);
	fclose($fd);
		
	$sshd .= <<<EOD
# PAM configuration for the "sshd" service

# auth
auth            required        pam_nologin.so          no_warn
auth            sufficient      pam_opie.so             no_warn no_fake_prompts
auth            requisite       pam_opieaccess.so       no_warn allow_local

EOD;

	if (isset($config['ad']['enable'])) {
	$sshd .= <<<EOD
auth sufficient /usr/local/lib/pam_winbind.so	debug try_first_pass

EOD;
	} 

	$sshd .= <<<EOD
auth            required        pam_unix.so             no_warn try_first_pass

# account

EOD;

	if (isset($config['ad']['enable'])) 	{
	$sshd .= <<<EOD
account			sufficient		/usr/local/lib/pam_winbind.so

EOD;
	} 

	$sshd .= <<<EOD
account         required        pam_unix.so

# session
session         required        pam_permit.so

# password

EOD;

if (isset($config['ad']['enable']))	{
	$sshd .= <<<EOD
password	sufficient	/usr/local/lib/pam_winbind.so	debug try_first_pass

EOD;
	} 


$sshd .= <<<EOD
password        required        pam_unix.so             no_warn try_first_pass

EOD;
	
	unset($fd);
	$fd = fopen("{$g['varetc_path']}/pam.d/sshd", "w");
	if (!$fd) 	{
		printf("Error: cannot open /pam.d/sshd in system_pam_configure().\n");
		return 1;
	}

	fwrite($fd, $sshd);
	fclose($fd);
	
		$ftp = <<<EOD
# PAM configuration for the "ftpd" service

# auth
auth            required        pam_nologin.so          no_warn
auth            sufficient      pam_opie.so             no_warn no_fake_prompts
auth            requisite       pam_opieaccess.so       no_warn allow_local

EOD;

	if (isset($config['ad']['enable'])) 	{
	$ftp .= <<<EOD
auth	sufficient	/usr/local/lib/pam_winbind.so	debug try_first_pass

EOD;
	} 

	$ftp .= <<<EOD
auth            required        pam_unix.so             no_warn try_first_pass

# account

EOD;

if (isset($config['ad']['enable'])) 	{
	$ftp .= <<<EOD
account		sufficient	/usr/local/lib/pam_winbind.so

EOD;
	} 

	$ftp .= <<<EOD
account         required        pam_login_access.so
account         required        pam_unix.so

# session
session         required        pam_permit.so

EOD;
	
	unset($fd);
	
	$fd = fopen("{$g['varetc_path']}/pam.d/ftp", "w");
	if (!$fd) 	{
		printf("Error: cannot open /pam.d/ftp in system_pam_configure().\n");
		return 1;
	}

	fwrite($fd, $ftp);
	fclose($fd);
	
		
	$login = <<<EOD
# PAM configuration for the "login" service
#

# auth
auth            required        pam_nologin.so          no_warn

EOD;

	if (isset($config['ad']['enable'])) 	{
	$login .= <<<EOD
auth	sufficient	/usr/local/lib/pam_winbind.so	debug try_first_pass

EOD;
	} 

	$login .= <<<EOD
auth            sufficient      pam_self.so             no_warn
auth            include         system

# account

EOD;

if (isset($config['ad']['enable'])) 	{
	$login .= <<<EOD
account		sufficient	/usr/local/lib/pam_winbind.so

EOD;
	} 

	$login .= <<<EOD
account         requisite       pam_securetty.so
account         include         system

# session
session         include         system

# password
password        include         system

EOD;
	
	unset($fd);
	
	$fd = fopen("{$g['varetc_path']}/pam.d/login", "w");
	if (!$fd) 	{
		printf("Error: cannot open /pam.d/login in system_pam_configure().\n");
		return 1;
	}

	fwrite($fd, $login);
	fclose($fd);
	
	unset($fd);
	
	/* Create the nsswitch.conf file*/
	
	
	if (isset($config['ad']['enable'])) 	{
	$nsswitch = <<<EOD
group: files winbind
group_compat: nis
hosts: files dns wins
networks: files
passwd: files winbind
passwd_compat: nis
shells: files

EOD;

	}
	else 	{
	$nsswitch = <<<EOD
group: compat
group_compat: nis
hosts: files dns
networks: files
passwd: compat
passwd_compat: nis
shells: files

EOD;
	}
	
	
	$fd = fopen("{$g['varetc_path']}/nsswitch.conf", "w");
	if (!$fd) 	{
		printf("Error: cannot open /var/etc/nsswitch.conf in system_pam_configure().\n");
		return 1;
	}

	fwrite($fd, $nsswitch);
	fclose($fd);
	
	unset($fd);
	
	return 0;

}

function system_tuning()
{
	/* fine tune kernel value */
	global $config, $g;
	
	if (isset($config['system']['tune'])) 	{
	
		if ($g['booting'])
			echo 'Tuning the system... ';
		
		mwexec('/sbin/sysctl net.inet.tcp.delayed_ack=0');
		mwexec('/sbin/sysctl net.inet.tcp.sendspace=65536');
		mwexec('/sbin/sysctl net.inet.tcp.recvspace=65536');
		mwexec('/sbin/sysctl net.inet.udp.recvspace=65536');
		mwexec('/sbin/sysctl net.inet.udp.maxdgram=57344');
		mwexec('/sbin/sysctl net.local.stream.recvspace=65535');
		mwexec('/sbin/sysctl net.local.stream.sendspace=65535');
		mwexec('/sbin/sysctl kern.ipc.maxsockbuf=2097152');	
		mwexec('/sbin/sysctl kern.ipc.somaxconn=8192');
		// This variable can be set on the boot conf file only:
		// mwexec('/sbin/sysctl kern.ipc.maxsockets=16424');
		mwexec('/sbin/sysctl kern.ipc.nmbclusters=60000');
		mwexec('/sbin/sysctl kern.maxfiles=65536');
		mwexec('/sbin/sysctl kern.maxfilesperproc=32768');
		mwexec('/sbin/sysctl net.inet.tcp.inflight.enable=0');
		
		if ($g['booting'])
			echo "done\n";
		return 0;
	
	}
	else 	{
		// Set the default value (if previously changed) if not booting mode
		if (!$g['booting']) {
			mwexec('/sbin/sysctl net.inet.tcp.delayed_ack=1');
			mwexec('/sbin/sysctl net.inet.tcp.sendspace=32768');
			mwexec('/sbin/sysctl net.inet.tcp.recvspace=65536');
			mwexec('/sbin/sysctl net.inet.udp.recvspace=42080');
			mwexec('/sbin/sysctl net.inet.udp.maxdgram=9216');
			mwexec('/sbin/sysctl net.local.stream.recvspace=8192');
			mwexec('/sbin/sysctl net.local.stream.sendspace=8192');
			mwexec('/sbin/sysctl kern.ipc.maxsockbuf=262144');	
			mwexec('/sbin/sysctl kern.ipc.somaxconn=128');
			mwexec('/sbin/sysctl kern.ipc.nmbclusters=3072');
			mwexec('/sbin/sysctl kern.maxfiles=1064');
			mwexec('/sbin/sysctl kern.maxfilesperproc=957');
		}
			
			return 0;
	}

}

function system_install_mount_cd($cdrom)
{
	/* Part of install process: Mount the CDROM */
	
	global $config, $g;
	
	/* Creating tempo directory for cdrom*/
	echo "Creating Mount point for the CDROM:\n";
	if (mwexec("/bin/mkdir /mnt/cdrom_fr_0507"))
		return 1;
	
	/* Monting the CDROM */
	echo "Mount CDROM:\n";
	if (mwexec("/sbin/mount_cd9660 /dev/$cdrom /mnt/cdrom_fr_0507"))
		return 1;
	
	return 0;
}

function system_install_init_fulldisk($harddrive)
{
		
	/* Part of install process: Initialize the destination disk with one partition */
	
	global $config, $g;
	
	echo "Erasing partitions:\n";
	if (mwexec("/bin/dd if=/dev/zero of=/dev/$harddrive bs=1k count=20"))
		return 1;
	
	echo "Creating the primary partition and install simple MBR:\n";
	if (mwexec("/sbin/fdisk -BI -b /boot/mbr $harddrive"))
		return 1;
	
	/* Wait that the /dev is upatded with the new information */
	// There should be a more intelligent code here than this stupid timer...
	
	echo "Waiting for system update..";
	$devtotest="/dev/$harddrive". "s1";
	$i=0;
	
	while (!file_exists($devtotest)) 	{
		sleep(1);
		echo ".";
		$i++;
		if ($i==20)
			return 1;
	}
	echo "\n";
	
	echo "Erasing primary partition:\n";
	if (mwexec("/bin/dd if=/dev/zero of=/dev/" . escapeshellarg($harddrive) . "s1 bs=32k count=16"))
		return 1;
	
	echo "Creating FreeBSD partition:\n";
	if (mwexec("/sbin/bsdlabel -B -w -b /boot/boot " . escapeshellarg($harddrive) ."s1 auto"))
		return 1;
	
	echo "Setting BSDlabel:\n";

	disks_bsdlabel($harddrive,"s1","4.2BSD");
	
	echo "Creating UFS filesystem:\n";
	if (mwexec("/sbin/newfs -U /dev/" . escapeshellarg($harddrive) . "s1"))
		return 1;
	
	return 0;
}

function system_install_init_halfdisk($harddrive)
{
	/* Part of install process: Initialize the destination disk with 2 partitions */
	
	global $config, $g;
	
	/* Create the partitions */
	fdisk_hd_install($harddrive);
	
	if (mwexec("(/bin/echo y; /bin/echo y) | /sbin/fdisk -B -b /boot/mbr $harddrive"))
		return 0;
	
	/* Must wait that the /dev is upatded with the new information */
	
	echo "Waiting for system update..";

	$devtotest="/dev/$harddrive". "s2";
	$i=0;
	while (!file_exists($devtotest))
	{
		sleep(1);
		echo ".";
		$i++;
		if ($i==20)
			return 1;
	}
	echo "\n";
		
	echo "Creating BSD Label:\n";
	if (mwexec("/sbin/bsdlabel -B -w -b /boot/boot " . escapeshellarg($harddrive) ."s1 auto"))
		return 1;
	if (mwexec("/sbin/bsdlabel -w " . escapeshellarg($harddrive) ."s2 auto"))
		return 1;
	
	echo "Modify BSD Label information:\n";

	disks_bsdlabel($harddrive,"s1","4.2BSD");
	disks_bsdlabel($harddrive,"s2","4.2BSD");
	
	echo "Creating filesystem:\n";
	if (mwexec("/sbin/newfs -U /dev/" . escapeshellarg($harddrive) . "s1"))
		return 1;
	if (mwexec("/sbin/newfs -U /dev/" . escapeshellarg($harddrive) . "s2"))
		return 1;
	
	return 0;

}

function system_install_mount_destdisk($harddrive)
{
	
	/* Part of install process: Mount the destination disk */
	
	global $config, $g;
	
	echo "Creation tempory mouting point:\n";
	if (mwexec("/bin/mkdir /mnt/install_fr_0507"))
		return 1;
	
	echo "Mount destination disk:\n";
	if (mwexec("/sbin/mount /dev/" . escapeshellarg($harddrive) . "s1 /mnt/install_fr_0507"))
		return 1;
		
	return 0;
	
}


function system_install_unmount()
{
	
	/* Part of install process: Unmounting the disks: CDROM and destination drive */
	
	global $config, $g;
	
	/* Unmounting disk */
	if (mwexec("/sbin/umount /mnt/install_fr_0507"))
		return 1;
	if (mwexec("/sbin/umount /mnt/cdrom_fr_0507"))
		return 1;
		
	// using 'rm -rf' for deleting the temp directory is too dangerous here... 
	/*
	
	if (mwexec("/bin/rm -rf /mnt/cdrom_fr_0507"))
		return 1;
	if (mwexec("/bin/rm -rf /mnt/install_fr_0507"))
		return 1;
	
	*/
	
	// Test: replace with PHP function rmdir
	
	@rmdir ("/mnt/cdrom_fr_0507");
	@rmdir ("/mnt/install_fr_0507");
	
	
	
	return 0;
}

function system_install_installation($harddrive)
{
	/* Part of install process: dd image file on the destination disk */
	
	// It's perhaps this brut method 'dd' that create some problem with BIOS configured on LBA or not mode....
	
	global $config, $g;
	
	echo "Installation:\n";
	
	if (mwexec("/usr/bin/gunzip -S \"\" -c /mnt/cdrom_fr_0507/FreeNAS-generic-pc.gz | dd of=/dev/" . escapeshellarg($harddrive) . " bs=16k > /dev/null 2>&1"))
		return 1;
	
	return 0;

}


function system_language_getall()
{
	/* Get all the languages */
	global $g;
 	$handle=opendir($g['www_path']);
    while ($file = readdir($handle)) {
        if (preg_match("/^lang\-(.+)\.inc/", $file, $matches)) {
            $langFound = $matches[1];
            $files[] = $langFound;
        }
    }
    closedir($handle);
    sort($files);
    return $files;

}

function system_language_getlang()
{
	/* Get the language configured*/
	global $config;
	return $config['system']['language'];
}

function system_language_load()
{
	/* Include the language file*/
	global $config, $g;
	if (file_exists($g['www_path']."/lang-".system_language_getlang().".inc")){
		require_once($g['www_path']."/lang-".system_language_getlang().".inc");
		return 1;
	} else {
		require_once($g['www_path']."/lang-English.inc");
		return 1;
	}
}


?>
