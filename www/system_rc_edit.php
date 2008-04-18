#!/usr/local/bin/php
<?php
/*
	system_rc_edit.php
	Copyright © 2007-2008 Volker Theile (votdev@gmx.de)
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
$type = $_GET['type'];

if (isset($_POST['id']))
	$id = $_POST['id'];

if (isset($_POST['type']))
	$type = $_POST['type'];

$pgtitle = array(gettext("System"),gettext("Advanced"),gettext("Command scripts"),isset($id)?gettext("Edit"):gettext("Add"));

if (!is_array($config['rc']['preinit']['cmd']))
	$config['rc']['preinit']['cmd'] = array();

if (!is_array($config['rc']['postinit']['cmd']))
	$config['rc']['postinit']['cmd'] = array();

if (!is_array($config['rc']['shutdown']['cmd']))
	$config['rc']['shutdown']['cmd'] = array();

if (isset($id) && isset($type)) {
	switch($type) {
		case "PREINIT":
			$command = $config['rc']['preinit']['cmd'][$id];
			break;
		case "POSTINIT":
			$command = $config['rc']['postinit']['cmd'][$id];
			break;
		case "SHUTDOWN":
			$command = $config['rc']['shutdown']['cmd'][$id];
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
			case "PREINIT":
				$a_cmd = &$config['rc']['preinit']['cmd'];
				break;
			case "POSTINIT":
				$a_cmd = &$config['rc']['postinit']['cmd'];
				break;
			case "SHUTDOWN":
				$a_cmd = &$config['rc']['shutdown']['cmd'];
				break;
		}

		if (isset($id) && $a_cmd[$id])
			$a_cmd[$id] = $command;
		else
			$a_cmd[] = $command;

		write_config();

		header("Location: system_rc.php");
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
        <li class="tabact"><a href="system_rc.php" title="<?=gettext("Reload page");?>"><?=gettext("Command scripts");?></a></li>
        <li class="tabinact"><a href="system_cron.php"><?=gettext("Cron");?></a></li>
        <li class="tabinact"><a href="system_rcconf.php"><?=gettext("rc.conf");?></a></li>
      </ul>
    </td>
  </tr>
  <tr>
    <td class="tabcont">
			<form action="system_rc_edit.php" method="post" name="iform" id="iform">
				<?php if ($input_errors) print_input_errors($input_errors); ?>
			  <table width="100%" border="0" cellpadding="6" cellspacing="0">
					<tr>
						<td width="22%" valign="top" class="vncellreq"><?=gettext("Command");?></td>
						<td width="78%" class="vtable">
							<input name="command" type="text" class="formfld" id="command" size="60" value="<?=htmlspecialchars($command);?>">
						</td>
					</tr>
					<tr>
						<td width="22%" valign="top" class="vncellreq"><?=gettext("Type");?></td>
						<td width="78%" class="vtable">
							<select name="type" class="formfld" id="type" <?php if ($type) echo "disabled";?>>
								<option value="PREINIT" <?php if ($type === "PREINIT") echo "selected";?>>PreInit</option>
								<option value="POSTINIT" <?php if ($type === "POSTINIT") echo "selected";?>>PostInit</option>
								<option value="SHUTDOWN" <?php if ($type === "SHUTDOWN") echo "selected";?>>Shutdown</option>
							</select><br/>
							<?=gettext("Execute command pre or post system initialization (booting) or before system shutdown.");?>
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
