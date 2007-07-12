#!/usr/local/bin/php
<?php
/*
	system_advanced_rcstartup_edit.php
	Copyright © 2007 Volker Theile (votdev@gmx.de)
  All rights reserved.

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
require("guiconfig.inc");

$id = $_GET['id'];
$type = $_GET['type'];

if (isset($_POST['id']))
	$id = $_POST['id'];

if (isset($_POST['type']))
	$type = $_POST['type'];

$pgtitle = array(gettext("System"),gettext("Advanced"),gettext("Startup"),isset($id)?gettext("Edit"):gettext("Add"));

if (!is_array($config['system']['earlyshellcmd']))
	$config['system']['earlyshellcmd'] = array();

if (!is_array($config['system']['shellcmd']))
	$config['system']['shellcmd'] = array();

if (isset($id) && isset($type)) {
	switch($type) {
		case "PRE":
			$command = $config['system']['earlyshellcmd'][$id];
			break;
		case "POST":
			$command = $config['system']['shellcmd'][$id];
			break;
	}
}

if ($_POST) {
	unset($input_errors);

	/* Input validation */
  $reqdfields = explode(" ", "command type");
  $reqdfieldsn = array(gettext("Command"),gettext("Type"));
  do_input_validation($_POST, $reqdfields, $reqdfieldsn, &$input_errors);

	if (!$input_errors) {
		$command = $_POST['command'];
		$type = $_POST['type'];

		switch($type) {
			case "PRE":
				$a_cmd = &$config['system']['earlyshellcmd'];
				break;
			case "POST":
				$a_cmd = &$config['system']['shellcmd'];
				break;
		}

		if (isset($id) && $a_cmd[$id])
			$a_cmd[$id] = $command;
		else
			$a_cmd[] = $command;

		write_config();

		header("Location: system_advanced_rcstartup.php");
		exit;
	}
}
?>
<?php include("fbegin.inc"); ?>
<p><span class="vexpl"><span class="red"><strong><?=gettext("Note");?>:</strong></span><br><?=gettext("The options on this page are intended for use by advanced users only, and there's <strong>NO</strong> support for them.");?></p>
<table width="100%" border="0" cellpadding="0" cellspacing="0">
	<tr>
    <td class="tabnavtbl">
      <ul id="tabnav">
      	<li class="tabinact"><a href="system_advanced.php"><?=gettext("Advanced");?></a></li>
      	<li class="tabinact"><a href="system_advanced_swap.php"><?=gettext("Swap");?></a></li>
        <li class="tabact"><a href="system_advanced_rcstartup.php" style="color:black" title="<?=gettext("Reload page");?>"><?=gettext("Startup");?></a></li>
      </ul>
    </td>
  </tr>
  <tr>
    <td class="tabcont">
			<form action="system_advanced_rcstartup_edit.php" method="post" name="iform" id="iform">
				<?php if ($input_errors) print_input_errors($input_errors); ?>
			  <table width="100%" border="0" cellpadding="6" cellspacing="0">
					<tr>
						<td width="22%" valign="top" class="vncellreq"><?=gettext("Command");?></td>
						<td width="78%" class="vtable">
			        <?=$mandfldhtml;?>
							<input name="command" type="text" class="formfld" id="command" size="60" value="<?=htmlspecialchars($command);?>">
						</td>
					</tr>
					<tr>
						<td width="22%" valign="top" class="vncellreq"><?=gettext("Type");?></td>
						<td width="78%" class="vtable">
							<?=$mandfldhtml;?>
							<select name="type" class="formfld" id="type" <?php if ($type) echo "disabled";?>>
								<option value="PRE" <?php if ($type == "PRE") echo "selected";?>>Pre</option>
								<option value="POST" <?php if ($type == "POST") echo "selected";?>>Post</option>
							</select>
							<br><?=gettext("Execute command pre or post system initialization (booting).");?>
						</td>
					</tr>
			    <tr>
			      <td width="22%" valign="top">&nbsp;</td>
			      <td width="78%"> <input name="Submit" type="submit" class="formbtn" value="<?=(isset($id) && isset($type))?gettext("Save"):gettext("Add")?>">
			        <?php if (isset($id) && isset($type)): ?>
			        <input name="id" type="hidden" value="<?=$id;?>">
			        <input name="type" type="hidden" value="<?=$type;?>">
			        <?php endif; ?>
			      </td>
			    </tr>
			  </table>
			</form>
    </td>
  </tr>
</table>
<?php include("fend.inc");?>
