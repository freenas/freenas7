<?php
/*
	services.inc
	part of FreeNAS (http://www.freenas.org)
	Copyright (C) 2005-2007 Olivier Cochard-Labbe <olivier@freenas.org>.
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
require_once("functions.inc");
require_once("util.inc");

// Create lighttpd.conf file.
// Return 0 if successful, otherwise 1.
function services_create_lighttpd_conf() {
	global $config, $g;

	$fd = fopen("{$g['varetc_path']}/lighttpd.conf", "w");
	if (!$fd) {
		$message = "Error: Failed to create '{$g['varetc_path']}/lighttpd.conf'";
		write_console($message . ".\n");
		write_log($message);
		return 1;
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

	// Non-standard port?
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
			// Default certificate/key.
			$cert = <<<EOD

-----BEGIN CERTIFICATE-----
MIIDVTCCAr6gAwIBAgIJAJKqT796+FGHMA0GCSqGSIb3DQEBBQUAMHsxCzAJBgNV
BAYTAkZSMRMwEQYDVQQIEwpTb21lLVN0YXRlMQ8wDQYDVQQHEwZOYW50ZXMxEDAO
BgNVBAoTB0ZyZWVOQVMxEDAOBgNVBAMTB0ZyZWVOQVMxIjAgBgkqhkiG9w0BCQEW
E29saXZpZXJAZnJlZW5hcy5vcmcwHhcNMDcwNjI4MTcyODA5WhcNMzQxMTEyMTcy
ODA5WjB7MQswCQYDVQQGEwJGUjETMBEGA1UECBMKU29tZS1TdGF0ZTEPMA0GA1UE
BxMGTmFudGVzMRAwDgYDVQQKEwdGcmVlTkFTMRAwDgYDVQQDEwdGcmVlTkFTMSIw
IAYJKoZIhvcNAQkBFhNvbGl2aWVyQGZyZWVuYXMub3JnMIGfMA0GCSqGSIb3DQEB
AQUAA4GNADCBiQKBgQCjKqGAk2OKnoIZHPb9hhUZy8pFEtPvmR0IfE7VAmsTYTzV
vnhf6YWnCQIYPerOm+WrLPqfeqpjjiNRpcPX1LCiOPNSQsSOV+iaUdCvrGVtolrk
lhbSQ860gQFVZKkMslnvrdkUY8TLtL5mXaNj7zMh4UcNugmkkG8eERGkPDuxqQID
AQABo4HgMIHdMB0GA1UdDgQWBBQ2aBkawbD7+upiqT9nmSviC8/APTCBrQYDVR0j
BIGlMIGigBQ2aBkawbD7+upiqT9nmSviC8/APaF/pH0wezELMAkGA1UEBhMCRlIx
EzARBgNVBAgTClNvbWUtU3RhdGUxDzANBgNVBAcTBk5hbnRlczEQMA4GA1UEChMH
RnJlZU5BUzEQMA4GA1UEAxMHRnJlZU5BUzEiMCAGCSqGSIb3DQEJARYTb2xpdmll
ckBmcmVlbmFzLm9yZ4IJAJKqT796+FGHMAwGA1UdEwQFMAMBAf8wDQYJKoZIhvcN
AQEFBQADgYEAWFvm1x2crB5nTjW0np8YayCXgTvJN5zHgILR+84fKfseMlogT+T/
gAkxW/rGRNsf6UEUGS8mov6S2y21ldph05tjt5WegGvT4OAAlpltZh4QH1z3ArZF
73xX9WbPI2LkKf/RO1Cs442aSRkbCZ1tZRyNmYEPYZt/5b6+ZgXKlAE=
-----END CERTIFICATE-----

EOD;

			$key = <<<EOD
-----BEGIN RSA PRIVATE KEY-----
MIICWwIBAAKBgQCjKqGAk2OKnoIZHPb9hhUZy8pFEtPvmR0IfE7VAmsTYTzVvnhf
6YWnCQIYPerOm+WrLPqfeqpjjiNRpcPX1LCiOPNSQsSOV+iaUdCvrGVtolrklhbS
Q860gQFVZKkMslnvrdkUY8TLtL5mXaNj7zMh4UcNugmkkG8eERGkPDuxqQIDAQAB
AoGALFYZLGbgVXpcv5YvRxR4B1poy/whuqlQSk383cSac9moPixTaTx7JLg96Xhp
pVqXPbwrHcqnIgYtcrIoVS1mET9VPUQExyh8DmtDgQ3Iwvg8szmbdpzEVndrVmRl
ZGhMIjxdMmWuQrROyCkz1EXGPZ0BPaRoK9XbaliyYuCWK0ECQQDWdS3FS4HirSZT
j44IF7Oeg7Ma/J6ruvIkM1GOudD+r5t368/+sJ16QyrcrylmC7CrgoT4N6WrxTJ+
31+/iOPvAkEAwsX0SQBdAByIW9cmdBc0IqqjA78dZRZ5DPc30lChZiUAmGD6wCcm
menq4sP2i4h1EtKkRulz/wKzY8gRJwFL5wJAGmNQvnYMBVhI3v9K+rPv5hFOeIOd
AoR+RvYJkwZKMQxqlNbvQOd6eRDKtVzYOzY1BuWIdwJh4EDjdbkGjwrJAQJAaX7g
3r2Eoe/SZ7Q0DsMWqQQ1gWz9lehDLqOkp5SmpvW5yc8VYgiSnuEVKTgtDMyfZM+U
zRfbjS9ATPZrW+4W1wJAWgDCN8tt1yKiIe1EMWsNfRV75ubogH489DO35ud+P8ss
EG4I1k299JhubmkB2FjvgFC4BTA7C75nBLpsY6ckYA==
-----END RSA PRIVATE KEY-----

EOD;
		}

		$fdcert = fopen("{$g['varetc_path']}/cert.pem", "w");
		if (!$fdcert) {
			$message = "Error: Failed to create '{$g['varetc_path']}/cert.pem'";
			write_console($message . ".\n");
			write_log($message);
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

	return 0;
}

// Create mdnsresponder.conf file.
// Return 0 if successful, otherwise 1.
function services_create_mdnsresponder_conf() {
	global $config, $g;

	$fd = fopen("{$g['varetc_path']}/mdnsresponder.conf", "w");
	if (!$fd) {
		$message = "Error: Failed to create '{$g['varetc_path']}/mdnsresponder.conf'";
		write_console($message . ".\n");
		write_log($message);
		return 1;
	}

	// Check if WebGUI use HTTPS
	if ($config['system']['webgui']['protocol'] === "https") {
		if ($config['system']['webgui']['port']) {
			$mdnsresponderconf = <<<EOD
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
		} else {
		$mdnsresponderconf = <<<EOD
{$config['system']['hostname']} WebAdmin Service
_https._tcp. local.
443
path=/index.php

EOD;
		}
		// if WebGUI don't use HTTPS then:
		} else {
			if ($config['system']['webgui']['port']) {
				$mdnsresponderconf = <<<EOD
{$config['system']['hostname']} WebAdmin Service
_http._tcp. local.
{$config['system']['webgui']['port']}
path=/index.php

EOD;
			} else {
				$mdnsresponderconf = <<<EOD
{$config['system']['hostname']} WebAdmin Service
_http._tcp. local.
80
path=/index.php

EOD;
			}
		}

	// Check if AFP service is enabled
	if (isset($config['afp']['enable'])) {
		$mdnsresponderconf .= <<<EOD

{$config['system']['hostname']} AFP Service
_afpovertcp._tcp. local.
548

EOD;
	}

	// Check if RSYNC service is enabled
	if (isset($config['rsyncd']['enable'])) {
		$mdnsresponderconf .= <<<EOD

{$config['system']['hostname']} RSYNC Service
_rsync._tcp. local.
{$config['rsyncd']['port']}

EOD;
	}

	// Check if SSH service is enabled
	if (isset($config['sshd']['enable'])) {
		$mdnsresponderconf .= <<<EOD

{$config['system']['hostname']} SSH Service
_ssh._tcp. local.
{$config['sshd']['port']}

EOD;
	}

	// Check if FTP service is enabled
	if (isset($config['ftp']['enable'])) {
		$mdnsresponderconf .= <<<EOD

{$config['system']['hostname']} FTP Service
_ftp._tcp. local.
{$config['ftp']['port']}

EOD;
	}

	// Check if Samba service is enabled
	if (isset($config['samba']['enable'])) {
		$mdnsresponderconf .= <<<EOD

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
			$mdnsresponderconf .= <<<EOD

{$config['system']['hostname']} NFS Service
_nfs._tcp. local.
2049
path=/mnt/{$mount['sharename']}

EOD;
		}
	}

	// Check if UPnP service is enabled
	if (isset($config['upnp']['enable'])) {
		$mdnsresponderconf .= <<<EOD

{$config['system']['hostname']} UPnP Service
_upnp._tcp. local.
1900

EOD;
	}

	fwrite($fd, $mdnsresponderconf);
	fclose($fd);

	return 0;
}

// Create smb.conf file.
// Return 0 if successful, otherwise 1.
function services_create_samba_conf() {
	global $config, $g;

	$fd = fopen("{$g['varetc_path']}/smb.conf", "w");
	if (!$fd) {
		$message = "Error: Failed to create '{$g['varetc_path']}/smb.conf'";
		write_console($message . ".\n");
		write_log($message);
		return 1;
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
	} else {
		$sambaconf .= <<<EOD
SO_SNDBUF=16384
EOD;
	}

	if ($config['samba']['rcvbuf']) {
		$sambaconf .= <<<EOD
SO_RCVBUF={$config['samba']['rcvbuf']}
EOD;
	} else {
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

	if (is_array($config['mounts']['mount'])) {
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

			if (isset($config['afp']['enable'])) {
				$sambaconf .= <<<EOD
vfs objects = netatalk

EOD;
			}
		}
	}

	fwrite($fd, $sambaconf);
	fclose($fd);

	return 0;
}

// Create rsyncd.conf file.
// Return 0 if successful, otherwise 1.
function services_create_rsyncd_conf() {
	global $config, $g;

	$fd = fopen("{$g['varetc_path']}/rsyncd.conf", "w");
	if (!$fd) {
		$message = "Error: Failed to create '{$g['varetc_path']}/rsyncd.conf'";
		write_console($message . ".\n");
		write_log($message);
		return 1;
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
		} else {
			$rsyncdconf .= <<<EOD
uid = ftp
gid = ftp

EOD;
	}

	if (is_array($config['mounts']['mount'])) {
		foreach ($config['mounts']['mount'] as $mountent) {
			$rsyncdconf .= <<<EOD

[{$mountent['sharename']}]
comment = {$mountent['desc']}
path = /mnt/{$mountent['sharename']}

EOD;
		}
	}

	fwrite($fd, $rsyncdconf);
	fclose($fd);

	// Generate MOTD file if configured
	if (!empty($config['rsyncd']['motd'])) {
		$fd = fopen("{$g['varetc_path']}/rsyncd.motd", "w");
		if (!$fd) {
			$message = "Error: Failed to create '{$g['varetc_path']}/rsyncd.motd'";
			write_console($message . ".\n");
			write_log($message);
		} else {
			$motd  = <<<EOD
{$config['rsyncd']['motd']}

EOD;

			fwrite($fd, $motd);
			fclose($fd);
		}
	}

	return 0;
}

// Create snmpd.conf file.
// Return 0 if successful, otherwise 1.
function services_create_snmpd_conf() {
	global $config, $g;

	$fd = fopen("{$g['varetc_path']}/snmpd.conf", "w");
	if (!$fd) {
		$message = "Error: Failed to create '{$g['varetc_path']}/snmpd.conf'";
		write_console($message . ".\n");
		write_log($message);
		return 1;
	}

	$snmpdconf = <<<EOD
syslocation "{$config['snmpd']['syslocation']}"
syscontact "{$config['snmpd']['syscontact']}"
rocommunity "{$config['snmpd']['rocommunity']}"

EOD;

	fwrite($fd, $snmpdconf);
	fclose($fd);

	return 0;
}

// Create .../ssh/sshd_config file.
// Return 0 if successful, otherwise 1.
function services_create_sshd_conf() {
	global $config, $g;

	$fd = fopen("{$g['varetc_path']}/ssh/sshd_config", "w");
	if (!$fd) {
		$message = "Error: Failed to create '{$g['varetc_path']}/ssh/sshd_config'";
		write_console($message . ".\n");
		write_log($message);
		return 1;
	}

	$sshdconfig = <<<EOD
SyslogFacility LOCAL3
Protocol 2
UseDNS no
PasswordAuthentication yes
PubkeyAuthentication yes
Subsystem       sftp    /usr/libexec/sftp-server

EOD;

	if (isset($config['sshd']['port'])) {
		$sshdconfig .= <<<EOD
Port {$config['sshd']['port']}

EOD;
	}

  if (isset($config['sshd']['permitrootlogin'])) {
		$sshdconfig .= <<<EOD
PermitRootLogin yes

EOD;
	}

	if (isset($config['sshd']['tcpforwarding'])) {
		$sshdconfig .= <<<EOD
AllowTcpForwarding yes

EOD;
	}

	fwrite($fd, $sshdconfig);
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
		$message = "Error: Failed to create '{$g['varetc_path']}/ssh/ssh_host_dsa_key'";
		write_console($message . ".\n");
		write_log($message);
	} else {
		// Restrict right on the SSH key.
		mwexec("/bin/chmod 0600 {$g['varetc_path']}/ssh/ssh_host_dsa_key");
	}

	fwrite($fd, $key);
	fclose($fd);

	return 0;
}
?>
