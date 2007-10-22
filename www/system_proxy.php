#!/usr/local/bin/php
<?php
/*
	system_proxy.php
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

$pgtitle = array(gettext("System"),gettext("Advanced"),gettext("Proxy"));

if (!is_array($config['system']['proxy']['http'])) {
	$config['system']['proxy']['http'] = array();
}

if (!is_array($config['system']['proxy']['ftp'])) {
	$config['system']['proxy']['ftp'] = array();
}

$pconfig['http_enable'] = isset($config['system']['proxy']['http']['enable']);
$pconfig['http_address'] = $config['system']['proxy']['http']['address'];
$pconfig['http_port'] = $config['system']['proxy']['http']['port'];
$pconfig['http_auth'] = isset($config['system']['proxy']['http']['auth']);
$pconfig['http_username'] = $config['system']['proxy']['http']['username'];
$pconfig['http_password'] = $config['system']['proxy']['http']['password'];

$pconfig['ftp_enable'] = isset($config['system']['proxy']['ftp']['enable']);
$pconfig['ftp_address'] = $config['system']['proxy']['ftp']['address'];
$pconfig['ftp_port'] = $config['system']['proxy']['ftp']['port'];
$pconfig['ftp_auth'] = isset($config['system']['proxy']['ftp']['auth']);
$pconfig['ftp_username'] = $config['system']['proxy']['ftp']['username'];
$pconfig['ftp_password'] = $config['system']['proxy']['ftp']['password'];

if ($_POST) {
	unset($input_errors);
	$pconfig = $_POST;

	$reqdfields = array();
	$reqdfieldsn = array();
	$reqdfieldst = array();

	if ($_POST['http_enable']) {
		$reqdfields = array_merge($reqdfields, explode(" ", "http_address http_port"));
		$reqdfieldsn = array_merge($reqdfieldsn, array(gettext("Address"),gettext("Port")));
		$reqdfieldst = array_merge($reqdfieldst,array("string","numeric"));

		if ($_POST['http_auth']) {
			$reqdfields = array_merge($reqdfields, explode(" ", "http_username http_password"));
			$reqdfieldsn = array_merge($reqdfieldsn, array(gettext("User"),gettext("Password")));
			$reqdfieldst = array_merge($reqdfieldst,array("string","password"));
		}
	}

	if ($_POST['ftp_enable']) {
		$reqdfields = array_merge($reqdfields, explode(" ", "ftp_address ftp_port"));
		$reqdfieldsn = array_merge($reqdfieldsn, array(gettext("Address"),gettext("Port")));
		$reqdfieldst = array_merge($reqdfieldst,array("string","numeric"));

		if ($_POST['ftp_auth']) {
			$reqdfields = array_merge($reqdfields, explode(" ", "ftp_username ftp_password"));
			$reqdfieldsn = array_merge($reqdfieldsn, array(gettext("User"),gettext("Password")));
			$reqdfieldst = array_merge($reqdfieldst,array("string","password"));
		}
	}

	do_input_validation($_POST, $reqdfields, $reqdfieldsn, &$input_errors);
	do_input_validation_type($_POST, $reqdfields, $reqdfieldsn, $reqdfieldst, &$input_errors);

	if ($_POST['http_auth']) {
		if (($_POST['password'] && !is_validpassword($_POST['password']))) {
			$input_errors[] = gettext("The password contains the illegal character ':'.");
		}
	}

	if (!$input_errors) {
		$config['system']['proxy']['http']['enable'] = $pconfig['http_enable'] ? true : false;
		$config['system']['proxy']['http']['address'] = $pconfig['http_address'];
		$config['system']['proxy']['http']['port'] = $pconfig['http_port'];
		$config['system']['proxy']['http']['auth'] = $pconfig['http_auth'] ? true : false;
		$config['system']['proxy']['http']['username'] = $pconfig['http_username'];
		$config['system']['proxy']['http']['password'] = $pconfig['http_password'];

		$config['system']['proxy']['ftp']['enable'] = $pconfig['ftp_enable'] ? true : false;
		$config['system']['proxy']['ftp']['address'] = $pconfig['ftp_address'];
		$config['system']['proxy']['ftp']['port'] = $pconfig['ftp_port'];
		$config['system']['proxy']['ftp']['auth'] = $pconfig['ftp_auth'] ? true : false;
		$config['system']['proxy']['ftp']['username'] = $pconfig['ftp_username'];
		$config['system']['proxy']['ftp']['password'] = $pconfig['ftp_password'];

		write_config();

		$retval = 0;
		if (!file_exists($d_sysrebootreqd_path)) {
			config_lock();
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
	if (enable_change.name == "http_enable") {
		var endis = !enable_change.checked;
		document.iform.http_address.disabled = endis;
		document.iform.http_port.disabled = endis;
		document.iform.http_auth.disabled = endis;
		document.iform.http_username.disabled = endis;
		document.iform.http_password.disabled = endis;
	} else if (enable_change.name == "ftp_enable") {
		var endis = !enable_change.checked;
		document.iform.ftp_address.disabled = endis;
		document.iform.ftp_port.disabled = endis;
		document.iform.ftp_auth.disabled = endis;
		document.iform.ftp_username.disabled = endis;
		document.iform.ftp_password.disabled = endis;
	} else {
		var endis = !(document.iform.http_enable.checked || enable_change);
		document.iform.http_address.disabled = endis;
		document.iform.http_port.disabled = endis;
		document.iform.http_auth.disabled = endis;
		document.iform.http_username.disabled = endis;
		document.iform.http_password.disabled = endis;

		endis = !(document.iform.ftp_enable.checked || enable_change);
		document.iform.ftp_address.disabled = endis;
		document.iform.ftp_port.disabled = endis;
		document.iform.ftp_auth.disabled = endis;
		document.iform.ftp_username.disabled = endis;
		document.iform.ftp_password.disabled = endis;
	}
}

function proxy_auth_change() {
	switch(document.iform.http_auth.checked) {
		case false:
      showElementById('http_username_tr','hide');
  		showElementById('http_password_tr','hide');
      break;

    case true:
      showElementById('http_username_tr','show');
  		showElementById('http_password_tr','show');
      break;
	}

	switch(document.iform.ftp_auth.checked) {
		case false:
      showElementById('ftp_username_tr','hide');
  		showElementById('ftp_password_tr','hide');
      break;

    case true:
      showElementById('ftp_username_tr','show');
  		showElementById('ftp_password_tr','show');
      break;
	}
}
//-->
</script>
<table width="100%" border="0" cellpadding="0" cellspacing="0">
	<tr>
    <td class="tabnavtbl">
      <ul id="tabnav">
      	<li class="tabinact"><a href="system_advanced.php"><?=gettext("Advanced");?></a></li>
      	<li class="tabact"><a href="system_proxy.php" style="color:black" title="<?=gettext("Reload page");?>"><?=gettext("Proxy");?></a></li>
      	<li class="tabinact"><a href="system_swap.php"><?=gettext("Swap");?></a></li>
      	<li class="tabinact"><a href="system_rc.php"><?=gettext("Command scripts");?></a></li>
        <li class="tabinact"><a href="system_cron.php"><?=gettext("Cron");?></a></li>
      </ul>
    </td>
  </tr>
  <tr>
    <td class="tabcont">
    	<form action="system_proxy.php" method="post" name="iform" id="iform">
				<?php if ($input_errors) print_input_errors($input_errors);?>
				<?php if ($savemsg) print_info_box($savemsg);?>
			  <table width="100%" border="0" cellpadding="6" cellspacing="0">
			  	<tr>
            <td colspan="2" valign="top" class="optsect_t">
    				  <table border="0" cellspacing="0" cellpadding="0" width="100%">
    				    <tr>
                  <td class="optsect_s"><strong><?=gettext("HTTP Proxy");?></strong></td>
    				      <td align="right" class="optsect_s"><input name="http_enable" type="checkbox" value="yes" <?php if ($pconfig['http_enable']) echo "checked";?> onClick="enable_change(this)"> <strong><?=gettext("Enable");?></strong></td>
                </tr>
    				  </table>
            </td>
          </tr>
			    <tr>
			      <td width="22%" valign="top" class="vncellreq"><?=gettext("Address");?></td>
			      <td width="78%" class="vtable">
			        <input name="http_address" type="text" class="formfld" id="http_address" size="40" value="<?=htmlspecialchars($pconfig['http_address']);?>">
			      </td>
			    </tr>
			    <tr>
			      <td width="22%" valign="top" class="vncellreq"><?=gettext("Port");?></td>
			      <td width="78%" class="vtable">
			        <input name="http_port" type="text" class="formfld" id="http_port" size="10" value="<?=htmlspecialchars($pconfig['http_port']);?>">
			      </td>
			    </tr>
			    <tr>
			      <td width="22%" valign="top" class="vncell"><?=gettext("Authentication");?></td>
			      <td width="78%" class="vtable">
			        <input name="http_auth" type="checkbox" id="http_auth" value="yes" <?php if ($pconfig['http_auth']) echo "checked";?> onClick="proxy_auth_change()">
			        <?=gettext("Login is required.");?>
						</td>
			    </tr>
			    <tr id="http_username_tr">
				    <td width="22%" valign="top" class="vncellreq"><?=gettext("User");?></td>
			      <td width="78%" class="vtable">
			        <input name="http_username" type="text" class="formfld" id="http_username" size="20" value="<?=htmlentities($pconfig['http_username']);?>">
			      </td>
					</tr>
					<tr id="http_password_tr">
				    <td width="22%" valign="top" class="vncellreq"><?=gettext("Password");?></td>
			      <td width="78%" class="vtable">
			        <input name="http_password" type="password" class="formfld" id="http_password" size="20" value="<?=htmlentities($pconfig['http_password']);?>">
			      </td>
					</tr>
					<tr>
						<td colspan="2" class="list" height="12"></td>
					</tr>
			    <tr>
            <td colspan="2" valign="top" class="optsect_t">
    				  <table border="0" cellspacing="0" cellpadding="0" width="100%">
    				    <tr>
                  <td class="optsect_s"><strong><?=gettext("FTP Proxy");?></strong></td>
    				      <td align="right" class="optsect_s"><input name="ftp_enable" type="checkbox" value="yes" <?php if ($pconfig['ftp_enable']) echo "checked";?> onClick="enable_change(this)"> <strong><?=gettext("Enable");?></strong></td>
                </tr>
    				  </table>
            </td>
          </tr>
			    <tr>
			      <td width="22%" valign="top" class="vncellreq"><?=gettext("Address");?></td>
			      <td width="78%" class="vtable">
			        <input name="ftp_address" type="text" class="formfld" id="ftp_address" size="40" value="<?=htmlspecialchars($pconfig['ftp_address']);?>">
			      </td>
			    </tr>
			    <tr>
			      <td width="22%" valign="top" class="vncellreq"><?=gettext("Port");?></td>
			      <td width="78%" class="vtable">
			        <input name="ftp_port" type="text" class="formfld" id="ftp_port" size="10" value="<?=htmlspecialchars($pconfig['ftp_port']);?>">
			      </td>
			    </tr>
			    <tr>
			      <td width="22%" valign="top" class="vncell"><?=gettext("Authentication");?></td>
			      <td width="78%" class="vtable">
			        <input name="ftp_auth" type="checkbox" id="ftp_auth" value="yes" <?php if ($pconfig['ftp_auth']) echo "checked";?> onClick="proxy_auth_change()">
			        <?=gettext("Login is required.");?>
						</td>
			    </tr>
			    <tr id="ftp_username_tr">
				    <td width="22%" valign="top" class="vncellreq"><?=gettext("User");?></td>
			      <td width="78%" class="vtable">
			        <input name="ftp_username" type="text" class="formfld" id="ftp_username" size="20" value="<?=htmlentities($pconfig['ftp_username']);?>">
			      </td>
					</tr>
					<tr id="ftp_password_tr">
				    <td width="22%" valign="top" class="vncellreq"><?=gettext("Password");?></td>
			      <td width="78%" class="vtable">
			        <input name="ftp_password" type="password" class="formfld" id="ftp_password" size="20" value="<?=htmlentities($pconfig['ftp_password']);?>">
			      </td>
					</tr>
			    <tr>
			      <td width="22%" valign="top">&nbsp;</td>
			      <td width="78%">
			        <input name="Submit" type="submit" class="formbtn" value="<?=gettext("Save");?>" onClick="enable_change(true)">
			      </td>
			    </tr>
			  </table>
			</form>
		</td>
  </tr>
</table>
<script language="JavaScript">
<!--
proxy_auth_change();
enable_change(false);
//-->
</script>
<?php include("fend.inc");?>
