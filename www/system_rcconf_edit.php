#!/usr/local/bin/php
<?php
/*
	system_rcconf_edit.php
	Copyright © 2006-2008 Volker Theile (votdev@gmx.de)
	All rights reserved.

	part of FreeNAS (http://www.freenas.org)
	Copyright (C) 2005-2008 Olivier Cochard-Labbé <olivier@freenas.org>.
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

$id = $_GET['id'];
if (isset($_POST['id']))
	$id = $_POST['id'];

$pgtitle = array(gettext("System"), gettext("Advanced"), gettext("rc.conf"), isset($id) ? gettext("Edit") : gettext("Add"));

if (!is_array($config['system']['rcconf']['auxparam']))
	$config['system']['rcconf']['auxparam'] = array();

sort($config['system']['rcconf']['auxparam']);

$a_rcvar = &$config['system']['rcconf']['auxparam'];

if (isset($id) && $a_rcvar[$id]) {
	$pconfig['option'] = $a_rcvar[$id];
} else {
	$pconfig['option'] = "";
}

if ($_POST) {
	unset($input_errors);
	$pconfig = $_POST;

	// Input validation.
  $reqdfields = explode(" ", "option");
  $reqdfieldsn = array(gettext("Option"));
  $reqdfieldst = explode(" ", "string");

  do_input_validation($_POST, $reqdfields, $reqdfieldsn, &$input_errors);
  do_input_validation_type($_POST, $reqdfields, $reqdfieldsn, $reqdfieldst, &$input_errors);

	if (!$input_errors) {
		if (isset($id) && $a_rcvar[$id])
			$a_rcvar[$id] = $pconfig['option'];
		else
			$a_rcvar[] = $pconfig['option'];

		write_config();
		touch($d_rcconfdirty_path);

		header("Location: system_rcconf.php");
		exit;
	}
}
?>
<?php include("fbegin.inc");?>
<table width="100%" border="0" cellpadding="0" cellspacing="0">
	<tr>
    <td class="tabnavtbl">
      <ul id="tabnav">
      	<li class="tabinact"><a href="system_advanced.php"><?=gettext("Advanced");?></a></li>
      	<li class="tabinact"><a href="system_email.php"><?=gettext("Email");?></a></li>
      	<li class="tabinact"><a href="system_proxy.php"><?=gettext("Proxy");?></a></li>
      	<li class="tabinact"><a href="system_swap.php"><?=gettext("Swap");?></a></li>
        <li class="tabinact"><a href="system_rc.php"><?=gettext("Command scripts");?></a></li>
        <li class="tabinact"><a href="system_cron.php"><?=gettext("Cron");?></a></li>
        <li class="tabact"><a href="system_rcconf.php" title="<?=gettext("Reload page");?>"><?=gettext("rc.conf");?></a></li>
        <li class="tabinact"><a href="system_sysctl.php"><?=gettext("sysctl.conf");?></a></li>
      </ul>
    </td>
  </tr>
  <tr>
    <td class="tabcont">
			<form action="system_rcconf_edit.php" method="post" name="iform" id="iform">
				<?php if ($input_errors) print_input_errors($input_errors); ?>
			  <table width="100%" border="0" cellpadding="6" cellspacing="0">
					<tr>
						<td width="22%" valign="top" class="vncellreq"><?=gettext("Option");?></td>
						<td width="78%" class="vtable">
							<input name="option" type="text" class="formfld" id="option" size="60" value="<?=htmlspecialchars($pconfig['option']);?>"><br/>
							<span class="vexpl"><?=gettext("Options are set with 'name=value' assignments that use 'sh' syntax.");?></span>
						</td>
					</tr>
			    <tr>
			      <td width="22%" valign="top">&nbsp;</td>
			      <td width="78%"> <input name="Submit" type="submit" class="formbtn" value="<?=(isset($id)) ? gettext("Save") : gettext("Add")?>">
			        <?php if (isset($id)):?>
			        <input name="id" type="hidden" value="<?=$id;?>">
			        <?php endif;?>
			      </td>
			    </tr>
			  </table>
			</form>
    </td>
  </tr>
</table>
<?php include("fend.inc");?>
