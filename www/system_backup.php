#!/usr/local/bin/php
<?php
/*
	system_backup.php
	part of FreeNAS (http://www.freenas.org)
	Copyright (C) 2005-2007 Olivier Cochard-Labb� <olivier@freenas.org>.
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
require("guiconfig.inc");

$pgtitle = array(gettext("System"), gettext("Backup/Restore"));

/* omit no-cache headers because it confuses IE with file downloads */
$omit_nocacheheaders = true;

if ($_POST) {
	unset($input_errors);

	if (0 == strcmp($_POST['Submit'], gettext("Restore configuration")))
		$mode = "restore";
	else if (0 == strcmp($_POST['Submit'], gettext("Download configuration")))
		$mode = "download";

	if ($mode) {
		if ($mode == "download") {
			config_lock();

			$fn = "config-" . $config['system']['hostname'] . "." . $config['system']['domain'] . "-" . date("YmdHis") . ".xml";
			$fs = filesize($g['conf_path'] . "/config.xml");

			header("Content-Type: application/octet-stream");
			header("Content-Disposition: attachment; filename=$fn");
			header("Content-Length: $fs");
			readfile($g['conf_path'] . "/config.xml");
			config_unlock();

			exit;
		} else if ($mode == "restore") {
			if (is_uploaded_file($_FILES['conffile']['tmp_name'])) {
				if (config_install($_FILES['conffile']['tmp_name']) == 0) {
					system_reboot();
					$savemsg = sprintf(gettext("The configuration has been restored. %s is now rebooting."), get_product_name());
				} else {
					$input_errors[] = gettext("The configuration could not be restored.");
				}
			} else {
				$input_errors[] = gettext("The configuration could not be restored (file upload error).");
			}
		}
	}
}
?>
<?php include("fbegin.inc");?>
<form action="system_backup.php" method="post" enctype="multipart/form-data">
	<table width="100%" border="0" cellpadding="0" cellspacing="0">
	  <tr>
	    <td class="tabcont">
				<?php if ($input_errors) print_input_errors($input_errors); ?>
				<?php if ($savemsg) print_info_box($savemsg); ?>
			  <table width="100%" border="0" cellspacing="0" cellpadding="6">
			    <tr>
			      <td colspan="2" class="listtopic"><?=gettext("Backup configuration");?></td>
			    </tr>
			    <tr>
			      <td width="22%" valign="baseline" class="vncell">&nbsp;</td>
			      <td width="78%" class="vtable">
							<?=gettext("Click this button to download the system configuration in XML format.");?><br><br>
							<input name="Submit" type="submit" class="formbtn" id="download" value="<?=gettext("Download configuration");?>">
						</td>
			    </tr>
			    <tr>
			      <td colspan="2" class="list" height="12"></td>
			    </tr>
			    <tr>
			      <td colspan="2" class="listtopic"><?=gettext("Restore configuration");?></td>
			    </tr>
			    <tr>
			      <td width="22%" valign="baseline" class="vncell">&nbsp;</td>
			      <td width="78%" class="vtable">
							<?php echo sprintf(gettext("Open a %s configuration XML file and click the button below to restore the configuration."), get_product_name());?><br><br>
							<strong><span class="red"><?=gettext("Note");?>:</span></strong><br>
							<?php echo sprintf(gettext("%s will reboot after restoring the configuration."), get_product_name());?><br><br>
							<input name="conffile" type="file" class="formfld" id="conffile" size="40"><br><br>
							<input name="Submit" type="submit" class="formbtn" id="restore" value="<?=gettext("Restore configuration");?>">
			      </td>
			    </tr>
			  </table>
			</td>
		</tr>
	</table>
</form>
<?php include("fend.inc");?>
