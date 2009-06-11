#!/usr/local/bin/php
<?php
/*
	system_sysctl_edit.php
	Copyright (C) 2008 Nelson Silva (nsilva@hotlap.org)
	All rights reserved.

	Modified by Volker Theile (votdev@gmx.de)

	part of FreeNAS (http://www.freenas.org)
	Copyright (C) 2005-2009 Olivier Cochard-Labbe <olivier@freenas.org>.
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
require("auth.inc");
require("guiconfig.inc");

$uuid = $_GET['uuid'];
if (isset($_POST['uuid']))
	$uuid = $_POST['uuid'];

$pgtitle = array(gettext("System"), gettext("Advanced"), gettext("sysctl.conf"), isset($uuid) ? gettext("Edit") : gettext("Add"));

if (!is_array($config['system']['sysctl']['param']))
	$config['system']['sysctl']['param'] = array();

array_sort_key($config['system']['sysctl']['param'], "name");
$a_sysctlvar = &$config['system']['sysctl']['param'];

if (isset($uuid) && (FALSE !== ($cnid = array_search_ex($uuid, $a_sysctlvar, "uuid")))) {
	$pconfig['enable'] = isset($a_sysctlvar[$cnid]['enable']);
	$pconfig['uuid'] = $a_sysctlvar[$cnid]['uuid'];
	$pconfig['name'] = $a_sysctlvar[$cnid]['name'];
	$pconfig['value'] = $a_sysctlvar[$cnid]['value'];
	$pconfig['comment'] = $a_sysctlvar[$cnid]['comment'];
} else {
	$pconfig['enable'] = true;
	$pconfig['uuid'] = uuid();
	$pconfig['name'] = "";
	$pconfig['value'] = "";
	$pconfig['comment'] = "";
}

if ($_POST) {
	unset($input_errors);
	$pconfig = $_POST;

	if ($_POST['Cancel']) {
		header("Location: system_sysctl.php");
		exit;
	}

	// Input validation.
	$reqdfields = explode(" ", "name value");
	$reqdfieldsn = array(gettext("Option"), gettext("Value"));
	$reqdfieldst = explode(" ", "string string");

	do_input_validation($_POST, $reqdfields, $reqdfieldsn, &$input_errors);
	do_input_validation_type($_POST, $reqdfields, $reqdfieldsn, $reqdfieldst, &$input_errors);

	// Check if MIB name exists.
	exec("/sbin/sysctl -NA", $arr);
	if (!in_array(trim($pconfig['name']), $arr)) {
		$input_errors[] = sprintf(gettext("The MIB '%s' doesn't exist in sysctl."), trim($pconfig['name']));
	}

	// Check if MIB is already configured (not in edit mode).
	if (!(isset($uuid) && (FALSE !== $cnid)) && (false !== array_search_ex(trim($pconfig['name']), $config['system']['sysctl']['param'], "name"))) {
		$input_errors[] = sprintf(gettext("The MIB '%s' already exist."), trim($pconfig['name']));
	}

	if (!$input_errors) {
		$param = array();
		$param['enable'] = $_POST['enable'] ? true : false;
		$param['uuid'] = $_POST['uuid'];
		$param['name'] = $pconfig['name'];
		$param['value'] = $pconfig['value'];
		$param['comment'] = $pconfig['comment'];

		if (isset($uuid) && (FALSE !== $cnid)) {
			$a_sysctlvar[$cnid] = $param;
			$mode = UPDATENOTIFY_MODE_MODIFIED;
		} else {
			$a_sysctlvar[] = $param;
			$mode = UPDATENOTIFY_MODE_NEW;
		}

		updatenotify_set("sysctl", $mode, $param['uuid']);
		write_config();

		header("Location: system_sysctl.php");
		exit;
	}
}
?>
<?php include("fbegin.inc");?>
<table width="100%" border="0" cellpadding="0" cellspacing="0">
	<tr>
    <td class="tabnavtbl">
      <ul id="tabnav">
      	<li class="tabinact"><a href="system_advanced.php"><span><?=gettext("Advanced");?></span></a></li>
      	<li class="tabinact"><a href="system_email.php"><span><?=gettext("Email");?></span></a></li>
      	<li class="tabinact"><a href="system_proxy.php"><span><?=gettext("Proxy");?></span></a></li>
      	<li class="tabinact"><a href="system_swap.php"><span><?=gettext("Swap");?></span></a></li>
        <li class="tabinact"><a href="system_rc.php"><span><?=gettext("Command scripts");?></span></a></li>
        <li class="tabinact"><a href="system_cron.php"><span><?=gettext("Cron");?></span></a></li>
        <li class="tabinact"><a href="system_rcconf.php"><span><?=gettext("rc.conf");?></span></a></li>
        <li class="tabact"><a href="system_sysctl.php" title="<?=gettext("Reload page");?>"><span><?=gettext("sysctl.conf");?></span></a></li>
      </ul>
    </td>
  </tr>
  <tr>
    <td class="tabcont">
			<form action="system_sysctl_edit.php" method="post" name="iform" id="iform">
				<?php if ($input_errors) print_input_errors($input_errors);?>
			  <table width="100%" border="0" cellpadding="6" cellspacing="0">
					<?php html_titleline_checkbox("enable", "", $pconfig['enable'] ? true : false, gettext("Enable"));?>
					<?php html_inputbox("name", gettext("Name"), $pconfig['name'], gettext("Enter a valid sysctl MIB name."), true, 40);?>
					<?php html_inputbox("value", gettext("Value"), $pconfig['value'], gettext("A valid systctl MIB value."), true);?>
					<?php html_inputbox("comment", gettext("Comment"), $pconfig['comment'], gettext("You may enter a description here for your reference."), false, 40);?>
				</table>
				<div id="submit">
					<input name="Submit" type="submit" class="formbtn" value="<?=(isset($uuid) && (FALSE !== $cnid)) ? gettext("Save") : gettext("Add")?>">
					<input name="Cancel" type="submit" class="formbtn" value="<?=gettext("Cancel");?>">
					<input name="uuid" type="hidden" value="<?=$pconfig['uuid'];?>">
			  </div>
			  <?php include("formend.inc");?>
			</form>
    </td>
  </tr>
</table>
<?php include("fend.inc");?>
