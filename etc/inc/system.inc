<?php
/*
	system.inc
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
require_once("rc.inc");

function system_stop_common() {
	/* Stop swap */
	rc_stop_service("swap");

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

function system_set_harddisk_standby() {
	global $g, $config;

	if (($g['platform'] != "embedded") || ($g['platform'] != "full"))
		return;

	if (isset($config['system']['harddiskstandby']) &&
			($config['system']['harddiskstandby'] > 0)) {
		if (is_booting()) {
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
				if (is_booting()) {
					write_console("done\n");
				}
			} else if (is_booting()) {
				write_console("failed\n");
			}
		} else if (is_booting()) {
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

	if (is_booting())
		write_console("Generating user/password database(s)... ");

	$result |= system_create_masterpasswd();
	$result |= system_create_group();
	$result |= system_create_pwdmkdb();
	$result |= system_create_htpasswd();

	if (0 == $result) {
		if(is_booting())
			write_console("done\n");
		write_log("User/password database(s) created");
	} else {
		if(is_booting())
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
?>
