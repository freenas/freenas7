#!/usr/local/bin/php
<?php
/*
	services_tftp.php
	Copyright © 2008 Volker Theile (votdev@gmx.de)
	All rights reserved.

	part of FreeNAS (http://www.freenas.org)
	Copyright (C) 2005-2008 Olivier Cochard <olivier@freenas.org>.
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

if (!is_array($config['tftpd']))
	$config['tftpd'] = array();

$pconfig['enable'] = isset($config['tftpd']['enable']);
$pconfig['dir'] = $config['tftpd']['dir'];
$pconfig['allownew'] = isset($config['tftpd']['allownew']);
$pconfig['uploadmode'] = $config['tftpd']['uploadmode'];
$pconfig['extraoptions'] = $config['tftpd']['extraoptions'];

if ($_POST) {
	unset($input_errors);
	$pconfig = $_POST;

	// Input validation.
	if ($_POST['enable']) {
		$reqdfields = explode(" ", "dir");
		$reqdfieldsn = array(gettext("Directory"));
		$reqdfieldst = explode(" ", "string");

		do_input_validation($_POST, $reqdfields, $reqdfieldsn, &$input_errors);
		do_input_validation_type($_POST, $reqdfields, $reqdfieldsn, $reqdfieldst, &$input_errors);
	}

	if (!$input_errors) {
		$config['tftpd']['enable'] = $_POST['enable'] ? true : false;
		$config['tftpd']['dir'] = $_POST['dir'];
		$config['tftpd']['allownew'] = $_POST['allownew'] ? true : false;
		$config['tftpd']['extraoptions'] = $_POST['extraoptions'];

		write_config();

		$retval = 0;
		if (!file_exists($d_sysrebootreqd_path)) {
			config_lock();
			$retval |= rc_update_service("tftpd");
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
	document.iform.dir.disabled = endis;
	document.iform.allownew.disabled = endis;
	document.iform.extraoptions.disabled = endis;
}
//-->
</script>
<table width="100%" border="0" cellpadding="0" cellspacing="0">
  <tr>
    <td class="tabcont">
    	<form action="services_tftp.php" method="post" name="iform" id="iform">
				<?php if ($input_errors) print_input_errors($input_errors);?>
				<?php if ($savemsg) print_info_box($savemsg);?>
				<table width="100%" border="0" cellpadding="6" cellspacing="0">
					<?php html_titleline_checkbox("enable", gettext("Trivial File Transfer Protocol"), $pconfig['enable'] ? true : false, gettext("Enable"), "enable_change(false)");?>
					<?php html_filechooser("dir", gettext("Directory"), $pconfig['dir'], gettext("The directory containing the files you want to publish."), "/mnt", true, 60);?>
					<?php html_checkbox("allownew", gettext("Allow new files"), $pconfig['allownew'] ? true : false, gettext("Allow new files to be created."), gettext("By default, only already existing files can be uploaded."), false);?>
					<?php html_inputbox("extraoptions", gettext("Extra options"), $pconfig['extraoptions'], gettext("Extra options (usually empty)."), false, 40);?>
			  </table>
				<div id="submit">
					<input name="Submit" type="submit" class="formbtn" value="<?=gettext("Save and Restart");?>" onClick="enable_change(true)">
				</div>
			</form>
		</td>
  </tr>
</table>
<script language="JavaScript">
<!--
enable_change();
//-->
</script>
<?php include("fend.inc");?>
