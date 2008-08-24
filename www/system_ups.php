#!/usr/local/bin/php
<?php
/*
	system_ups.php
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

$pgtitle = array(gettext("System"), gettext("UPS"));

if(!is_array($config['system']['ups']))
	$config['system']['ups'] = array();

$pconfig['enable'] = isset($config['system']['ups']['enable']);
$pconfig['upsname'] = $config['system']['ups']['upsname'];
$pconfig['driver'] = $config['system']['ups']['driver'];
$pconfig['port'] = $config['system']['ups']['port'];
$pconfig['cable'] = $config['system']['ups']['cable'];
$pconfig['desc'] = $config['system']['ups']['desc'];

if ($_POST) {
	unset($input_errors);
	$pconfig = $_POST;

	// Input validation.
	if ($_POST['enable']) {
		$reqdfields = explode(" ", "upsname driver port");
		$reqdfieldsn = array(gettext("Identifier"), gettext("Driver"), gettext("Port"));
		$reqdfieldst = explode(" ", "alias string string");

		do_input_validation($_POST, $reqdfields, $reqdfieldsn, &$input_errors);
		do_input_validation_type($_POST, $reqdfields, $reqdfieldsn, $reqdfieldst, &$input_errors);
	}

	if (!$input_errors) {
		$config['system']['ups']['enable'] = $_POST['enable'] ? true : false;
		$config['system']['ups']['upsname'] = $_POST['upsname'];
		$config['system']['ups']['driver'] = $_POST['driver'];
		$config['system']['ups']['port'] = $_POST['port'];
		$config['system']['ups']['cable'] = $_POST['cable'];
		$config['system']['ups']['desc'] = $_POST['desc'];

		write_config();

		$retval = 0;
		if (!file_exists($d_sysrebootreqd_path)) {
			config_lock();
			$retval |= rc_update_service("nut");
			$retval |= rc_update_service("nut_upslog");
			$retval |= rc_update_service("nut_upsmon");
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
	document.iform.upsname.disabled = endis;
	document.iform.driver.disabled = endis;
	document.iform.port.disabled = endis;
	document.iform.cable.disabled = endis;
	document.iform.desc.disabled = endis;
}
//-->
</script>
<form action="system_ups.php" method="post" name="iform" id="iform">
	<table width="100%" border="0" cellpadding="0" cellspacing="0">
	  <tr>
	    <td class="tabcont">
				<?php if ($input_errors) print_input_errors($input_errors);?>
				<?php if ($savemsg) print_info_box($savemsg);?>
			  <table width="100%" border="0" cellpadding="6" cellspacing="0">
			  	<?php html_titleline_checkbox("enable", gettext("UPS"), $pconfig['enable'] ? true : false, gettext("Enable"), "enable_change(false)");?>
					<?php html_inputbox("upsname", gettext("Identifier"), $pconfig['upsname'], gettext("This is used to access the UPS in the form like upsname@localhost."), true, 30);?>
					<?php html_inputbox("driver", gettext("Driver"), $pconfig['driver'], gettext("The driver to be used."), true, 30);?>
					<?php html_inputbox("port", gettext("Port"), $pconfig['port'], gettext("The port to be used."), true, 30);?>
					<?php html_inputbox("cable", gettext("Cable"), $pconfig['cable'], gettext("The cable type."), false, 30);?>
					<?php html_inputbox("desc", gettext("Description"), $pconfig['desc'], gettext("You may enter a description here for your reference."), false, 40);?>
					<tr>
			      <td width="22%" valign="top">&nbsp;</td>
			      <td width="78%">
			        <input name="Submit" type="submit" class="formbtn" value="<?=gettext("Save and Restart");?>" onClick="enable_change(true)">
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
