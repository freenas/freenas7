#!/usr/local/bin/php
<?php
/*
	services_upnp.php
	Copyright © 2006-2008 Volker Theile (votdev@gmx.de)
	All rights reserved.

	part of FreeNAS (http://www.freenas.org)
	Copyright (C) 2005-2008 Olivier Cochard <olivier@freenas.org>.
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

$pgtitle = array(gettext("Services"),gettext("Dynamic DNS"));

if(!is_array($config['dynamicdns']))
	$config['dynamicdns'] = array();

$pconfig['enable'] = isset($config['dynamicdns']['enable']);
$pconfig['provider'] = $config['dynamicdns']['provider'];
$pconfig['domainname'] = $config['dynamicdns']['domainname'];
$pconfig['username'] = $config['dynamicdns']['username'];
$pconfig['password'] = $config['dynamicdns']['password'];
$pconfig['updateperiod'] = $config['dynamicdns']['updateperiod'];
$pconfig['forcedupdateperiod'] = $config['dynamicdns']['forcedupdateperiod'];

if($_POST) {
	unset($input_errors);

	$pconfig = $_POST;

	/* input validation */
	if($_POST['enable']) {
		$reqdfields = explode(" ", "provider domainname username password");
		$reqdfieldsn = array(gettext("Provider"), gettext("Domain name"), gettext("Username"), gettext("Password"));
		do_input_validation($_POST, $reqdfields, $reqdfieldsn, &$input_errors);

		$reqdfields = array_merge($reqdfields, explode(" ", "updateperiod forcedupdateperiod"));
		$reqdfieldsn = array_merge($reqdfieldsn, array(gettext("Update period"),gettext("Forced update period")));
		$reqdfieldst = explode(" ", "string string string string numeric numeric");
		do_input_validation_type($_POST, $reqdfields, $reqdfieldsn, $reqdfieldst, &$input_errors);
	}

	if(!$input_errors) {
    $config['dynamicdns']['enable'] = $_POST['enable'] ? true : false;
    $config['dynamicdns']['provider'] = $_POST['provider'];
		$config['dynamicdns']['domainname'] = $_POST['domainname'];
		$config['dynamicdns']['username'] = $_POST['username'];
		$config['dynamicdns']['password'] = $_POST['password'];
		$config['dynamicdns']['updateperiod'] = $_POST['updateperiod'];
		$config['dynamicdns']['forcedupdateperiod'] = $_POST['forcedupdateperiod'];

		write_config();

		$retval = 0;
		if(!file_exists($d_sysrebootreqd_path)) {
			config_lock();
			$retval |= rc_update_service("inadyn");
			config_unlock();
		}

		$savemsg = get_std_save_message($retval);
	}
}
// Get list of available interfaces.
$a_interface = get_interface_list();
?>
<?php include("fbegin.inc");?>
<script language="JavaScript">
<!--
function enable_change(enable_change) {
	var endis = !(document.iform.enable.checked || enable_change);
	document.iform.provider.disabled = endis;
	document.iform.domainname.disabled = endis;
	document.iform.username.disabled = endis;
	document.iform.password.disabled = endis;
	document.iform.updateperiod.disabled = endis;
	document.iform.forcedupdateperiod.disabled = endis;
}
//-->
</script>
<form action="services_dynamicdns.php" method="post" name="iform" id="iform">
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
			          <td class="optsect_s"><strong><?=gettext("Dynamic DNS");?></strong></td>
			  			  <td align="right" class="optsect_s"><input name="enable" type="checkbox" value="yes" <?php if ($pconfig['enable']) echo "checked"; ?> onClick="enable_change(false)"> <strong><?=gettext("Enable");?></strong></td>
			        </tr>
			  		  </table>
			      </td>
			    </tr>
			    <tr>
				    <td width="22%" valign="top" class="vncellreq"><?=gettext("Provider");?></td>
			      <td width="78%" class="vtable">
			        <select name="provider" class="formfld" id="provider">
								<option value="dyndns.org" <?php if ("dyndns.org" === $pconfig['provider']) echo "selected";?>>dyndns.org</option>
								<option value="freedns.afraid.org" <?php if ("freedns.afraid.org" === $pconfig['provider']) echo "selected";?>>freedns.afraid.org</option>
								<option value="zoneedit.com" <?php if ("zoneedit.com" === $pconfig['provider']) echo "selected";?>>zoneedit.com</option>
								<option value="no-ip.com" <?php if ("no-ip.com" === $pconfig['provider']) echo "selected";?>>no-ip.com</option>
								<option value="easydns.com" <?php if ("easydns.com" === $pconfig['provider']) echo "selected";?>>easydns.com</option>
								<option value="3322.org" <?php if ("3322.org" === $pconfig['provider']) echo "selected";?>>3322.org</option>
			        </select>
			      </td>
					</tr>
					<tr>
			      <td width="22%" valign="top" class="vncellreq"><?=gettext("Domain name");?></td>
			      <td width="78%" class="vtable">
			        <input name="domainname" type="text" class="formfld" id="domainname" size="40" value="<?=htmlspecialchars($pconfig['domainname']);?>"><br/>
							<span class="vexpl"><?=gettext("Alias host name.");?></span>
			      </td>
			    </tr>
					<tr>
			      <td width="22%" valign="top" class="vncellreq"><?=gettext("Username");?></td>
			      <td width="78%" class="vtable">
			        <input name="username" type="text" class="formfld" id="username" size="20" value="<?=htmlspecialchars($pconfig['username']);?>">
			      </td>
			    </tr>
			    <tr>
			      <td width="22%" valign="top" class="vncellreq"><?=gettext("Password");?></td>
			      <td width="78%" class="vtable">
			        <input name="password" type="password" class="formfld" id="password" size="20" value="<?=htmlspecialchars($pconfig['password']);?>">
			      </td>
					</tr>
					<tr>
						<td width="22%" valign="top" class="vncell"><?=gettext("Update period");?></td>
						<td width="78%" class="vtable">
							<input name="updateperiod" type="text" class="formfld" id="updateperiod" size="20" value="<?=htmlspecialchars($pconfig['updateperiod']);?>">
							<br><?=gettext("How often the IP is checked. The period is in seconds (max. is 10 days).");?>
						</td>
					</tr>
					<tr>
						<td width="22%" valign="top" class="vncell"><?=gettext("Forced update period");?></td>
						<td width="78%" class="vtable">
							<input name="forcedupdateperiod" type="text" class="formfld" id="forcedupdateperiod" size="20" value="<?=htmlspecialchars($pconfig['forcedupdateperiod']);?>">
							<br><?=gettext("How often the IP is updated even if it is not changed. The period is in seconds (max. is 10 days).");?>
						</td>
					</tr>
			    <tr>
			      <td width="22%" valign="top">&nbsp;</td>
			      <td width="78%">
			        <input name="Submit" type="submit" class="formbtn" value="<?=gettext("Save and Restart");?>" onClick="enable_change(true)">
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
