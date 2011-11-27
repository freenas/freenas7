#!/usr/local/bin/php
<?php
/*
	services_lcdproc.php
	Copyright (C) 2005-2011 FreeNAS Project.
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
require("auth.inc");
require("guiconfig.inc");

$pgtitle = array(gettext("Services"), gettext("LCDproc"));

if(!is_array($config['lcdproc']))
	$config['lcdproc'] = array();
if(!is_array($config['lcdproc']['lcdproc']))
	$config['lcdproc']['lcdproc'] = array();

$pconfig['enable'] = isset($config['lcdproc']['enable']);
$pconfig['driver'] = $config['lcdproc']['driver'];
$pconfig['port'] = $config['lcdproc']['port'];
$pconfig['waittime'] = $config['lcdproc']['waittime'];
if (is_array($config['lcdproc']['param']))
	$pconfig['param'] = implode("\n", $config['lcdproc']['param']);
if (is_array($config['lcdproc']['auxparam']))
	$pconfig['auxparam'] = implode("\n", $config['lcdproc']['auxparam']);
if (is_array($config['lcdproc']['lcdproc']['param']))
	$pconfig['lcdproc_param'] = implode("\n", $config['lcdproc']['lcdproc']['param']);
if (is_array($config['lcdproc']['lcdproc']['auxparam']))
	$pconfig['lcdproc_auxparam'] = implode("\n", $config['lcdproc']['lcdproc']['auxparam']);

if ($_POST) {
	unset($input_errors);
	$pconfig = $_POST;

	// Input validation.
	$reqdfields = explode(" ", "driver port waittime");
	$reqdfieldsn = array(gettext("Driver"), gettext("Port"), gettext("Wait time"));
	$reqdfieldst = explode(" ", "string numeric numeric");

	do_input_validation($_POST, $reqdfields, $reqdfieldsn, &$input_errors);
	do_input_validation_type($_POST, $reqdfields, $reqdfieldsn, $reqdfieldst, &$input_errors);

	if (!$input_errors) {
		$config['lcdproc']['enable'] = $_POST['enable'] ? true : false;
		$config['lcdproc']['driver'] = $_POST['driver'];
		$config['lcdproc']['port'] = $_POST['port'];
		$config['lcdproc']['waittime'] = $_POST['waittime'];

		# Write additional parameters.
		unset($config['lcdproc']['param']);
		foreach (explode("\n", $_POST['param']) as $param) {
			$param = trim($param, "\t\n\r");
			if (!empty($param))
				$config['lcdproc']['param'][] = $param;
		}
		unset($config['lcdproc']['auxparam']);
		foreach (explode("\n", $_POST['auxparam']) as $auxparam) {
			$auxparam = trim($auxparam, "\t\n\r");
			if (!empty($auxparam))
				$config['lcdproc']['auxparam'][] = $auxparam;
		}
		unset($config['lcdproc']['lcdproc']['param']);
		foreach (explode("\n", $_POST['lcdproc_param']) as $param) {
			$param = trim($param, "\t\n\r");
			if (!empty($param))
				$config['lcdproc']['lcdproc']['param'][] = $param;
		}
		unset($config['lcdproc']['lcdproc']['auxparam']);
		foreach (explode("\n", $_POST['lcdproc_auxparam']) as $auxparam) {
			$auxparam = trim($auxparam, "\t\n\r");
			if (!empty($auxparam))
				$config['lcdproc']['lcdproc']['auxparam'][] = $auxparam;
		}

		write_config();

		$retval = 0;
		if (!file_exists($d_sysrebootreqd_path)) {
			config_lock();
			$retval |= rc_update_service("LCDd");
			$retval |= rc_update_service("lcdproc");
			config_unlock();
		}

		$savemsg = get_std_save_message($retval);
	}
}
?>
<?php include("fbegin.inc");?>
<script type="text/javascript">
<!--
function enable_change(enable_change) {
	var endis = !(document.iform.enable.checked || enable_change);

	document.iform.driver.disabled = endis;
	document.iform.port.disabled = endis;
	document.iform.waittime.disabled = endis;
	document.iform.param.disabled = endis;
	document.iform.auxparam.disabled = endis;
	document.iform.lcdproc_param.disabled = endis;
	document.iform.lcdproc_auxparam.disabled = endis;
}
//-->
</script>
<table width="100%" border="0" cellpadding="0" cellspacing="0">
  <tr>
    <td class="tabcont">
      <form action="services_lcdproc.php" method="post" name="iform" id="iform">
	<?php if ($input_errors) print_input_errors($input_errors);?>
	<?php if ($savemsg) print_info_box($savemsg);?>
	<table width="100%" border="0" cellpadding="6" cellspacing="0">
	<?php html_titleline_checkbox("enable", gettext("LCDproc"), $pconfig['enable'] ? true : false, gettext("Enable"), "enable_change(false)");?>
	<?php html_inputbox("driver", gettext("Driver"), $pconfig['driver'], sprintf(gettext("The driver used to connect with the LCD. The list of available <a href='%s' target='_blank'>drivers</a>."), "http://lcdproc.org/hardware.php3"), true, 30);?>
	<?php html_inputbox("port", gettext("Port"), $pconfig['port'], sprintf(gettext("Port to listen on. Default port is %d."), 13666), true, 10);?>
	<?php html_inputbox("waittime", gettext("Wait time"), $pconfig['waittime'], gettext("The default time in seconds to display a screen."), true, 10);?>
	<?php html_textarea("param", gettext("Driver parameters"), $pconfig['param'], gettext("Additional parameters to the hardware-specific part of the driver."), false, 65, 10, false, false);?>
	<?php html_textarea("auxparam", gettext("Auxiliary parameters"), $pconfig['auxparam'], "", false, 65, 5, false, false);?>
	<?php html_separator();?>
	<?php html_titleline(gettext("LCDproc (client)"));?>
	<?php html_textarea("lcdproc_param", gettext("Extra options"), $pconfig['lcdproc_param'], "", false, 65, 10, false, false);?>
	<?php html_textarea("lcdproc_auxparam", gettext("Auxiliary parameters"), $pconfig['lcdproc_auxparam'], "", false, 65, 5, false, false);?>
	</table>
	<div id="submit">
	  <input name="Submit" type="submit" class="formbtn" value="<?=gettext("Save and Restart");?>" onclick="enable_change(true)" />
	</div>
	<div id="remarks">
	  <?php html_remark("note", gettext("Note"), sprintf(gettext("To get more information how to configure LCDproc check the LCDproc <a href='%s' target='_blank'>documentation</a>."), "http://lcdproc.org/"));?>
	</div>
	<?php include("formend.inc");?>
      </form>
    </td>
  </tr>
</table>
<script type="text/javascript">
<!--
enable_change(false);
//-->
</script>
<?php include("fend.inc");?>
