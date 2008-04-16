#!/usr/local/bin/php
<?php
/*
	acces_ad.php
	part of FreeNAS (http://www.freenas.org)
	Copyright (C) 2005-2008 Olivier Cochard-Labb� <olivier@freenas.org>.
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

$pgtitle = array(gettext("Access"), gettext("Active Directory"));

if (!is_array($config['ad'])) {
	$config['ad'] = array();
}

if (!is_array($config['samba'])) {
	$config['samba'] = array();
}

$pconfig['enable'] = isset($config['ad']['enable']);
$pconfig['domaincontrollername'] = $config['ad']['domaincontrollername'];
$pconfig['domainname_dns'] = $config['ad']['domainname_dns'];
$pconfig['domainname_netbios'] = $config['ad']['domainname_netbios'];
$pconfig['username'] = $config['ad']['username'];
$pconfig['password'] = $config['ad']['password'];
$pconfig['password2'] = $config['ad']['password'];

if ($_POST) {
	unset($input_errors);
	$pconfig = $_POST;

	// Input validation.
	if ($_POST['enable']) {
		$reqdfields = explode(" ", "domaincontrollername domainname_dns domainname_netbios username password");
		$reqdfieldsn = array(gettext("Domain controller name"), gettext("Domain name (DNS/Realm-Name)"), gettext("Domain name (NetBIOS-Name)"), gettext("Administrator name"), gettext("Administration password"));
		$reqdfieldst = explode(" ", "string domain domain string string");

		do_input_validation($_POST, $reqdfields, $reqdfieldsn, &$input_errors);
		do_input_validation_type($_POST, $reqdfields, $reqdfieldsn, $reqdfieldst, &$input_errors);

		if (($_POST['password'] !== $_POST['password2'])) {
			$input_errors[] = gettext("The confimed password does not match. Please ensure the passwords match exactly.");
		}
	}

	if (!$input_errors) {
		$config['ad']['domaincontrollername'] = $_POST['domaincontrollername'];
		$config['ad']['domainname_dns'] = $_POST['domainname_dns'];
		$config['ad']['domainname_netbios'] = $_POST['domainname_netbios'];
		$config['ad']['username'] = $_POST['username'];
		$config['ad']['password'] = $_POST['password'];
		$config['ad']['enable'] = $_POST['enable'] ? true : false;

		if ($config['ad']['enable']) {
			$config['samba']['enable'] = true;
			$config['samba']['security'] = "domain";
			$config['samba']['workgroup'] = $_POST['domainname_netbios'];
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
<?php include("fbegin.inc");?>
<script language="JavaScript">
<!--
function enable_change(enable_change) {
	var endis = !(document.iform.enable.checked || enable_change);
	document.iform.domaincontrollername.disabled = endis;
	document.iform.domainname_dns.disabled = endis;
	document.iform.domainname_netbios.disabled = endis;
	document.iform.username.disabled = endis;
	document.iform.password.disabled = endis;
	document.iform.password2.disabled = endis;
}
//-->
</script>
<form action="access_ad.php" method="post" name="iform" id="iform">
	<table width="100%" border="0" cellpadding="0" cellspacing="0">
	  <tr>
	    <td class="tabcont">
	    	<?php if ($input_errors) print_input_errors($input_errors);?>
				<?php if ($savemsg) print_info_box($savemsg);?>
				<table width="100%" border="0" cellpadding="6" cellspacing="0">
			    <tr>
			      <td colspan="2" valign="top" class="optsect_t">
						  <table border="0" cellspacing="0" cellpadding="0" width="100%">
							  <tr>
									<td class="optsect_s"><strong><?=gettext("Active Directory");?></strong></td>
							  	<td align="right" class="optsect_s">
										<input name="enable" type="checkbox" value="yes" <?php if ($pconfig['enable']) echo "checked"; ?> onClick="enable_change(false)"> <strong><?=gettext("Enable");?></strong>
									</td>
								</tr>
						  </table>
						</td>
			    </tr>
			    <tr>
			      <td width="22%" valign="top" class="vncellreq"><?=gettext("Domain controller name");?></td>
			      <td width="78%" class="vtable">
			        <input name="domaincontrollername" type="text" class="formfld" id="domaincontrollername" size="20" value="<?=htmlspecialchars($pconfig['domaincontrollername']);?>">
			      	<br/><span class="vexpl"><?=gettext("AD or PDC name.");?></span>
						</td>
					</tr>
					<tr>
			      <td width="22%" valign="top" class="vncellreq"><?=gettext("Domain name (DNS/Realm-Name)");?></td>
			      <td width="78%" class="vtable">
			        <input name="domainname_dns" type="text" class="formfld" id="domainname_dns" size="20" value="<?=htmlspecialchars($pconfig['domainname_dns']);?>">
							<br/><span class="vexpl"><?=gettext("Domain name, e.g. example.com.");?></span>
						</td>
					</tr>
					<tr>
			      <td width="22%" valign="top" class="vncellreq"><?=gettext("Domain name (NetBIOS-Name)");?></td>
			      <td width="78%" class="vtable">
			        <input name="domainname_netbios" type="text" class="formfld" id="domainname_netbios" size="20" value="<?=htmlspecialchars($pconfig['domainname_netbios']);?>">
							<br/><span class="vexpl"><?=gettext("Domain name in old format, e.g. EXAMPLE.");?></span>
						</td>
					</tr>
			    <tr>
			      <td width="22%" valign="top" class="vncellreq"><?=gettext("Administrator name");?></td>
			      <td width="78%" class="vtable">
			        <input name="username" type="text" class="formfld" id="username" size="20" value="<?=htmlspecialchars($pconfig['username']);?>">
							<br/><span class="vexpl"><?=gettext("Username of a domain administrator account.");?></span>
						</td>
					</tr>
					<tr>
			      <td width="22%" valign="top" class="vncellreq"><?=gettext("Administration password");?></td>
			      <td width="78%" class="vtable">
			      	<input name="password" type="password" class="formfld" id="password" size="20" value="<?=htmlspecialchars($pconfig['password']);?>"><br>
							<input name="password2" type="password" class="formfld" id="password2" size="20" value="<?=htmlspecialchars($pconfig['password2']);?>">
			        &nbsp;(<?=gettext("Confirmation");?>)
							<br/><span class="vexpl"><?=gettext("Password of domain administrator account.");?></span>
						</td>
			    </tr>
					<tr>
			      <td width="22%" valign="top">&nbsp;</td>
			      <td width="78%">
			        <input name="Submit" type="submit" class="formbtn" value="<?=gettext("Save");?>" onClick="enable_change(true)">
			      </td>
			    </tr>
			    <tr>
			      <td width="22%" valign="top">&nbsp;</td>
			      <td width="78%">
							<span class="vexpl"><span class="red"><strong><?=gettext("Note");?>:</strong></span><br/>
							<?=gettext("To use Active Directory the CIFS/SMB service will enabled, too. The following services will use AD authentication:<br/><ul><li>CIFS/SMB</li><li>SSH</li><li>FTP</li><li>AFP</li><li>System</li></ul>");?></span>
						</td>
			    </tr>
			  </table>
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
