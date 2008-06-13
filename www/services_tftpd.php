#!/usr/local/bin/php
<?php
/*
	services_tftpd.php
	part of FreeNAS (http://www.freenas.org)
	Copyright (C) 2005-2008 Olivier Cochard-Labbe <olivier@freenas.org>.
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

$pgtitle = array(gettext("Services"), gettext("TFTP"));

if (!is_array($config['tftpd'])) {
	$config['tftpd'] = array();
}

$pconfig['enable'] = isset($config['tftpd']['enable']);

if($config['tftpd']['directory']) {
	$pconfig['directory'] = $config['tftpd']['directory'];
} else {
	$pconfig['directory'] = "/mnt";
}

if($config['tftpd']['umask']) {
	$pconfig['umask'] = $config['tftpd']['umask'];
} else {
	$pconfig['umask'] = "022";
}

$pconfig['logging'] = isset($config['tftpd']['logging']);
$pconfig['negativeack'] = isset($config['tftpd']['negativeack']);
$pconfig['writereqs'] = isset($config['tftpd']['writereqs']);

if ($_POST) {
	unset($input_errors);
	$pconfig = $_POST;

	if ($_POST['enable']) {
		// Input validation.
		$reqdfields = explode(" ", "directory umask");
		$reqdfieldsn = array(gettext("Directory"), gettext("Create mask"));
		do_input_validation($_POST, $reqdfields, $reqdfieldsn, &$input_errors);
		
		$reqdfields = explode(" ", "umask");
		$reqdfieldsn = array(gettext("Create mask"));
		$reqdfieldst = explode(" ", "filemode");
		do_input_validation_type($_POST, $reqdfields, $reqdfieldsn, $reqdfieldst, &$input_errors);
	}
	
	if (!$input_errors) {
	
		$config['tftpd']['enable'] = $_POST['enable'] ? true : false;
		$config['tftpd']['umask'] = $_POST['umask'];
		$config['tftpd']['directory'] = $_POST['directory'];
		$config['tftpd']['logging'] = $_POST['logging'] ? true : false;
		$config['tftpd']['negativeack'] = $_POST['negativeack'] ? true : false;
		$config['tftpd']['writereqs'] = $_POST['writereqs'] ? true : false;
		
		write_config();

		$retval = 0;
		if (!file_exists($d_sysrebootreqd_path)) {
			config_lock();
			$retval |= rc_update_service("inetd");
			config_unlock();
		}
		$savemsg = get_std_save_message($retval);
	}
}
?>
<?php include("fbegin.inc");?>
<script language="JavaScript">
<!--
function enable_change(enable_change) {
	var endis = !(document.iform.enable.checked || enable_change);
	document.iform.directory.disabled = endis;
	document.iform.umask.disabled = endis;
	document.iform.logging.disabled = endis;
	document.iform.negativeack.disabled = endis;
	document.iform.write.disabled = endis;
	document.iform.fcbrowse.disabled = endis;
}
//-->
</script>
<form action="services_tftpd.php" method="post" name="iform" id="iform">
	<table width="100%" border="0" cellpadding="0" cellspacing="0">
	  <tr>
	    <td class="tabcont">
				<?php if ($input_errors) print_input_errors($input_errors);?>
				<?php if ($savemsg) print_info_box($savemsg);?>
				<table width="100%" border="0" cellpadding="6" cellspacing="0">
				<tr>
					<td colspan="2" valign="top" class="optsect_t">
						<table border="0" cellspacing="0" cellpadding="0" width="100%">
						<tr>
							<td class="optsect_s"><strong><?=gettext("TFTP Server");?></strong></td>
							<td align="right" class="optsect_s"><input name="enable" type="checkbox" value="yes" <?php if ($pconfig['enable']) echo "checked"; ?> onClick="enable_change(false)"> <strong><?=gettext("Enable"); ?></strong></td>
						</tr>
						</table>
					</td>
				</tr>
				<?=html_filechooser("directory", gettext("Directory"), htmlspecialchars($pconfig['directory']), gettext("Cause tftpd to change its root directory to directory (/mnt by default)."), "/mnt", true);?>
				<?=html_inputbox("umask",gettext("Create mask"),htmlspecialchars($pconfig['umask']),gettext("Use this option to override the file creation mask (022 by default)."), true);?>
				<?=html_checkbox("logging",gettext("Logging"), $pconfig['logging'] ? true : false, "", gettext("Log all requests using syslog."));?>
				<?=html_checkbox("negativeack",gettext("Negative acknowledge"), $pconfig['negativeack'] ? true : false, "", gettext("Suppress negative acknowledgement of requests for nonexistent relative filenames."));?>
				<?=html_checkbox("writereqs",gettext("Write requests"), $pconfig['writereqs'] ? true : false, "", gettext("Allow write requests to create new files."));?>
				<tr>
					<td width="22%" valign="top">&nbsp;</td>
					<td width="78%">
					<input name="Submit" type="submit" class="formbtn" value="<?=gettext("Save and Restart");?>" onClick="enable_change(true)">
					</td>
				</tr>
				<tr>
					<td width="22%" valign="top">&nbsp;</td>
					<td width="78%">
						<span class="red"><strong><?=gettext("Note");?>:</strong></span><br>
						<?=gettext("You must add tftpd to inetd.");?>
					</td>
				</tr>
				</table>
			</td>
		</tr>
	</table>
</form>
<script language="JavaScript">
<!--
enable_change(false);
//-->
</script>
<?php include("fend.inc");?>
