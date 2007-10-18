#!/usr/local/bin/php
<?php
/*
	acces_ldap.php
	part of FreeNAS (http://www.freenas.org)
	Copyright (C) 2005-2007 Olivier Cochard-Labbe <olivier@freenas.org>.
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

$pgtitle = array(gettext("Access"),gettext("LDAP"));

if (!is_array($config['ldap'])) {
	$config['ldap'] = array();
}

if (!is_array($config['samba'])) {
	$config['samba'] = array();
}

#LDAP take priority over MS ActiveDirectory (FreeNAS choicee), then disable AD:
if (!is_array($config['ad'])) {
	$config['ad'] = array();
}

$pconfig['enable'] = isset($config['ldap']['enable']);
$pconfig['hostname'] = $config['ldap']['hostname'];
$pconfig['base'] = $config['ldap']['base'];
$pconfig['binddn'] = $config['ldap']['binddn'];
$pconfig['bindpw2'] = $pconfig['bindpw'] = $config['ldap']['bindpw'];
$pconfig['user_suffix'] = $config['ldap']['user_suffix'];
$pconfig['password_suffix'] = $config['ldap']['password_suffix'];
$pconfig['group_suffix'] = $config['ldap']['group_suffix'];
$pconfig['pam_password'] = $config['ldap']['pam_password'];

if ($_POST) {
	unset($input_errors);
	$pconfig = $_POST;

	/* input validation */
	$reqdfields = array();
	$reqdfieldsn = array();

	if ($_POST['enable']) {
		$reqdfields = array_merge($reqdfields, explode(" ", "hostname base binddn bindpw user_suffix password_suffix group_suffix"));
		$reqdfieldsn = array_merge($reqdfieldsn, array(gettext("LDAP server name or IP"),gettext("Base DN"),gettext("DN to bind"),gettext("Password for DN"),gettext("User suffix"),gettext("Password suffix"),gettext("Group suffix")));
	}

	do_input_validation($_POST, $reqdfields, $reqdfieldsn, &$input_errors);

  if (($_POST['bindpw'] != $_POST['bindpw2'])) {
		$input_errors[] = gettext("Password don't match.");
	}

	if (!$input_errors) {
		$config['ldap']['enable'] = $_POST['enable'] ? true : false;
		$config['ldap']['hostname'] = $_POST['hostname'];
		$config['ldap']['base'] = $_POST['base'];
		$config['ldap']['binddn'] = $_POST['binddn'];
		$config['ldap']['bindpw'] = $_POST['bindpw'];
		$config['ldap']['user_suffix'] = $_POST['user_suffix'];
		$config['ldap']['password_suffix'] = $_POST['password_suffix'];
		$config['ldap']['group_suffix'] = $_POST['group_suffix'];
		$config['ldap']['pam_password'] = $_POST['pam_password'];

		// Disable AD
		if ($config['ldap']['enable']) {
			$config['samba']['security'] = "user";
			$config['ad']['enable'] = false ;
		}

		write_config();

		$retval = 0;
		if (!file_exists($d_sysrebootreqd_path)) {
			config_lock();
			rc_exec_service("pam");
			rc_exec_service("ldap");
			rc_start_service("nsswitch");
			rc_update_service("samba");
			config_unlock();
		}
		$savemsg = get_std_save_message($retval);
	}
}
?>
<?php include("fbegin.inc"); ?>
<script language="JavaScript">
<!--
function enable_change(enable_change) {
	var endis = !(document.iform.enable.checked || enable_change);
	document.iform.hostname.disabled = endis;
	document.iform.base.disabled = endis;
	document.iform.binddn.disabled = endis;
	document.iform.bindpw.disabled = endis;
	document.iform.bindpw2.disabled = endis;
	document.iform.user_suffix.disabled = endis;
	document.iform.password_suffix.disabled = endis;
	document.iform.group_suffix.disabled = endis;
	document.iform.pam_password.disabled = endis;
}
//-->
</script>
<?php if ($input_errors) print_input_errors($input_errors); ?>
<?php if ($savemsg) print_info_box($savemsg); ?>
<form action="access_ldap.php" method="post" name="iform" id="iform">
	<table width="100%" border="0" cellpadding="6" cellspacing="0">
    <tr>
      <td colspan="2" valign="top" class="optsect_t">
			  <table border="0" cellspacing="0" cellpadding="0" width="100%">
				  <tr>
						<td class="optsect_s"><strong><?=gettext("LDAP");?></strong></td>
				  	<td align="right" class="optsect_s">
							<input name="enable" type="checkbox" value="yes" <?php if ($pconfig['enable']) echo "checked"; ?> onClick="enable_change(false)"> <strong><?=gettext("Enable");?></strong>
						</td>
					</tr>
			  </table>
			</td>
    </tr>
		<tr>
      <td width="22%" valign="top" class="vncellreq"><?=gettext("LDAP server name");?></td>
      <td width="78%" class="vtable">
        <input name="hostname" type="text" class="formfld" id="hostname" size="20" value="<?=htmlspecialchars($pconfig['hostname']);?>">
      	<br><?=gettext("Hostname or IP address of LDAP server. Warning: Use of hostname is mandatory for TLS");?>
			</td>
		</tr>
		<tr>
      <td width="22%" valign="top" class="vncellreq"><?=gettext("Base DN");?></td>
      <td width="78%" class="vtable">
        <input name="base" type="text" class="formfld" id="base" size="20" value="<?=htmlspecialchars($pconfig['base']);?>">
      	<br><?=gettext("Specifies the default base DN to use when performing ldap operations, example: dc=example,dc=com");?>
			</td>
		</tr>
    <tr>
      <td width="22%" valign="top" class="vncellreq"><?=gettext("DN to bind");?></td>
      <td width="78%" class="vtable">
        <input name="binddn" type="text" class="formfld" id="binddn" size="20" value="<?=htmlspecialchars($pconfig['binddn']);?>">
				<br><?=gettext("Specifies the default bind DN to use when performing ldap operations, example:cn=administrator,dc=example,dc=com ");?>
			</td>
		</tr>
		<tr>
      <td width="22%" valign="top" class="vncellreq"><?=gettext("Password for DN");?></td>
      <td width="78%" class="vtable">
      	<input name="bindpw" type="password" class="formfld" id="bindpw" size="20" value="<?=htmlspecialchars($pconfig['bindpw']);?>"><br>
				<input name="bindpw2" type="password" class="formfld" id="bindpw2" size="20" value="<?=htmlspecialchars($pconfig['bindpw2']);?>">
        &nbsp;(<?=gettext("Confirmation");?>)<br>
        <span class="vexpl"><?=gettext("The credentials to bind with, enter it here twice.");?></span>
			</td>
    </tr>
		<tr>
		  <tr>
      <td width="22%" valign="top" class="vncellreq"><?=gettext("User suffix");?></td>
      <td width="78%" class="vtable">
        <input name="user_suffix" type="text" class="formfld" id="user_suffix" size="20" value="<?=htmlspecialchars($pconfig['user_suffix']);?>">
      	<br><?=gettext("user_suffix, example: ou=users,dc=example,dc=com");?>
			</td>
		</tr>
		<tr>
		  <tr>
	      <td width="22%" valign="top" class="vncellreq"><?=gettext("Password suffix");?></td>
	      <td width="78%" class="vtable">
	        <input name="password_suffix" type="text" class="formfld" id="password_suffix" size="20" value="<?=htmlspecialchars($pconfig['password_suffix']);?>">
	      	<br><?=gettext("password_suffix, example: ou=users,dc=example,dc=com");?>
				</td>
			</tr>
		  <tr>
	      <td width="22%" valign="top" class="vncellreq"><?=gettext("Group suffix");?></td>
	      <td width="78%" class="vtable">
	        <input name="group_suffix" type="text" class="formfld" id="group_suffix" size="20" value="<?=htmlspecialchars($pconfig['group_suffix']);?>">
	      	<br><?=gettext("group_suffix, example: ou=groups,dc=example,dc=com");?>
				</td>
			</tr>
			<tr>
	      <td width="22%" valign="top" class="vncellreq"><?=gettext("Password encryption"); ?></td>
				<td width="78%" class="vtable">
					<select name="pam_password" class="formfld" id="pam_password">
		        <?php $types = explode(",", "clear,crypt,md5,nds,ad,exop"); $vals = explode(" ", "clear crypt md5 nds ad exop");?>
		        <?php $j = 0; for ($j = 0; $j < count($vals); $j++): ?>
		          <option value="<?=$vals[$j];?>" <?php if ($vals[$j] == $pconfig['pam_password']) echo "selected";?>>
		          <?=htmlspecialchars($types[$j]);?>
		          </option>
		        <?php endfor; ?>
	        </select>
				  <br><?=gettext("Method used to store your password in your LDAP.");?>
				</td>
			</tr>
			<td width="22%" valign="top">&nbsp;</td>
			<td width="78%">
        <input name="Submit" type="submit" class="formbtn" value="<?=gettext("Save");?>" onClick="enable_change(true)">
      </td>
    </tr>
		<tr>
			<td width="22%" valign="top">&nbsp;</td>
			<td width="78%">
				<span class="red"><strong><?=gettext("Help Needed!");?>:</strong></span>
				<br><?php echo gettext("LDAP authentication feature is not implemented: If you know how to use PAM to authenticate UNIX services (FTP,SSH, etc...) AND Samba against an LDAP server... Your patchs are welcome.");?>
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