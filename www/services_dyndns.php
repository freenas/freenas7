#!/usr/local/bin/php
<?php
/*
	services_upnp.php
	Copyright © 2006-2007 Volker Theile (votdev@gmx.de)
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

$pgtitle = array(gettext("Services"),gettext("DynDNS"));

if(!is_array($config['dyndns']))
	$config['dyndns'] = array();

$pconfig['enable'] = isset($config['dyndns']['enable']);
$pconfig['servername'] = $config['dyndns']['servername'];
$pconfig['hostname'] = $config['dyndns']['hostname'];
$pconfig['username'] = $config['dyndns']['username'];
$pconfig['password'] = $config['dyndns']['password'];
$pconfig['updateperiod'] = $config['dyndns']['updateperiod'];
$pconfig['forcedupdateperiod'] = $config['dyndns']['forcedupdateperiod'];

if($_POST) {
	unset($input_errors);

	$pconfig = $_POST;

	/* input validation */
	if($_POST['enable']) {
		$reqdfields = explode(" ", "servername hostname username password");
		$reqdfieldsn = array(gettext("Servername"), gettext("Hostname"), gettext("Username"), gettext("Password"));

		do_input_validation($_POST, $reqdfields, $reqdfieldsn, &$input_errors);
	}

	if(!$input_errors) {
    $config['dyndns']['enable'] = $_POST['enable'] ? true : false;
    $config['dyndns']['servername'] = $_POST['servername'];
		$config['dyndns']['hostname'] = $_POST['hostname'];
		$config['dyndns']['username'] = $_POST['username'];
		$config['dyndns']['password'] = $_POST['password'];
		$config['dyndns']['updateperiod'] = $_POST['updateperiod'];
		$config['dyndns']['forcedupdateperiod'] = $_POST['forcedupdateperiod'];

		write_config();

		$retval = 0;
		if(!file_exists($d_sysrebootreqd_path)) {
			config_lock();
			$retval = services_dyndns_configure();
			config_unlock();
		}

		$savemsg = get_std_save_message($retval);
	}
}
// Get list of available interfaces.
$a_interface = get_interface_list();
?>
<?php include("fbegin.inc"); ?>
<script language="JavaScript">
<!--
function enable_change(enable_change) {
	var endis = !(document.iform.enable.checked || enable_change);
	document.iform.servername.disabled = endis;
	document.iform.hostname.disabled = endis;
	document.iform.username.disabled = endis;
	document.iform.password.disabled = endis;
	document.iform.updateperiod.disabled = endis;
	document.iform.forcedupdateperiod.disabled = endis;
}
//-->
</script>
<?php if ($input_errors) print_input_errors($input_errors); ?>
<?php if ($savemsg) print_info_box($savemsg); ?>
<form action="services_dyndns.php" method="post" name="iform" id="iform">
  <table width="100%" border="0" cellpadding="6" cellspacing="0">
    <tr>
      <td colspan="2" valign="top" class="optsect_t">
  		  <table border="0" cellspacing="0" cellpadding="0" width="100%">
  		  <tr>
          <td class="optsect_s"><strong><?=gettext("Dynamic DNS");?></strong></td>
  			  <td align="right" class="optsect_s"><input name="enable" type="checkbox" value="yes" <?php if ($pconfig['enable']) echo "checked"; ?> onClick="enable_change(false)"> <strong><?=gettext("Enable");?></strong></td>
        </tr>
  		  </table>
      </td>
    </tr>
    <tr>
	    <td width="22%" valign="top" class="vncellreq"><?=gettext("Servername");?></td>
      <td width="78%" class="vtable">
        <?=$mandfldhtml;?>
        <select name="servername" class="formfld" id="servername">
					<option value="dyndns@dyndns.org" <?php if ("dyndns@dyndns.org" == $pconfig['servername']) echo "selected";?>>dyndns.org</option>
					<option value="default@freedns.afraid.org" <?php if ("default@freedns.afraid.org" == $pconfig['servername']) echo "selected";?>>freedns.afraid.org</option>
					<option value="default@zoneedit.com" <?php if ("default@zoneedit.com" == $pconfig['servername']) echo "selected";?>>www.zoneedit.com</option>
					<option value="default@no-ip.com" <?php if ("default@no-ip.com" == $pconfig['servername']) echo "selected";?>>www.no-ip.com</option>
        </select>
      </td>
		</tr>
		<tr>
      <td width="22%" valign="top" class="vncellreq"><?=gettext("Hostname");?></td>
      <td width="78%" class="vtable">
        <?=$mandfldhtml;?>
        <input name="hostname" type="text" class="formfld" id="hostname" size="20" value="<?=htmlentities($pconfig['hostname']);?>">
      </td>
    </tr>
		<tr>
      <td width="22%" valign="top" class="vncellreq"><?=gettext("Username");?></td>
      <td width="78%" class="vtable">
        <?=$mandfldhtml;?>
        <input name="username" type="text" class="formfld" id="username" size="20" value="<?=htmlentities($pconfig['username']);?>">
      </td>
    </tr>
    <tr>
      <td width="22%" valign="top" class="vncellreq"><?=gettext("Password");?></td>
      <td width="78%" class="vtable">
        <?=$mandfldhtml;?>
        <input name="password" type="password" class="formfld" id="password" size="20" value="<?=htmlentities($pconfig['password']);?>">
      </td>
		</tr>
		<tr>
			<td width="22%" valign="top" class="vncell"><?=gettext("Update period");?></td>
			<td width="78%" class="vtable">
				<input name="updateperiod" type="text" class="formfld" id="updateperiod" size="20" value="<?=htmlentities($pconfig['updateperiod']);?>">
				<br><?=gettext("How often the IP is checked. The period is in seconds (max. is 10 days).");?>
			</td>
		</tr>
		<tr>
			<td width="22%" valign="top" class="vncell"><?=gettext("Forced update period");?></td>
			<td width="78%" class="vtable">
				<input name="forcedupdateperiod" type="text" class="formfld" id="forcedupdateperiod" size="20" value="<?=htmlentities($pconfig['forcedupdateperiod']);?>">
				<br><?=gettext("How often the IP is updated even if it is not changed. The period is in seconds (max. is 10 days).");?>
			</td>
		</tr>
    <tr>
      <td width="22%" valign="top">&nbsp;</td>
      <td width="78%">
        <input name="Submit" type="submit" class="formbtn" value="<?=gettext("Save and Restart DynDNS");?>" onClick="enable_change(true)">
      </td>
    </tr>
  </table>
</form>
<script language="JavaScript">
<!--
enable_change(false);
//-->
</script>
<?php include("fend.inc"); ?>
