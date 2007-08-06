#!/usr/local/bin/php
<?php
/*
	status_report.php
	Copyright © 2007 Volker Theile (votdev@gmx.de)
	Copyright © 2007 Dan Merschi (freenas@bcapro.com)
  All rights reserved.

	part of FreeNAS (http://www.freenas.org)
	Copyright (C) 2005-2007 Olivier Cochard <olivier@freenas.org>.
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
$pgtitle = array(gettext("Status"), gettext("Report"));

if(!is_array($config['statusreport']))
	$config['statusreport'] = array();

$pconfig['enable'] = isset($config['statusreport']['enable']);
$pconfig['server'] = $config['statusreport']['server'];
$pconfig['port'] = $config['statusreport']['port'];
$pconfig['auth'] = isset($config['statusreport']['auth']);
$pconfig['username'] = $config['statusreport']['username'];
$pconfig['from'] = $config['statusreport']['from'];
$pconfig['to'] = $config['statusreport']['to'];
$pconfig['npoll'] = $config['statusreport']['npoll'];
$pconfig['tpoll'] = $config['statusreport']['tpoll'];

if($_POST) {
	unset($input_errors);

	$pconfig = $_POST;

	/* Input validation. */
	if($_POST['enable']) {
		$reqdfields = explode(" ", "server port from to");
		$reqdfieldsn = array(gettext("Server address"), gettext("Server port"), gettext("From e-mail"), gettext("To e-mail"));
		$reqdfieldst = explode(" ", "string numeric string string");

		if ($_POST['auth']) {
			$reqdfields = array_merge($reqdfields,array("username", "password"));
			$reqdfieldsn = array_merge($reqdfieldsn,array(gettext("Username"), gettext("Password")));
			$reqdfieldst = array_merge($reqdfieldst,array("string","string"));
		}

		do_input_validation($_POST, $reqdfields, $reqdfieldsn, &$input_errors);
		do_input_validation_type($_POST, $reqdfields, $reqdfieldsn, $reqdfieldst, &$input_errors);
	}

	/* Check for a password mismatch. */
	if ($_POST['auth'] && ($_POST['password'] !== $_POST['passwordconf'])) {
		$input_errors[] = gettext("The passwords do not match.");
	}

	if(!$input_errors) {
		$config['statusreport']['enable'] = $_POST['enable'] ? true : false;
		$config['statusreport']['server'] = $_POST['server'];
		$config['statusreport']['port'] = $_POST['port'];
		$config['statusreport']['auth'] = $_POST['auth'] ? true : false;
		$config['statusreport']['username'] = $_POST['username'];
		$config['statusreport']['password'] = base64_encode($_POST['password']);
		$config['statusreport']['from'] = $_POST['from'];
		$config['statusreport']['to'] = $_POST['to'];
    $config['statusreport']['tpoll']= $_POST['tpoll'];
    $config['statusreport']['npoll']= $_POST['npoll'];

		write_config();

    $retval = 0;
		if (!file_exists($d_sysrebootreqd_path)) {
			config_lock();
			$retval |= rc_update_service("cron");
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
	document.iform.server.disabled = endis;
	document.iform.port.disabled = endis;
	document.iform.auth.disabled = endis;
	document.iform.username.disabled = endis;
	document.iform.password.disabled = endis;
	document.iform.passwordconf.disabled = endis;
	document.iform.from.disabled = endis;
	document.iform.to.disabled = endis;
	document.iform.tpoll.disabled = endis;
	document.iform.npoll.disabled = endis;
}
function auth_change() {
	switch(document.iform.auth.checked) {
		case false:
      showElementById('username_tr','hide');
  		showElementById('password_tr','hide');
      break;

    case true:
      showElementById('username_tr','show');
  		showElementById('password_tr','show');
      break;
	}
}
//-->
</script>
<?php if ($input_errors) print_input_errors($input_errors); ?>
<?php if ($savemsg) print_info_box($savemsg); ?>
<form action="status_report.php" method="post" name="iform" id="iform">
  <table width="100%" border="0" cellpadding="6" cellspacing="0">
    <tr>
      <td colspan="2" valign="top" class="optsect_t">
  		  <table border="0" cellspacing="0" cellpadding="0" width="100%">
  		  <tr>
          <td class="optsect_s"><strong><?=gettext("Status report");?></strong></td>
  			  <td align="right" class="optsect_s"><input name="enable" type="checkbox" value="yes" <?php if ($pconfig['enable']) echo "checked"; ?> onClick="enable_change(false)"> <strong><?=gettext("Enable");?></strong></td>
        </tr>
  		  </table>
      </td>
    </tr>
    <tr>
	    <td width="22%" valign="top" class="vncellreq"><?=gettext("Server address");?></td>
      <td width="78%" class="vtable">
        <?=$mandfldhtml;?>
        <input name="server" type="text" class="formfld" id="server" size="40" value="<?=htmlentities($pconfig['server']);?>">
      </td>
		</tr>
		<tr>
      <td width="22%" valign="top" class="vncellreq"><?=gettext("Server port");?></td>
      <td width="78%" class="vtable">
        <?=$mandfldhtml;?>
        <input name="port" type="text" class="formfld" id="port" size="10" value="<?=htmlentities($pconfig['port']);?>">
      </td>
    </tr>
		<tr>
      <td width="22%" valign="top" class="vncellreq"><?=gettext("Authentication");?></td>
      <td width="78%" class="vtable">
      	<?=$mandfldhtml;?>
        <input name="auth" type="checkbox" id="auth" value="yes" <?php if ($pconfig['auth']) echo "checked"; ?> onClick="auth_change()"><br>
        <?=gettext("Use SMTP authentication.");?>
			</td>
    </tr>
		<tr id="username_tr">
      <td width="22%" valign="top" class="vncellreq"><?=gettext("Username");?></td>
      <td width="78%" class="vtable">
        <?=$mandfldhtml;?>
        <input name="username" type="text" class="formfld" id="username" size="40" value="<?=htmlentities($pconfig['username']);?>">
      </td>
    </tr>
    <tr id="password_tr">
      <td width="22%" valign="top" class="vncellreq"><?=gettext("Password");?></td>
      <td width="78%" class="vtable">
        <?=$mandfldhtml;?>
        <input name="password" type="password" class="formfld" id="password" size="20" value="<?=htmlentities($pconfig['password']);?>"><br>
        <input name="passwordconf" type="password" class="formfld" id="passwordconf" size="20" value="<?=htmlspecialchars($pconfig['passwordconf']);?>">&nbsp;(<?=gettext("Confirmation");?>)<br>
      </td>
		</tr>
		<tr>
			<td width="22%" valign="top" class="vncellreq"><?=gettext("From email");?></td>
			<td width="78%" class="vtable">
				<?=$mandfldhtml;?>
				<input name="from" type="text" class="formfld" id="from" size="40" value="<?=htmlentities($pconfig['from']);?>"><br>
				<?=gettext("Your own email.");?>
			</td>
		</tr>
		<tr>
			<td width="22%" valign="top" class="vncellreq"><?=gettext("To email");?></td>
			<td width="78%" class="vtable">
				<?=$mandfldhtml;?>
				<input name="to" type="text" class="formfld" id="to" size="40" value="<?=htmlentities($pconfig['to']);?>"><br>
				<?=gettext("Destination e-mail.");?>
			</td>
		</tr>
		<tr id="minspace_tr">
			<td width="22%" valign="top" class="vncellreq"><?=gettext("Polling period") ; ?></td>
			<td width="78%" class="vtable">
				<?=$mandfldhtml;?>
				<?=gettext("Poll every ");?>
				<select name="npoll" class="formfld" id="npoll">
				<?php $types = explode(",", "1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20,21,22,23"); $vals = explode(" ", "1 2 3 4 5 6 7 8 9 10 11 12 13 14 15 16 17 18 19 20 21 22 23");?>
				<?php $j = 0; for ($j = 0; $j < count($vals); $j++): ?>
					<option value="<?=$vals[$j];?>"><?=htmlspecialchars($types[$j]);?></option>
				<?php endfor; ?>
				</select>&nbsp;&nbsp;
				<select name="tpoll" class="formfld" id="tpoll">
				<?php $types = explode(",", "minutes, hours, days"); $vals = explode(" ", "minutes hours days");?>
				<?php $j = 0; for ($j = 0; $j < count($vals); $j++): ?>
					<option value="<?=$vals[$j];?>"><?=htmlspecialchars($types[$j]);?></option>
				<?php endfor; ?>
				</select>
			</td>
		</tr>
    <tr>
      <td width="22%" valign="top">&nbsp;</td>
      <td width="78%">
        <input name="Submit" type="submit" class="formbtn" value="<?=gettext("Save and Restart");?>" onClick="enable_change(true)">
      </td>
    </tr>
  </table>
</form>
<script language="JavaScript">
<!--
enable_change(false);
auth_change();
//-->
</script>
<?php include("fend.inc"); ?>
