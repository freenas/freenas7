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
#Comment netatalk vfs, because it crash Samba
#vfs objects = netatalk

EOD;
			}
		}
	}

	fwrite($fd, $sambaconf);
	fclose($fd);

	return 0;
}
?>
