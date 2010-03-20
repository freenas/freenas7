#!/usr/local/bin/php
<?php
/*
	system_firmware.php
	part of FreeNAS (http://www.freenas.org)
	Copyright (C) 2005-2010 Olivier Cochard-Labbe <olivier@freenas.org>.
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
$d_isfwfile = 1;

require("auth.inc");
require("guiconfig.inc");

$pgtitle = array(gettext("System"), gettext("Firmware"));

/* checks with www.freenas.org to see if a newer firmware version is available;
   returns any HTML message it gets from the server */
function check_firmware_version() {
	global $g;

	$post = "platform=".rawurlencode($g['fullplatform'])."&version=".rawurlencode(get_product_version());

	$rfd = @fsockopen("www.".get_product_url(), 80, $errno, $errstr, 3);
	if ($rfd) {
		$hdr = "POST /checkversion.php HTTP/1.0\r\n";
		$hdr .= "Content-Type: application/x-www-form-urlencoded\r\n";
		$hdr .= "User-Agent: ".get_product_name()."-webGUI/1.0\r\n";
		$hdr .= "Host: ".get_product_url()."\r\n";
		$hdr .= "Content-Length: ".strlen($post)."\r\n\r\n";

		fwrite($rfd, $hdr);
		fwrite($rfd, $post);

		$inhdr = true;
		$resp = "";
		while (!feof($rfd)) {
			$line = fgets($rfd);
			if ($inhdr) {
				if (trim($line) === "")
					$inhdr = false;
			} else {
				$resp .= $line;
			}
		}

		fclose($rfd);

		return $resp;
	}

	return null;
}

