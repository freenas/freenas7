<?php
/*
	guiconfig.inc
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

/* make sure nothing is cached */
if (!$omit_nocacheheaders) {
	header("Expires: 0");
	header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
	header("Cache-Control: no-store, no-cache, must-revalidate");
	header("Cache-Control: post-check=0, pre-check=0", false);
	header("Pragma: no-cache");
}

/* parse the configuration and include all configuration functions */
require_once("config.inc");
require_once("functions.inc");

/* Set the current language */
system_language_load();

$d_staticmapsdirty_path = $g['varrun_path'] . "/staticmaps.dirty";
$d_staticroutesdirty_path = $g['varrun_path'] . "/staticroutes.dirty";
$d_hostsdirty_path = $g['varrun_path'] . "/hosts.dirty";
$d_fwupenabled_path = $g['varrun_path'] . "/fwup.enabled";
$d_firmwarelock_path = $g['varrun_path'] . "/firmware.lock";
$d_sysrebootreqd_path = $g['varrun_path'] . "/sysreboot.reqd";
$d_allowedipsdirty_path = $g['varrun_path'] . "/allowedips.dirty";

$d_mountdirty_path = $g['varrun_path'] . "/mount.dirty";
$d_diskdirty_path = $g['varrun_path'] . "/disk.dirty";
$d_gelidirty_path = $g['varrun_path'] . "/geli.dirty";
$d_iscsiinitdirty_path = $g['varrun_path'] . "/iscsiinit.dirty";
$d_raidconfdirty_path = $g['varrun_path'] . "/raid.conf.dirty";
$d_userconfdirty_path = $g['varrun_path'] . "/user.conf.dirty";
$d_groupconfdirty_path = $g['varrun_path'] . "/group.conf.dirty";
$d_smbshareconfdirty_path = $g['varrun_path'] . "/smbshare.conf.dirty";
$d_upnpconfdirty_path = $g['varrun_path'] . "/upnp.conf.dirty";
$d_landirty_path = $g['varrun_path'] . "/lan.dirty";

if (file_exists($d_firmwarelock_path)) {
	if (!$d_isfwfile) {
		header("Location: system_firmware.php");
		exit;
	} else {
		return;
	}
}

/* Reserverd login names */
$reservedlogin = array("root", "toor", "daemon", "operator", "bin", "tty", "kmem", "www", "nobody", "ftp", "sshd");

/* TCP flags */
$tcpflags = array("fin", "syn", "rst", "psh", "ack", "urg");

/* platforms that support firmware updating */
$fwupplatforms = array('net45xx', 'net48xx', 'generic-pc', 'wrap');

function do_input_validation($postdata, $reqdfields, $reqdfieldsn, $input_errors) {
	
	/* check for bad control characters */	
	foreach ($postdata as $pn => $pd) {
		if (is_string($pd) && preg_match("/[\\x00-\\x08\\x0b\\x0c\\x0e-\\x1f]/", $pd)) {
			$input_errors[] = sprintf( gettext("The attribute '%s' contains invalid characters."), $pn);
		}
	}
	
	for ($i = 0; $i < count($reqdfields); $i++) {
		if (!isset($_POST[$reqdfields[$i]]) || ($_POST[$reqdfields[$i]] === "")) {
			$input_errors[] = sprintf( gettext("The attribute '%s' is required."), $reqdfieldsn[$i]);
		}
	}	
}

function print_input_errors($input_errors) {
	echo "<p><table border=\"0\" cellspacing=\"0\" cellpadding=\"4\" width=\"100%\">\n";
	echo "<tr><td bgcolor=\"#A12A2A\" width=\"36\" align=\"center\" valign=\"top\"><img src=\"/err.gif\" width=\"28\" height=\"32\"></td>\n";
	echo "<td bgcolor=\"#FFD9D1\" style=\"padding-left: 8px; padding-top: 6px\">";
	
	echo "<span class=\"errmsg\"><p>".gettext("The following input errors were detected").":<ul>\n";
	foreach ($input_errors as $ierr) {
		echo "<li>" . $ierr . "</li>\n";
	}
	echo "</ul></span>";
	echo "</td></tr></table></p>";
}

function exec_rc_script($scriptname) {
	
	global $d_sysrebootreqd_path;
	
	if (file_exists($d_sysrebootreqd_path))
		return 0;
	
	exec($scriptname . " >/dev/null 2>&1", $execoutput, $retval);
	return $retval; 
}

function exec_rc_script_async($scriptname) {
	
	global $d_sysrebootreqd_path;
	
	if (file_exists($d_sysrebootreqd_path))
		return 0;
		
	exec("nohup " . $scriptname . " >/dev/null 2>&1 &", $execoutput, $retval);
	return $retval; 
}

function verify_gzip_file($fname) {

	$returnvar = mwexec("/usr/bin/gunzip -S \"\" -t " . escapeshellarg($fname));
	if ($returnvar != 0)
		return 0;
	else
		return 1;
}

function print_info_box_np($msg) {
	echo "<table border=\"0\" cellspacing=\"0\" cellpadding=\"4\" width=\"100%\">\n";
	echo "<tr><td bgcolor=\"#687BA4\" align=\"center\" valign=\"top\" width=\"36\"><img src=\"/exclam.gif\" width=\"28\" height=\"32\"></td>\n";
	echo "<td bgcolor=\"#D9DEE8\" style=\"padding-left: 8px\">";
	echo $msg;
	echo "</td></tr></table>";
}

