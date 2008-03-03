<?php
/*
	guiconfig.inc
	part of FreeNAS (http://www.freenas.org)
	Copyright (C) 2005-2008 Olivier Cochard-Labbe <olivier@freenas.org>.
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
require_once("rc.inc");

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
$d_iscsitargetdirty_path = $g['varrun_path'] . "/iscsitarget.dirty";
$d_rsyncclientdirty_path = $g['varrun_path'] . "/rsyncclient.dirty";
$d_rsynclocaldirty_path = $g['varrun_path'] . "/rsynclocal.dirty";
$d_raid_graid5_confdirty_path = $g['varrun_path'] . "/raid_graid5.conf.dirty";
$d_raid_gmirror_confdirty_path = $g['varrun_path'] . "/raid_gmirror.conf.dirty";
$d_raid_gconcat_confdirty_path = $g['varrun_path'] . "/raid_gconcat.conf.dirty";
$d_raid_gvinum_confdirty_path = $g['varrun_path'] . "/raid_gvinum.conf.dirty";
$d_raid_gstripe_confdirty_path = $g['varrun_path'] . "/raid_gstripe.conf.dirty";
$d_userconfdirty_path = $g['varrun_path'] . "/user.conf.dirty";
$d_groupconfdirty_path = $g['varrun_path'] . "/group.conf.dirty";
$d_smbconfdirty_path = $g['varrun_path'] . "/smb.conf.dirty";
$d_smbshareconfdirty_path = $g['varrun_path'] . "/smbshare.conf.dirty";
$d_upnpconfdirty_path = $g['varrun_path'] . "/upnp.conf.dirty";
$d_nfsconfdirty_path = $g['varrun_path'] . "/nfs.conf.dirty";
$d_landirty_path = $g['varrun_path'] . "/lan.dirty";
$d_packagesconfdirty_path = $g['varrun_path'] . "/packages.conf.dirty";
$d_cronconfdirty_path = $g['varrun_path'] . "/cron.conf.dirty";
$d_afpconfdirty_path = $g['varrun_path'] . "/afp.conf.dirty";
$d_rsyncdconfdirty_path = $g['varrun_path'] . "/rsyncd.conf.dirty";
$d_nfsconfdirty_path = $g['varrun_path'] . "/nfs.conf.dirty";

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
$fwupplatforms = array('net45xx', 'net48xx', 'embedded', 'wrap', 'full');

function do_input_validation($postdata, $reqdfields, $reqdfieldsn, $input_errors) {
	/* check for bad control characters */
	foreach ($postdata as $pn => $pd) {
		if (is_string($pd) && preg_match("/[\\x00-\\x08\\x0b\\x0c\\x0e-\\x1f]/", $pd)) {
			$input_errors[] = sprintf( gettext("The attribute '%s' contains invalid characters."), $pn);
		}
	}

	for ($i = 0; $i < count($reqdfields); $i++) {
		if (!isset($postdata[$reqdfields[$i]]) || ($postdata[$reqdfields[$i]] === "")) {
			$input_errors[] = sprintf( gettext("The attribute '%s' is required."), $reqdfieldsn[$i]);
		}
	}
}

