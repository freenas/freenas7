#!/usr/local/bin/php
<?php
/*
	acces_ldap.php
	part of FreeNAS (http://www.freenas.org)
	Copyright (C) 2005-2008 Olivier Cochard-Labbe <olivier@freenas.org>.
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

$pgtitle = array(gettext("Access"), gettext("LDAP"));

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
$pconfig['bindpw'] = $config['ldap']['bindpw'];
$pconfig['bindpw2'] = $config['ldap']['bindpw'];
$pconfig['user_suffix'] = $config['ldap']['user_suffix'];
$pconfig['password_suffix'] = $config['ldap']['password_suffix'];
$pconfig['group_suffix'] = $config['ldap']['group_suffix'];
$pconfig['pam_password'] = $config['ldap']['pam_password'];
if (is_array($config['ldap']['auxparam']))
	$pconfig['auxparam'] = implode("\n", $config['ldap']['auxparam']);

if ($_POST) {
	unset($input_errors);
	$pconfig = $_POST;

	// Input validation.
	if ($_POST['enable']) {
		$reqdfields = explode(" ", "hostname base binddn bindpw user_suffix group_suffix password_suffix");
		$reqdfieldsn = array(gettext("Host name"), gettext("Base DN"), gettext("Bind DN"), gettext("Bind DN Password"), gettext("User suffix"), gettext("Group suffix"), gettext("Password suffix"));
		$reqdfieldst = explode(" ", "string string string string string string string");

		do_input_validation($_POST, $reqdfields, $reqdfieldsn, &$input_errors);
		do_input_validation_type($_POST, $reqdfields, $reqdfieldsn, $reqdfieldst, &$input_errors);
	}

	if (($_POST['bindpw'] !== $_POST['bindpw2'])) {
		$input_errors[] = gettext("The confimed password does not match. Please ensure the passwords match exactly.");
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

		# Write additional parameters.
		unset($config['ldap']['auxparam']);
		foreach (explode("\n", $_POST['auxparam']) as $auxparam) {
			$auxparam = trim($auxparam, "\t\n\r");
			if (!empty($auxparam))
				$config['ldap']['auxparam'][] = $auxparam;
		}

		// Disable AD
		if ($config['ldap']['enable']) {
			$config['samba']['security'] = "user";
			$config['ad']['enable'] = false;
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
	document.iform.auxparam.disabled = endis;
}
//-->
</script>
<form action="access_ldap.php" method="post" name="iform" id="iform">
	<table width="100%" border="0" cellpadding="0" cellspacing="0">
		<tr>
			<td class="tabcont">
				<?php if ($input_errors) print_input_errors($input_errors);?>
				<?php if ($savemsg) print_info_box($savemsg);?>
				<table width="100%" border="0" cellpadding="6" cellspacing="0">
					<?php html_titleline_checkbox("enable", gettext("Lightweight Directory Access Protocol"), $pconfig['enable'] ? true : false, gettext("Enable"), "enable_change(false)");?>
					<?php html_inputbox("hostname", gettext("Host name"), $pconfig['hostname'], gettext("The name or IP address of the LDAP server."), true, 20);?>
					<?php html_inputbox("base", gettext("Base DN"), $pconfig['base'], sprintf(gettext("The distinguished name to use as a base for queries, e.g. %s"), "dc=test,dc=org"), true, 40);?>
					<?php html_inputbox("binddn", gettext("Bind DN"), $pconfig['binddn'], sprintf(gettext("The distinguished name to bind to the LDAP server as, e.g. %s"), "cn=admin,dc=test,dc=org"), true, 40);?>
					<?php html_passwordconfbox("bindpw", "bindpw2", gettext("Bind DN Password"), $pconfig['bindpw'], $pconfig['bindpw2'], gettext("The credentials to bind with, enter it here twice."), true);?>
					<?php html_combobox("pam_password", gettext("Password encryption"), $pconfig['pam_password'], array("clear" => "clear", "crypt" => "crypt", "md5" => "md5", "nds" => "nds", "ad" => "ad", "exop" => "exop"), gettext("Method used to store your password in your LDAP."), true);?>
					<?php html_inputbox("user_suffix", gettext("User suffix"), $pconfig['user_suffix'], sprintf(gettext("This parameter specifies the suffix that is used for users when these are added to the LDAP directory, e.g. %s"), "ou=users,dc=test,dc=org"), true, 40);?>
					<?php html_inputbox("group_suffix", gettext("Group suffix"), $pconfig['group_suffix'], sprintf(gettext("This parameter specifies the suffix that is used for groups when these are added to the LDAP directory, e.g. %s"), "ou=groups,dc=test,dc=org"), true, 40);?>
					<?php html_inputbox("password_suffix", gettext("Password suffix"), $pconfig['password_suffix'], sprintf(gettext("This parameter specifies the suffix that is used for passwords when these are added to the LDAP directory, e.g. %s"), "ou=users,dc=test,dc=org"), true, 40);?>
					<?php html_textarea("auxparam", gettext("Auxiliary parameters"), $pconfig['auxparam'], sprintf(gettext("These parameters will be added to %s."), "ldap.conf"), false, 65, 5);?>
					</tr>
				</table>
				<div id="submit">
					<input name="Submit" type="submit" class="formbtn" value="<?=gettext("Save");?>" onClick="enable_change(true)">
				</div>
				<div id="remarks">
					<?php html_remark("help", gettext("Help needed!"), gettext("LDAP authentication feature is not implemented: If you know how to use PAM to authenticate UNIX services (FTP,SSH, etc...) AND Samba against an LDAP server... Your patchs are welcome."));?>
				</div>
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
