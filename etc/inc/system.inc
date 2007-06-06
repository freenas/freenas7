<?php
/*
	system.inc
	part of FreeNAS (http://www.freenas.org)
	Copyright (C) 2005-2007 Olivier Cochard-Labbé <olivier@freenas.org>.
	All rights reserved.
	system_systime_set() function added by Paul Wheels (pwheels@users.sourceforge.net)

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

// Create /var/etc/resolv.conf file. Will be automatically overwritten by /sbin/dhclient-script
// when using DHCP.
// Return 0 if successful, otherwise 1
function system_resolvconf_generate() {
	global $config, $g;

	$syscfg = $config['system'];

	$fd = fopen("{$g['varetc_path']}/resolv.conf", "w");
	if (!$fd) {
		$message = "Error: Can't open resolv.conf in system_resolvconf_generate()";
		write_console($message . ".\n");
		write_log($message);
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

/* Generate the /etc/hosts file
Return 0 if OK, and 1 if error */
function system_hosts_generate() {
	global $config, $g;

	$result = 0 ;

	$syscfg = $config['system'];
	$lanif = $config['interfaces']['lan']['if'];

	if (!is_array($syscfg['hosts'])) {
		$syscfg['hosts'] = array();
	}
	$hostscfg = $syscfg['hosts'];

	$fd = fopen("{$g['varetc_path']}/hosts", "w");
	if (!$fd) {
		$message = "Error: Can't open hosts file in system_hosts_generate()";
		write_console($message . ".\n");
		write_log($message);
		$result = 1;
	}

 	// Get the IP addres of the interface for "auto" NIC
	$lanif = get_ifname($lanif);

	// Get the IP address of the interface.
 	$ipaddr = get_ipaddr($lanif);

	$hosts = <<<EOD
127.0.0.1	localhost localhost.{$syscfg['domain']}
{$ipaddr}	{$syscfg['hostname']}.{$syscfg['domain']} {$syscfg['hostname']}

EOD;

	foreach ($hostscfg as $host) {
		if ($host['name']) {
			$hosts .= "{$host['address']}	{$host['name']}\n";
		}
	}
	fwrite($fd, $hosts);
	fclose($fd);

	return $result;
}

function system_hostname_configure() {
	global $config;

	$syscfg = $config['system'];

	/* set hostname */
	return mwexec("/bin/hostname " . escapeshellarg("{$syscfg['hostname']}.{$syscfg['domain']}"));
}

/* Generate the static routes
Return 0 if OK, and 1 if error */
function system_routing_configure() {
	global $config, $g;

	$result = 0 ;

	/* clear out old routes, if necessary */
	if (file_exists("{$g['vardb_path']}/routes.db")) {
		$fd = fopen("{$g['vardb_path']}/routes.db", "r");
		if (!$fd) {
			$message = "Error: Can't open routes DB file in system_routing_configure()";
			write_console($message . ".\n");
			write_log($message);
			$result = 1 ;
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
			$message = "Error: Can't open routes DB file in system_routing_configure()";
			write_console($message . ".\n");
			write_log($message);
			$result = 1 ;
		}

		foreach ($config['staticroutes']['route'] as $rtent) {
			mwexec("/sbin/route add " . escapeshellarg($rtent['network']) .
				" " . escapeshellarg($rtent['gateway']));

			/* record route so it can be easily removed later (if necessary) */
			fwrite($fd, $rtent['network'] . "\n");
		}

		fclose($fd);
	}

	return $result;
}

