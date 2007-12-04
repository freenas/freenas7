#!/usr/local/bin/php
<?php
/*
	acces_ad.php
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

$pgtitle = array(gettext("Access"),gettext("Active Directory"));

if (!is_array($config['ad'])) {
	$config['ad'] = array();
}

if (!is_array($config['samba'])) {
	$config['samba'] = array();
}

$pconfig['enable'] = isset($config['ad']['enable']);
$pconfig['admin_name'] = $config['ad']['admin_name'];
$pconfig['admin_pass'] = $config['ad']['admin_pass'];
$pconfig['admin_pass2'] = $config['ad']['admin_pass'];
$pconfig['ad_srv_name'] = $config['ad']['ad_srv_name'];
$pconfig['ad_srv_ip'] = $config['ad']['ad_srv_ip'];
$pconfig['domain_name'] = $config['samba']['workgroup'];

if ($_POST) {
	unset($input_errors);
	$pconfig = $_POST;

	/* input validation */
	$reqdfields = array();
	$reqdfieldsn = array();

	if ($_POST['enable']) {
		$reqdfields = array_merge($reqdfields, explode(" ", "admin_name admin_pass ad_srv_ip"));
		$reqdfieldsn = array_merge($reqdfieldsn, array(gettext("Administrator name"),gettext("Administration password"),gettext("AD server IP")));
	}

	do_input_validation($_POST, $reqdfields, $reqdfieldsn, &$input_errors);

	if ($_POST['enable'] && !is_ipaddr($_POST['ad_srv_ip'])){
  		$input_errors[] = gettext("A valid IP address must be specified.");
  	}

  	if (($_POST['admin_pass'] != $_POST['admin_pass2'])) {
		$input_errors[] = gettext("Password don't match.");
	}

	if (!$input_errors)
	{
		$config['ad']['admin_name'] = $_POST['admin_name'];
		$config['ad']['admin_pass'] = $_POST['admin_pass'];
		$config['ad']['ad_srv_ip'] = $_POST['ad_srv_ip'];
		$config['ad']['ad_srv_name'] = $_POST['ad_srv_name'];
		$config['samba']['workgroup'] = $_POST['domain_name'];

		$config['ad']['enable'] = $_POST['enable'] ? true : false;

		if ($config['ad']['enable'])
		{
			$config['samba']['security'] = "domain";
			$config['samba']['enable'] =  true;
			$config['samba']['winssrv'] = $config['ad']['ad_srv_ip'];
		}
		else
			$config['samba']['security'] = "user";

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
	var endis;

	endis = !(document.iform.enable.checked || enable_change);
	document.iform.ad_srv_name.disabled = endis;
	document.iform.ad_srv_ip.disabled = endis;
	document.iform.domain_name.disabled = endis;
	document.iform.admin_name.disabled = endis;
	document.iform.admin_pass.disabled = endis;
	document.iform.admin_pass2.disabled = endis;
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
			      <td width="22%" valign="top" class="vncellreq"><?=gettext("AD server name");?></td>
			      <td width="78%" class="vtable">
			        <input name="ad_srv_name" type="text" class="formfld" id="ad_srv_name" size="20" value="<?=htmlspecialchars($pconfig['ad_srv_name']);?>">
			      	<br><?=gettext("AD or PDC name.");?>
						</td>
					</tr>
					<tr>
			      <td width="22%" valign="top" class="vncellreq"><?=gettext("AD server IP");?></td>
			      <td width="78%" class="vtable">
			        <input name="ad_srv_ip" type="text" class="formfld" id="ad_srv_ip" size="20" value="<?=htmlspecialchars($pconfig['ad_srv_ip']);?>">
			      	<br><?=gettext("IP address of MS Active Directory server.");?>
						</td>
					</tr>
					<tr>
			      <td width="22%" valign="top" class="vncellreq"><?=gettext("Domain name");?></td>
			      <td width="78%" class="vtable">
			        <input name="domain_name" type="text" class="formfld" id="domain_name" size="20" value="<?=htmlspecialchars($pconfig['domain_name']);?>">
							<br><?=gettext("Domain name in old format.");?>
						</td>
					</tr>
			    <tr>
			      <td width="22%" valign="top" class="vncellreq"><?=gettext("Administrator name");?></td>
			      <td width="78%" class="vtable">
			        <input name="admin_name" type="text" class="formfld" id="admin_name" size="20" value="<?=htmlspecialchars($pconfig['admin_name']);?>">
							<br><?=gettext("Username of a domain administrator account.");?>
						</td>
					</tr>
					<tr>
			      <td width="22%" valign="top" class="vncellreq"><?=gettext("Administration password");?></td>
			      <td width="78%" class="vtable">
			      	<input name="admin_pass" type="password" class="formfld" id="admin_pass" size="20" value="<?=htmlspecialchars($pconfig['admin_pass']);?>"><br>
							<input name="admin_pass2" type="password" class="formfld" id="admin_pass2" size="20" value="<?=htmlspecialchars($pconfig['admin_pass2']);?>">
			        &nbsp;(<?=gettext("Confirmation");?>)<br>
			        <span class="vexpl"><?=gettext("Password of domain administrator account, enter it here twice.");?></span>
						</td>
			    </tr>
					<tr>
			      <td width="22%" valign="top">&nbsp;</td>
			      <td width="78%">
			        <input name="Submit" type="submit" class="formbtn" value="<?=gettext("Save");?>" onClick="enable_change(true)">
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