if ($_POST && !file_exists($d_firmwarelock_path)) {
	unset($input_errors);
	unset($sig_warning);

	if (stristr($_POST['Submit'], gettext("Enable firmware upload")))
		$mode = "enable";
	else if (stristr($_POST['Submit'], gettext("Disable firmware upload")))
		$mode = "disable";
	else if (stristr($_POST['Submit'], gettext("Upgrade firmware")) || $_POST['sig_override'])
		$mode = "upgrade";
	else if ($_POST['sig_no'])
		unlink("{$g['ftmp_path']}/firmware.img");

	if ($mode) {
		if ($mode === "enable") {
			$retval = rc_exec_script("/etc/rc.firmware enable");
			if (0 == $retval) {
				touch($d_fwupenabled_path);
			} else {
				$input_errors[] = gettext("Failed to create in-memory file system.");
			}
		} else if ($mode === "disable") {
			rc_exec_script("/etc/rc.firmware disable");
			if (file_exists($d_fwupenabled_path))
				unlink($d_fwupenabled_path);
		} else if ($mode === "upgrade") {
			if (is_uploaded_file($_FILES['ulfile']['tmp_name'])) {
				/* verify firmware image(s) */
				if (!stristr($_FILES['ulfile']['name'], $g['fullplatform']) && !$_POST['sig_override'])
					$input_errors[] = gettext("The uploaded image file is not for this platform")." ({$g['fullplatform']}).";
				else if (!file_exists($_FILES['ulfile']['tmp_name'])) {
					/* probably out of memory for the MFS */
					$input_errors[] = gettext("Image upload failed (out of memory?)");
				} else {
					/* move the image so PHP won't delete it */
					move_uploaded_file($_FILES['ulfile']['tmp_name'], "{$g['ftmp_path']}/firmware.img");

					if (!verify_gzip_file("{$g['ftmp_path']}/firmware.img")) {
						$input_errors[] = gettext("The image file is corrupt");
						unlink("{$g['ftmp_path']}/firmware.img");
					}
				}
			}

			// Cleanup if there were errors.
			if ($input_errors) {
				rc_exec_script("/etc/rc.firmware disable");
				unlink_if_exists($d_fwupenabled_path);
			}

			// Upgrade firmware if there were no errors.
			if (!$input_errors && !file_exists($d_firmwarelock_path) && (!$sig_warning || $_POST['sig_override'])) {
				touch($d_firmwarelock_path);

				switch($g['platform']) {
					case "embedded":
						rc_exec_script_async("/etc/rc.firmware upgrade {$g['ftmp_path']}/firmware.img");
						break;

					case "full":
						rc_exec_script_async("/etc/rc.firmware fullupgrade {$g['ftmp_path']}/firmware.img");
						break;
				}

				$savemsg = sprintf(gettext("The firmware is now being installed. %s will reboot automatically."), get_product_name());

				// Clean firmwarelock: Permit to force all pages to be redirect on the firmware page.
				if (file_exists($d_firmwarelock_path))
					unlink($d_firmwarelock_path);

				// Clean fwupenabled: Permit to know if the ram drive /ftmp is created.
				if (file_exists($d_fwupenabled_path))
					unlink($d_fwupenabled_path);
			}
		}
	}
} else {
	if (!isset($config['system']['disablefirmwarecheck']))
		$fwinfo = check_firmware_version();
}
?>
<?php include("fbegin.inc");?>
<table width="100%" border="0" cellpadding="0" cellspacing="0">
  <tr>
    <td class="tabcont">
			<?php if ($input_errors) print_input_errors($input_errors); ?>
			<?php if ($savemsg) print_info_box($savemsg); ?>
			<?php if ($fwinfo) echo "{$fwinfo}<br />";?>
			<?php if (!in_array($g['platform'], $fwupplatforms)): ?>
			<?php print_error_box(gettext("Firmware uploading is not supported on this platform."));?>
			<?php elseif ($sig_warning && !$input_errors): ?>
			<form action="system_firmware.php" method="post">
			<?php
			$sig_warning = "<strong>" . $sig_warning . "</strong><br />".gettext("This means that the image you uploaded is not an official/supported image and may lead to unexpected behavior or security compromises. Only install images that come from sources that you trust, and make sure that the image has not been tampered with.<br /><br />Do you want to install this image anyway (on your own risk)?");
			print_info_box($sig_warning);
			?>
			<input name="sig_override" type="submit" class="formbtn" id="sig_override" value=" Yes ">
			<input name="sig_no" type="submit" class="formbtn" id="sig_no" value=" No ">
			<?php include("formend.inc");?>
			</form>
			<?php else:?>
			<?php if (!file_exists($d_firmwarelock_path)):?>
			<?=gettext("Click &quot;Enable firmware upload&quot; below, then choose the image file to be uploaded.<br />Click &quot;Upgrade firmware&quot; to start the upgrade process.");?>
			<form action="system_firmware.php" method="post" enctype="multipart/form-data">
				<?php if (!file_exists($d_sysrebootreqd_path)):?>
					<?php if (!file_exists($d_fwupenabled_path)):?>
					<div id="submit">
					<input name="Submit" id="Enable" type="submit" class="formbtn" value="<?=gettext("Enable firmware upload");?>" />
					</div>
					<?php else:?>
					<div id="submit">
					<input name="Submit" id="Disable" type="submit" class="formbtn" value="<?=gettext("Disable firmware upload");?>" />
					</div>
					<div id="submit">
					<strong><?=gettext("Firmware image file");?> </strong>&nbsp;<input name="ulfile" type="file" class="formfld" size="40" />
					</div>
					<div id="submit">
					<input name="Submit" id="Upgrade" type="submit" class="formbtn" value="<?=gettext("Upgrade firmware");?>" />
					</div>
					<?php endif;?>
				<?php else:?>
				<strong><?=gettext("You must reboot the system before you can upgrade the firmware.");?></strong>
				<?php endif;?>
				<div id="remarks">
					<?php html_remark("warning", gettext("Warning"), sprintf(gettext("DO NOT abort the firmware upgrade once it has started. %s will reboot automatically after storing the new firmware. The configuration will be maintained.<br />You need a minimum of %d Mb RAM to perform the firmware update.<br />It is strongly recommended that you <a href=\"%s\">Backup</a> the System configuration before doing a Firmware upgrade."), get_product_name(), 192, "system_backup.php"));?>
				</div>
				<?php include("formend.inc");?>
			</form>
			<?php endif; endif;?>
		</td>
	</tr>
</table>
<?php include("fend.inc");?>