/* Start the syslog daemon
Return 0 if OK, and 1 or retval if error */
function system_syslogd_start() {
	global $config, $g;

	$retval = 0 ;

	$syslogcfg = $config['syslog'];

	if ($g['booting']) {
		write_console("Starting syslog service... ");
	} else {
		killbypid("{$g['varrun_path']}/syslog.pid");
	}

	if (isset($syslogcfg['enable'])) {

		/* write syslog.conf */
		$fd = fopen("{$g['varetc_path']}/syslog.conf", "w");
		if (!$fd) {
			$message = "Error: Can't open syslog.conf in system_syslogd_start()";
			write_console($message . ".\n");
			write_log($message);
			$retval = 1 ;
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
		write_console("done\n");

	return $retval;
}

function system_pccard_start() {
	global $config, $g;

	if ($g['booting'])
		write_console("Initializing PC cards... ");

	/* kill any running pccardd */
	killbypid("{$g['varrun_path']}/pccardd.pid");

	/* fire up pccardd */
	$res = mwexec("/usr/sbin/pccardd -z -f {$g['etc_path']}/pccard.conf");

	if ($g['booting']) {
		if ($res == 0)
			write_console("done\n");
		else
			write_console("failed (probably no PC card controller present)\n");
	}

	return $res;
}

// Start the WebGUI.
// Return 0 if OK, and 1 or retval if error
function system_webgui_start() {
	global $config, $g;

	$result = 0 ;

	if ($g['booting'])
		write_console("Starting webGUI... ");

	/* kill any running lighttpd */
	killbypid("{$g['varrun_path']}/lighttpd.pid");

	chdir($g['www_path']);

	// Generate lighttpd.conf
	$fd = fopen("{$g['varetc_path']}/lighttpd.conf", "w");
	if (!$fd) {
		$message = "Error: Can't open lightppd.conf in system_webgui_start()";
		write_console($message . ".\n");
		write_log($message);
		$result = 1 ;
	}

	$lighttpdconf = <<<EOD
server.modules              = (
                                "mod_access",
                                "mod_auth",
                                "mod_cgi" )
#                               "mod_accesslog" )
server.document-root        = "{$g['www_path']}"
#server.errorlog             = "{$g['varlog_path']}/lighttpd.error.log"
server.errorlog-use-syslog = "enable"
server.event-handler        = "freebsd-kqueue"
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
						"realm"   => "{$config['system']['hostname']}",
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

	if ($config['system']['webgui']['protocol'] === "https") {
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
			$message = "Error: Can't open cert.pem in system_webgui_start()";
			write_console($message . ".\n");
			write_log($message);
			$result = 1 ;
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

	// Run lighttpd. Check if there is a previous error.
	if (!$result) {
		$result = mwexec("/usr/local/sbin/lighttpd -f  {$g['varetc_path']}/lighttpd.conf -m /usr/local/lib/lighttpd");
	}

	if (0 == $result) {
		if($g['booting'])
			write_console("done\n");
		write_log("WebGUI started");
	} else {
		if($g['booting'])
			write_console("failed\n");
		write_log("Error: Failed to start WebGUI");
	}

	return $result;
}

// The date utility exits 0 on success, 1 if unable to set the date,
// and 2 if able to set the local date, but unable to set it globally.
function system_systime_set($date) {
	$result = mwexec("/bin/date -n {$date}");
	return $result;
}

function system_timezone_configure() {
	global $config, $g;

	$syscfg = $config['system'];

	if ($g['booting'])
		write_console("Initializing timezone... ");

	/* extract appropriate timezone file */
	$timezone = $syscfg['timezone'];
	if (!$timezone)
		$timezone = "Etc/UTC";

	exec("/usr/bin/tar xzfO /usr/share/zoneinfo.tgz " . escapeshellarg($timezone) . " > /etc/localtime");

	if ($g['booting'])
		write_console("done\n");
}

function system_ntp_configure() {
	global $config, $g;

	$syscfg = $config['system'];

	if ($g['booting'])
		write_console("Starting NTP client... ");
	else {
		killbypid("{$g['varrun_path']}/runmsntp.pid");
		killbypid("{$g['varrun_path']}/msntp.pid");
		write_log("Stopping NTP client");
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

	if ($g['booting']) {
		write_console("done\n");
	}

	write_log("NTP client started");
}

function system_stop_common() {
	/* Stop iSCSI-target */
	// If iSCSI-target is not stoped before disabling swap, the system can crash
	services_iscsitarget_stop();

	/* Stop swap */
	system_swap_disable();

	/* Umount all disk s*/
	disks_umount_all();

	/* Detach all encrypted disks */
	disks_geli_detach_all();

	/* Stop all raid disks */
	disks_raid_stop();
}

function system_reboot() {
	system_stop_common();

	/* Send beep */
	system_beep_down();

	exec("/usr/bin/nohup /etc/rc.reboot > /dev/null 2>&1 &");
}

function system_halt() {
	system_stop_common();

	/* Send beep */
	system_beep_down();

	exec("/sbin/halt -p > /dev/null 2>&1 &");
}

function system_reboot_sync() {
	system_stop_common();

	/* Send beep */
	system_beep_down();

	exec("/etc/rc.reboot > /dev/null 2>&1");
}

function system_do_shell_commands($early = 0) {
	global $config;

	if ($early)
		$cmdn = "earlyshellcmd";
	else
		$cmdn = "shellcmd";

	if (is_array($config['system'][$cmdn])) {
		foreach ($config['system'][$cmdn] as $cmd) {
			system($cmd);
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
		$message = "Error: Can't open dmesg.boot in system_dmesg_save()";
		write_console($message . ".\n");
		write_log($message);
		return 1;
	}

	for ($i = $lastcpline; $i < count($dmesg); $i++)
		fwrite($fd, $dmesg[$i] . "\n");

	fclose($fd);

	return 0;
}

function system_set_harddisk_standby() {
	global $g, $config;

	if (($g['platform'] != "embedded") || ($g['platform'] != "full"))
		return;

	if (isset($config['system']['harddiskstandby']) &&
			($config['system']['harddiskstandby'] > 0)) {
		if ($g['booting']) {
			write_console("Setting harddisk standby time... ");
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
					write_console("done\n");
				}
			} else if ($g['booting']) {
				write_console("failed\n");
			}
		} else if ($g['booting']) {
			write_console("failed\n");
		}
	}
}

function system_beep_up()
{
	global $config;

	/* Test if beep is enabled and send sound */
	if (!isset($config['system']['disablebeep'])) {
		if (file_exists("/dev/speaker")) {
			exec('echo O3L30cO4L30cO5L30cO5L30g > /dev/speaker');
		}
	}

	return 0;
}

function system_beep_down()
{
	global $config;

	/* Test if beep is enabled and send sound */
	if (!isset($config['system']['disablebeep'])) {
		if (file_exists("/dev/speaker")) {
			exec('echo O5L30gO5L30cO4L30cO3L30c > /dev/speaker');
		}
	}

	return 0;
}

// Create the following files:
// - /var/etc/master.passwd
// - /var/etc/group
// - /var/etc/passwd, pwd.db and spwd.db
// - /var/run/.htpasswd
// Return 0 if successful, otherwise 1
function system_create_usermanagement()
{
	global $g;

	$result = 0;

	if ($g['booting'])
		write_console("Generating user/password database(s)... ");

	$result |= system_create_masterpasswd();
	$result |= system_create_group();
	$result |= system_create_pwdmkdb();
	$result |= system_create_htpasswd();

	if (0 == $result) {
		if($g['booting'])
			write_console("done\n");
		write_log("User/password database(s) created");
	} else {
		if($g['booting'])
			write_console("failed\n");
		write_log("Error: Failed to create user/password database(s)");
	}

	return $result;
}

// Create the /var/etc/master.passwd file.
// Return 0 if successful, otherwise 1
function system_create_masterpasswd()
{
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
man:*:9:9::0:0:Mister Man Pages:/usr/share/man:/usr/sbin/nologin
sshd:*:22:22::0:0:Secure Shell Daemon:/var/empty:/usr/sbin/nologin
_dhcp:*:65:65::0:0:dhcp programs:/var/empty:/usr/sbin/nologin

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
	if (!$fd) {
		$message = "Error: Can't open master.passwd in system_create_masterpasswd()";
		write_console($message . ".\n");
		write_log($message);
		return 1;
	}

	fwrite($fd, $masterpasswd);
	fclose($fd);

	return 0;
}

// Generate the /var/etc/passwd, pwd.db and spwd.db files.
// Return 0 if successful, otherwise 1
function system_create_pwdmkdb()
{
	global $g;

	return (mwexec("/usr/sbin/pwd_mkdb -p -d {$g['varetc_path']} {$g['varetc_path']}/master.passwd"));
}

// Create the /var/etc/group file.
// Return 0 if successful, otherwise 1
function system_create_group()
{
	global $config, $g;

	$groupfile = <<<EOD
wheel:*:0:root
EOD;

	/* If user exist with admin right, put them on the wheel group */
	if (is_array($config['access']['user'])) {
		foreach ($config['access']['user'] as $user) {
			if (isset($user['admin'])) {
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
man:*:9:
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
		$message = "Error: Can't open group in system_create_group()";
		write_console($message . ".\n");
		write_log($message);
		return 1;
	}

	fwrite($fd, $groupfile);
	fclose($fd);

	return 0;
}

// Generate the /var/etc/private/smbpasswd samba password database.
// Return 0 if successful, otherwise 1
function system_create_smbpasswd()
{
	// MUST FIND A WAY OF USING UNIX CRYPTED PASSWORD IN THE PLACE OF CLEAR TEXT PASSWORD FOR GENERATING SAMBA DB!!!
	global $config;

	if (is_array($config['access']['user'])) {
		foreach ($config['access']['user'] as $user) {
			$password = escapeshellcmd($user['password']);
			$login = escapeshellcmd($user['login']);
			mwexec("(/bin/echo {$password}; /bin/echo {$password}) | /usr/local/bin/smbpasswd -s -a {$login}");
		}
	}

	return 0;
}

// Generate the /var/run/.htpasswd file used by lighttpd.
// Return 0 if successful, otherwise 1
function system_create_htpasswd() {
	global $config, $g;

	$result = 0 ;

	$fd = fopen("{$g['varrun_path']}/.htpasswd", "w");
	if (!$fd) {
		$message = "Error: Can't create .htpasswd in system_create_htpasswd()";
		write_console($message . ".\n");
		write_log($message);
		return 1;
	}

	if ($config['system']['username'])
		$username = $config['system']['username'];
	else
		$username = "admin";

	fwrite($fd, $username . ":" . $config['system']['password'] . "\n");
	fclose($fd);

	$result = mwexec("/bin/chmod 0600 {$g['varrun_path']}/.htpasswd");

	return $result;
}

/* Configure PAM, NSS and LDAP modules
Return 0 is Ok, 1 is error */
function system_pam_configure()
{
	/* Create the pam configuration files*/
	global $config, $g;

	$result = 0;

	if (!file_exists("{$g['varetc_path']}/pam.d")) {
		mkdir("{$g['varetc_path']}/pam.d", 0744);
	}

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
	if (isset($config['ldap']['enable'])) 	{
		$system .= <<<EOD
#auth			sufficient		/usr/local/lib/pam_ldap.so	no_warn try_first_pass

EOD;
	}

	$system .= <<<EOD
auth    required		pam_unix.so             no_warn try_first_pass nullok

# account

EOD;

	if (isset($config['ad']['enable'])) {
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
	if (!$fd) 	{
		$message = "Error: Can't open /pam.d/system in system_pam_configure()";
		write_console($message . ".\n");
		write_log($message);
		$result = 1;
	}

	fwrite($fd, $system);
	fclose($fd);

	$sshd = <<<EOD
# PAM configuration for the "sshd" service

# auth
auth            required        pam_nologin.so          no_warn
auth            sufficient      pam_opie.so             no_warn no_fake_prompts
auth            requisite       pam_opieaccess.so       no_warn allow_local

EOD;

	if (isset($config['ad']['enable'])) {
		$sshd .= <<<EOD
auth	sufficient			/usr/local/lib/pam_winbind.so	debug try_first_pass

EOD;
	}
	if (isset($config['ldap']['enable'])) 	{
		$sshd .= <<<EOD
auth	sufficient		/usr/local/lib/pam_ldap.so	no_warn try_first_pass

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
		$message = "Error: Can't open /pam.d/sshd in system_pam_configure()";
		write_console($message . ".\n");
		write_log($message);
		$result = 1;
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

	if (isset($config['ldap']['enable'])) 	{
		$ftp .= <<<EOD
auth	sufficient		/usr/local/lib/pam_ldap.so	no_warn try_first_pass

EOD;
	}

	$ftp .= <<<EOD
auth            required        pam_unix.so         no_warn try_first_pass

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
		$message = "Error: Can't open /pam.d/ftp in system_pam_configure()";
		write_console($message . ".\n");
		write_log($message);
		$result = 1;
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
	if (isset($config['ldap']['enable'])) 	{
		$login .= <<<EOD
auth	sufficient	/usr/local/lib/pam_ldap.so	no_warn try_first_pass

EOD;
	}

	$login .= <<<EOD
auth	sufficient	pam_self.so	no_warn
auth	include	system

# account

EOD;

	if (isset($config['ad']['enable'])) {
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
		$message = "Error: Can't open /pam.d/login in system_pam_configure()";
		write_console($message . ".\n");
		write_log($message);
		$result = 1;
	}

	fwrite($fd, $login);
	fclose($fd);

	unset($fd);

	/* Create the nsswitch.conf file*/
	if ((isset($config['ad']['enable'])) && (!isset($config['ldap']['enable'])))	{
		$nsswitch = <<<EOD
group: files winbind
group_compat: nis
hosts: files dns wins
networks: files
passwd: files winbind
passwd_compat: nis
shells: files

EOD;
	} else if ((isset($config['ad']['enable'])) && (isset($config['ldap']['enable']))) {
		$nsswitch = <<<EOD
group: files winbind ldap
group_compat: nis
hosts: files dns wins ldap
networks: files
passwd: files winbind ldap
passwd_compat: nis
shells: files

EOD;
	} else if ((!isset($config['ad']['enable'])) && (isset($config['ldap']['enable'])))	{
		$nsswitch = <<<EOD
group: files ldap
group_compat: nis
hosts: files dns ldap
networks: files
passwd: files ldap
passwd_compat: nis
shells: files

EOD;

	} else 	{
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
		$message = "Error: Can't open /var/etc/nsswitch.conf in system_pam_configure()";
		write_console($message . ".\n");
		write_log($message);
		$result = 1;
	}

	fwrite($fd, $nsswitch);
	fclose($fd);

	unset($fd);

	// Generate the ldap.conf file

	if (isset($config['ldap']['enable'])) {
		$ldapconf = <<<EOD
uri ldap://{$config['ldap']['host_ip']}
base {$config['ldap']['base']}
binddn {$config['ldap']['binddn']}
bindpw {$config['ldap']['bindpw']}
nss_base_passwd {$config['ldap']['password_suffix']}?one
nss_base_shadow {$config['ldap']['password_suffix']}?one
nss_base_group {$config['ldap']['group_suffix']}?one
pam_password {$config['ldap']['pam_password']}

EOD;
		$fd = fopen("{$g['varetc_path']}/ldap.conf", "w");
		if (!$fd) 	{
			$message = "Error: Can't open /var/etc/ldap.conf in system_pam_configure()";
			write_console($message . ".\n");
			write_log($message);
			$result = 1;
		}

		fwrite($fd, $ldapsecret);
		fclose($fd);

		unset($fd);

		// Generate the ldap.secret file

		$ldapsecret = <<<EOD
{$config['ldap']['password_suffix']}

EOD;
		$fd = fopen("{$g['varetc_path']}/ldap.secret", "w");
		if (!$fd) 	{
			$message = "Error: Can't open /var/etc/ldap.secret in system_pam_configure()";
			write_console($message . ".\n");
			write_log($message);
			$result = 1;
		}

		fwrite($fd, $ldapsecret);
		fclose($fd);
		chmod("{$g['varetc_path']}/ldap.secret",0600);
		unset($fd);
	}

	return $result;
}

/* Set the tuning value
return 0 */
function system_tuning()
{
	/* fine tune kernel value */
	global $config, $g;

	if (isset($config['system']['tune'])) {
		if ($g['booting']) {
			write_console("Tuning the system... ");
		}

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
		mwexec('/sbin/sysctl kern.ipc.nmbclusters=32768');
		mwexec('/sbin/sysctl kern.maxfiles=65536');
		mwexec('/sbin/sysctl kern.maxfilesperproc=32768');
		mwexec('/sbin/sysctl net.inet.tcp.inflight.enable=0');
		mwexec('/sbin/sysctl net.inet.tcp.path_mtu_discovery=0');

		if ($g['booting']) {
			write_console("done\n");
		}
	}
	else 	{
		// Set the default value (if previously changed) if not booting mode
		if (!$g['booting']) {
			mwexec('/sbin/sysctl net.inet.tcp.delayed_ack=1');
			mwexec('/sbin/sysctl net.inet.tcp.inflight.enable=1');
			mwexec('/sbin/sysctl net.inet.tcp.path_mtu_discovery=1');
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
	}

	return 0;
}

/* Init language environment */
function system_language_load()
{
	global $config, $g_languages;

	/* Get the language configured*/
	$language = $config['system']['language'];
	$locale = $g_languages[$language];
	$domain = strtolower( get_product_name());
	$codeset = ltrim(strstr($locale,"."),".");

	putenv( "LANG=$locale");
	setlocale( LC_MESSAGES, $locale);
	bindtextdomain( $domain, "/usr/local/share/locale");
	bind_textdomain_codeset( $domain, $codeset);
	textdomain( $domain);
}

/* Enable the swap file
Return 0 is OK, 1 if error
*/
function system_swap_configure()
{
	global $config, $g;

	$result = 0;

	// Stop swap if not in booting mode
	if (!$g['booting']) {
		// MUST add a check if the system can stop swap without problem
		write_log("Stopping swap");
		$result = mwexec("/sbin/swapoff /dev/md2");

		// Destroy block device
		$result |= mwexec("/sbin/mdconfig -d -u 2");

		// Delete swap file
		write_log("Deleting swap file");
		$result = mwexec("rm -f /mnt/{$config['system']['swap_mountname']}/swap_file");
	}

	if (isset($config['system']['swap_enable'])) {
		if (!file_exists("/mnt/{$config['system']['swap_mountname']}/swap_file")){
			$result |= mwexec("/bin/dd if=/dev/zero of=/mnt/{$config['system']['swap_mountname']}/swap_file bs=1024k count={$config['system']['swap_size']}");
			// $result |= mwexec("chmod 0600 /mnt/{$config['system']['swap_mountname']}/swap_file");
			chmod("/mnt/{$config['system']['swap_mountname']}/swap_file",0600);
		}
		// Create the block device
		$result |= mwexec("/sbin/mdconfig -a -t vnode -f /mnt/{$config['system']['swap_mountname']}/swap_file -u 2");
		// Enable Swap
		write_log("Enabling swap");
		$result |= mwexec("/sbin/swapon /dev/md2");
	}

	return $result;
}

/* Disable the swap file
Return 0 is OK, 1 if error
*/
function system_swap_disable()
{
	global $config;

	if (isset($config['system']['swap'])) {
		// Stop swap
		write_log("Stopping swap");
		$result = mwexec("/sbin/swapoff /dev/md2}");

		//Destroy block device
		$result |= mwexec("/sbin/mdconfig -d -u 2");

		//Delete the swap file (I hope that no user have important file called swap_file!)
		if (file_exists("/mnt/{$config['system']['swap_mountname']}/swap_file")){
			write_log("Deleting swap file");
			$result = mwexec("rm -f /mnt/{$config['system']['swap_mountname']}/swap_file");
		}
	}

	return $result;
}
?>