/* Validate attribute type. */
function do_input_validation_type($postdata, $reqdfields, $reqdfieldsn, $reqdfieldst, $input_errors) {
	/* Validate type. */
	for ($i = 0; $i < count($reqdfields); $i++) {
		if (isset($postdata[$reqdfields[$i]]) && ($postdata[$reqdfields[$i]] !== "")) {
			$valid = false;
			$message = "";

			switch($reqdfieldst[$i]) {
				case 'string':			$valid = is_string($postdata[$reqdfields[$i]]);
														$message = sprintf( gettext("The attribute '%s' must be a string."), $reqdfieldsn[$i]);
														break;
				case 'numeric':			$valid = is_numeric($postdata[$reqdfields[$i]]);
														$message = sprintf( gettext("The attribute '%s' must be a number."), $reqdfieldsn[$i]);
														break;
				case 'numericint':	$valid = is_numericint($postdata[$reqdfields[$i]]);
														$message = sprintf( gettext("The attribute '%s' must be a number."), $reqdfieldsn[$i]);
														break;
				case 'ipaddr':			$valid = is_ipaddr($postdata[$reqdfields[$i]]);
														$message = sprintf( gettext("The attribute '%s' is no valid IP address."), $reqdfieldsn[$i]);
														break;
				case 'macaddr': 		$valid = is_macaddr($postdata[$reqdfields[$i]]);
														$message = sprintf( gettext("The attribute '%s' is no valid MAC address."), $reqdfieldsn[$i]);
														break;
				case 'subnet': 			$valid = is_subnet($postdata[$reqdfields[$i]]);
														$message = sprintf( gettext("The attribute '%s' is no valid subnet mask."), $reqdfieldsn[$i]);
														break;
				case 'domain': 			$valid = is_domain($postdata[$reqdfields[$i]]);
														$message = sprintf( gettext("The attribute '%s' contains invalid characters and is no valid domain name."), $reqdfieldsn[$i]);
														break;
				case 'hostname': 		$valid = is_hostname($postdata[$reqdfields[$i]]);
														$message = sprintf( gettext("The attribute '%s' contains invalid characters and is no valid host name."), $reqdfieldsn[$i]);
														break;
				case 'workgroup': 	$valid = is_workgroup($postdata[$reqdfields[$i]]);
														$message = sprintf( gettext("The attribute '%s' contains invalid characters and is no valid workgroup name."), $reqdfieldsn[$i]);
														break;
				case 'filemode': 		$valid = is_filemode($postdata[$reqdfields[$i]]);
														$message = sprintf( gettext("The attribute '%s' is no valid file mode mask."), $reqdfieldsn[$i]);
														break;
				case 'mtu': 				$valid = is_mtu($postdata[$reqdfields[$i]]);
														$message = sprintf( gettext("The attribute '%s' is invalid."), $reqdfieldsn[$i]);
														break;
				case 'port': 				$valid = is_port($postdata[$reqdfields[$i]]);
														$message = sprintf( gettext("The attribute '%s' is an invalid port number."), $reqdfieldsn[$i]);
														break;
				case 'password':		$valid = is_validpassword($postdata[$reqdfields[$i]]);
														$message = sprintf( gettext("The attribute '%s' contains the illegal character ':' and is no valid password."), $reqdfieldsn[$i]);
														break;
			}
			if (!$valid) {
				$input_errors[] = $message;
			}
		}
	}
}

function print_input_errors($input_errors) {
	echo "<p>\n";
	echo "  <div id='errorbox'>\n";
	echo "    <table border=\"0\" cellspacing=\"0\" cellpadding=\"4\" width=\"100%\">\n";
	echo "      <tr>\n";
	echo "        <td class=\"icon\" align=\"center\" valign=\"top\"><img src=\"/err.gif\"></td>\n";
	echo "        <td class=\"message\">\n";
	echo "          <p>" . gettext("The following input errors were detected").":\n";
	echo "            <ul>\n";
	foreach ($input_errors as $msg) {
		echo "              <li>{$msg}</li>\n";
	}
	echo "            </ul>\n";
	echo "          </p>\n";
	echo "        </td>\n";
	echo "      </tr>\n";
	echo "    </table>\n";
	echo "  </div>\n";
	echo "</p>\n";
}

function verify_gzip_file($fname) {
	$returnvar = mwexec("/usr/bin/gzip -t " . escapeshellarg($fname));
	if ($returnvar != 0)
		return 0;
	else
		return 1;
}

function print_info_box_np($msg) {
	echo "<div id='infobox'>\n";
	echo "  <table border=\"0\" cellspacing=\"0\" cellpadding=\"4\" width=\"100%\">\n";
	echo "    <tr>\n";
	echo "      <td class=\"icon\" align=\"center\" valign=\"top\"><img src=\"/exclam.gif\"></td>\n";
	echo "      <td class=\"message\">{$msg}</td>\n";
	echo "    </tr>\n";
	echo "  </table>\n";
	echo "</div>\n";
}

function print_info_box($msg) {
	echo "<p>";
	print_info_box_np($msg);
	echo "</p>";
}

function print_error_box_np($msg) {
	echo "<div id='errorbox'>\n";
	echo "  <table border=\"0\" cellspacing=\"0\" cellpadding=\"4\" width=\"100%\">\n";
	echo "    <tr>\n";
	echo "      <td class=\"icon\" align=\"center\" valign=\"top\"><img src=\"/err.gif\"></td>\n";
	echo "      <td class=\"message\">{$msg}</td>\n";
	echo "    </tr>\n";
	echo "  </table>\n";
	echo "</div>\n";
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
		if (file_exists($d_sysrebootreqd_path)) {
			return sprintf( gettext("The changes have been saved. You have to <a href=%s>reboot</a> the system for the changes to take effect."), "reboot.php");
		}
		else {
			return gettext("The changes have been applied successfully.");
		}
	} else {
		return sprintf( gettext("Error: The changes could not be applied (error code %s)."), $ok);
	}
}
?>
