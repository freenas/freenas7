#!/usr/local/bin/php
<?php 
/*
	system_firmware.php
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
$d_isfwfile = 1;

require("guiconfig.inc"); 

$pgtitle = array(gettext("System"), gettext("Firmware"));

/* checks with www.freenas.org to see if a newer firmware version is available;
   returns any HTML message it gets from the server */
function check_firmware_version() {
	global $g;
	$post = "platform=" . rawurlencode($g['fullplatform']) . 
		"&version=" . rawurlencode(trim(file_get_contents("/etc/version")));
		
	$rfd = @fsockopen($g['product_url'], 80, $errno, $errstr, 3);
	if ($rfd) {
		$hdr = "POST /checkversion.php HTTP/1.0\r\n";
		$hdr .= "Content-Type: application/x-www-form-urlencoded\r\n";
		$hdr .= "User-Agent: {$g['product_name']}-webGUI/1.0\r\n";
		$hdr .= "Host: {$g['product_url']}\r\n";
		$hdr .= "Content-Length: " . strlen($post) . "\r\n\r\n";
		
		fwrite($rfd, $hdr);
		fwrite($rfd, $post);
		
		$inhdr = true;
		$resp = "";
		while (!feof($rfd)) {
			$line = fgets($rfd);
			if ($inhdr) {
				if (trim($line) == "")
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
	
	if (stristr($_POST['Submit'], gettext("Enable firmware upload'")))
		$mode = "enable";
	else if (stristr($_POST['Submit'], gettext("Disable firmware upload")))
		$mode = "disable";
	else if (stristr($_POST['Submit'], gettext("Upgrade firmware")) || $_POST['sig_override'])
		$mode = "upgrade";
	else if ($_POST['sig_no'])
		unlink("{$g['ftmp_path']}/firmware.img");
		
	if ($mode) {
		if ($mode == "enable") {
			exec_rc_script("/etc/rc.firmware enable");
			touch($d_fwupenabled_path);
		} else if ($mode == "disable") {
			exec_rc_script("/etc/rc.firmware disable");
			if (file_exists($d_fwupenabled_path))
				unlink($d_fwupenabled_path);
		} else if ($mode == "upgrade") {
			if (is_uploaded_file($_FILES['ulfile']['tmp_name'])) {
				/* verify firmware image(s) */
				if (!stristr($_FILES['ulfile']['name'], $g['fullplatform']) && !$_POST['sig_override'])
					$input_errors[] = gettext("The uploaded image file is not for this platform")." ({$g['fullplatform']}).";
				else if (!file_exists($_FILES['ulfile']['tmp_name'])) {
					/* probably out of memory for the MFS */
					$input_errors[] = gettext("Image upload failed (out of memory?)");
					exec_rc_script("/etc/rc.firmware disable");
					if (file_exists($d_fwupenabled_path))
						unlink($d_fwupenabled_path);
				} else {
					/* move the image so PHP won't delete it */
					rename($_FILES['ulfile']['tmp_name'], "{$g['ftmp_path']}/firmware.img");
					
					/* Remove the check digital signature */
					/* $sigchk = verify_digital_signature("{$g['ftmp_path']}/firmware.img"); */
					$sigchk = 0;
					
					if ($sigchk == 1)
						$sig_warning = gettext("The digital signature on this image is invalid.");
					else if ($sigchk == 2)
						$sig_warning = gettext("This image is not digitally signed.");
					else if (($sigchk == 3) || ($sigchk == 4))
						$sig_warning = gettext("There has been an error verifying the signature on this image.");
				
					if (!verify_gzip_file("{$g['ftmp_path']}/firmware.img")) {
						$input_errors[] = gettext("The image file is corrupt");
						unlink("{$g['ftmp_path']}/firmware.img");
					}
				}
			}

			if (!$input_errors && !file_exists($d_firmwarelock_path) && (!$sig_warning || $_POST['sig_override'])) {			
				/* fire up the update script in the background */
				touch($d_firmwarelock_path);
				exec_rc_script_async("/etc/rc.firmware upgrade {$g['ftmp_path']}/firmware.img");
				
				$savemsg = sprintf(gettext("The firmware is now being installed. %s will reboot automatically."), $g['product_name']);
			}
		}
	}
} else {
	if (!isset($config['system']['disablefirmwarecheck']))
		$fwinfo = check_firmware_version();
}
?>
<?php include("fbegin.inc"); ?>
<?php if ($input_errors) print_input_errors($input_errors); ?>
<?php if ($savemsg) print_info_box($savemsg); ?>
<?php if ($fwinfo) echo $fwinfo; ?>
<?php if (!in_array($g['platform'], $fwupplatforms)): ?>
<p><strong><?=gettext("Firmware uploading is not supported on this platform.");?></strong></p>
<?php elseif ($sig_warning && !$input_errors): ?>
<form action="system_firmware.php" method="post">
<?php 
$sig_warning = "<strong>" . $sig_warning . "</strong><br>".gettext("This means that the image you uploaded is not an official/supported image and may lead to unexpected behavior or security compromises. Only install images that come from sources that you trust, and make sure that the image has not been tampered with.<br><br>Do you want to install this image anyway (on your own risk)?");
print_info_box($sig_warning);
?>
<input name="sig_override" type="submit" class="formbtn" id="sig_override" value=" Yes ">
<input name="sig_no" type="submit" class="formbtn" id="sig_no" value=" No ">
</form>
<?php else: ?>
<?php if (!file_exists($d_firmwarelock_path)): ?>
<p><?=gettext("Click &quot;Enable firmware upload&quot; below, then choose the image file to be uploaded.<br>Click &quot;Upgrade firmware&quot; to start the upgrade process.');");?></p>
<form action="system_firmware.php" method="post" enctype="multipart/form-data">
  <table>
    <tr> 
      <td> 
        <?php if (!file_exists($d_sysrebootreqd_path)): ?>
				<?php if (!file_exists($d_fwupenabled_path)): ?>
				<input name="Submit" id="Enable" type="submit" class="formbtn" value="<?=gettext("Enable firmware upload'");?>">
				<?php else: ?>
				<input name="Submit" id="Disable" type="submit" class="formbtn" value="<?=gettext("Disable firmware upload");?>">
				<br><br>
				<strong><?=gettext("Firmware image file'");?> </strong>&nbsp;<input name="ulfile" type="file" class="formfld">
				<br><br>
				<input name="Submit" id="Upgrade" type="submit" class="formbtn" value="<?=gettext("Upgrade firmware");?>">
				<?php endif; else: ?>
				<strong><?=gettext("You must reboot the system before you can upgrade the firmware.");?></strong>
				<?php endif; ?>
      </td>
    </tr>
    <tr>
      <td>
				<span class="vexpl"><span class="red"><strong><?=gettext("Warning");?>:</strong></span><br>
				<?php echo sprintf( gettext("DO NOT abort the firmware upgrade once it has started. %s will reboot automatically after storing the new firmware. The configuration will be maintained.<br>You need a minium of %d Mb RAM to perform the firmware update.<br>It is strongly recommended that you <a href=%s>Backup</a> the System configuration before doing a Firmware upgrade."), $g['product_name'], 128, "diag_backup.php");?></span>
			</td>
    </tr>
  </table>
</form>
<?php endif; endif; ?>
<?php include("fend.inc"); ?>