function print_info_box($msg) {
	echo "<p>";
	print_info_box_np($msg);
	echo "</p>";
}

function print_error_box_np($msg) {
	echo "<table border=\"0\" cellspacing=\"0\" cellpadding=\"4\" width=\"100%\">\n";
	echo "<tr><td bgcolor=\"#A12A2A\" width=\"36\" align=\"center\" valign=\"top\"><img src=\"/err.gif\" width=\"28\" height=\"32\"></td>\n";
	echo "<td bgcolor=\"#FFD9D1\" style=\"padding-left: 8px; padding-top: 6px\">";
	echo "<span class=\"errmsg\">";
	echo $msg;
	echo "</span>";
	echo "</td></tr></table>";
}

function print_error_box($msg) {
	echo "<p>";
	print_error_box_np($msg);
	echo "</p>";
}

function format_bytes($bytes) {
	if ($bytes >= 1073741824) {
		return sprintf("%.2f GB", $bytes/1073741824);
	} else if ($bytes >= 1048576) {
		return sprintf("%.2f MB", $bytes/1048576);
	} else if ($bytes >= 1024) {
		return sprintf("%.0f KB", $bytes/1024);
	} else {
		return sprintf("%d bytes", $bytes);
	}
}

function get_std_save_message($ok) {
	global $d_sysrebootreqd_path;

	if ($ok == 0) {
		if (file_exists($d_sysrebootreqd_path))
			return sprintf( gettext("The changes have been saved. You must <a href=%s>reboot</a> your NAS for changes to take effect."), "reboot.php");
		else
			return gettext("The changes have been applied successfully.");
	} else {
		return sprintf( gettext("ERROR: The changes could not be applied (error code %s)."), $ok);
	}
}

/* Sort functions */
function staticroutes_sort() {
	global $g, $config;

	function staticroutecmp($a, $b) {
		return strcmp($a['network'], $b['network']);
	}

	usort($config['staticroutes']['route'], "staticroutecmp");
}

function staticmaps_sort($if) {
	global $g, $config;

	function staticmapcmp($a, $b) {
		return ipcmp($a['ipaddr'], $b['ipaddr']);
	}

	usort($config['dhcpd'][$if]['staticmap'], "staticmapcmp");
}

function aliases_sort() {
	global $g, $config;

	function aliascmp($a, $b) {
		return strcmp($a['name'], $b['name']);
	}

	usort($config['aliases']['alias'], "aliascmp");
}

function mount_sort() {
	global $g, $config;

	function mountcmp($a, $b) {
		return strcmp($a['mdisk'], $b['mdisk']);
	}

	usort($config['mounts']['mount'], "mountcmp");
}

function disks_sort() {
	global $g, $config;

	function diskcmp($a, $b) {
		return strcmp($a['name'], $b['name']);
	}

	usort($config['disks']['disk'], "diskcmp");
}

function gvinum_sort() {
	global $g, $config;

	function gvinumcmp($a, $b) {
		return strcmp($a['name'], $b['name']);
	}

	usort($config['gvinum']['vdisk'], "gvinumcmp");
}

function gmirror_sort() {
	global $g, $config;

	function gmirrorcmp($a, $b) {
		return strcmp($a['name'], $b['name']);
	}

	usort($config['gmirror']['vdisk'], "gmirrorcmp");
}

function gconcat_sort() {
	global $g, $config;

	function gconcatcmp($a, $b) {
		return strcmp($a['name'], $b['name']);
	}

	usort($config['gconcat']['vdisk'], "gconcatcmp");
}

function gstripe_sort() {
	global $g, $config;

	function gstripecmp($a, $b) {
		return strcmp($a['name'], $b['name']);
	}

	usort($config['gstripe']['vdisk'], "gstripecmp");
}

function graid5_sort() {
	global $g, $config;

	function graid5cmp($a, $b) {
		return strcmp($a['name'], $b['name']);
	}

	usort($config['graid5']['vdisk'], "graid5cmp");
}

function geli_sort() {
	global $g, $config;

	function gelicmp($a, $b) {
		return strcmp($a['fullname'], $b['fullname']);
	}

	usort($config['geli']['vdisk'], "gelicmp");
}

function iscsiinit_sort() {
	global $g, $config;

	function iscsiinitcmp($a, $b) {
		return strcmp($a['name'], $b['name']);
	}

	usort($config['iscsiinit']['vdisk'], "iscsiinitcmp");
}



function users_sort() {
	global $g, $config;

	function userscmp($a, $b) {
		return strcmp($a['login'], $b['login']);
	}

	usort($config['access']['user'], "userscmp");
}

function hosts_sort() {
	global $g, $config;

	function userscmp($a, $b) {
		return strcmp($a['name'], $b['name']);
	}

	usort($config['system']['hosts'], "userscmp");
}

function groups_sort() {
	global $g, $config;

	function groupscmp($a, $b) {
		return strcmp($a['name'], $b['name']);
	}

	usort($config['access']['group'], "groupscmp");
}
?>
