#!/usr/local/bin/php
<?php
/*
	services_inetd.php
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

$pgtitle = array(gettext("Services"), gettext("Inetd"), gettext("Settings"));

if (!is_array($config['inetd'])) {
	$config['inetd'] = array();
}

$pconfig['enable'] = isset($config['inetd']['enable']);
$pconfig['tcpwrapping'] = isset($config['inetd']['tcpwrapping']);
$pconfig['logging'] = isset($config['inetd']['logging']);
$pconfig['maxchild'] = $config['inetd']['maxchild'];
$pconfig['maxconnections'] = $config['inetd']['maxconnections'];
$pconfig['rate'] = $config['inetd']['rate'];
$pconfig['maxchildperip'] = $config['inetd']['maxchildperip'];

if ($_POST) {
	unset($input_errors);
	$pconfig = $_POST;

	if ($_POST['enable']) {
		// Input validation.
		if (!empty($_POST['maxchild'])) {
			$reqdfields = array("maxchild");
			$reqdfieldsn = array(gettext("max-child"));
			$reqdfieldst = explode(" ", "numericint");
			do_input_validation_type($_POST, $reqdfields, $reqdfieldsn, $reqdfieldst, &$input_errors);
		}
	
		if (!empty($_POST['maxconnections'])) {
			$reqdfields = array("maxconnections");
			$reqdfieldsn = array(gettext("max-connections-per-ip-per-minute"));
			$reqdfieldst = explode(" ", "numericint");
			do_input_validation_type($_POST, $reqdfields, $reqdfieldsn, $reqdfieldst, &$input_errors);
		}
	
		if (!empty($_POST['maxchildperip'])) {
			$reqdfields = array("maxchildperip");
			$reqdfieldsn = array(gettext("max-child-per-ip"));
			$reqdfieldst = explode(" ", "numericint");
			do_input_validation_type($_POST, $reqdfields, $reqdfieldsn, $reqdfieldst, &$input_errors);
		}
	}
		
	if (!$input_errors) {
		$config['inetd']['maxchild'] = $_POST['maxchild'];
		$config['inetd']['maxconnections'] = $_POST['maxconnections'];
		$config['inetd']['rate'] = $_POST['rate'];
		$config['inetd']['maxchildperip'] = $_POST['maxchildperip'];
		$config['inetd']['tcpwrapping'] = $_POST['tcpwrapping'] ? true : false;
		$config['inetd']['logging'] = $_POST['logging'] ? true : false;
		$config['inetd']['enable'] = $_POST['enable'] ? true : false;
	
		write_config();

		$retval = 0;
		if (!file_exists($d_sysrebootreqd_path)) {
			config_lock();
			$retval |= rc_update_service("inetd");
			config_unlock();
		}
		$savemsg = get_std_save_message($retval);
		
		if(0 == $retval) {
			unlink_if_exists($d_inetdserviceconfdirty_path);
			unlink_if_exists($d_inetdconfdirty_path);
		}
	}
}
?>
	<?php include("fbegin.inc");?>
<script language="JavaScript">
<!--
function enable_change(enable_change) {
	var endis = !(document.iform.enable.checked || enable_change);
	document.iform.tcpwrapping.disabled = endis;
	document.iform.logging.disabled = endis;
	document.iform.maxchild.disabled = endis;
	document.iform.maxconnections.disabled = endis;
	document.iform.rate.disabled = endis;
	document.iform.maxchildperip.disabled = endis;
}
//-->
</script>
<table width="100%" border="0" cellpadding="0" cellspacing="0">
<tr>
	<td class="tabnavtbl">
		<ul id="tabnav">
			<li class="tabact"><a href="services_inetd.php" title="<?=gettext("Reload page");?>"><?=gettext("Settings");?></a></li>
			<li class="tabinact"><a href="services_inetd_services.php"><?=gettext("Services");?></a></li>
		</ul>
	</td>
</tr>
<tr>
	<td class="tabcont">
		<form action="services_inetd.php" method="post" name="iform" id="iform">
		<?php if ($input_errors) print_input_errors($input_errors);?>
		<?php if ($savemsg) print_info_box($savemsg);?>
		<?php if (file_exists($d_inetdserviceconfdirty_path) || file_exists($d_inetdconfdirty_path)) print_config_change_box();?>
		<table width="100%" border="0" cellpadding="6" cellspacing="0">
		<tr>
			<td colspan="2" valign="top" class="optsect_t">
				<table border="0" cellspacing="0" cellpadding="0" width="100%">
				<tr>
					<td class="optsect_s"><strong><?=gettext("Inetd Server");?></strong></td>
					<td align="right" class="optsect_s"><input name="enable" type="checkbox" value="yes" <?php if ($pconfig['enable']) echo "checked"; ?> onClick="enable_change(false)"> <strong><?=gettext("Enable"); ?></strong></td>
				</tr>
				</table>
			</td>
		</tr>
		<?=html_checkbox("tcpwrapping",gettext("TCP wrapper"), $pconfig['tcpwrapping'] ? true : false, "", gettext("Turn on TCP Wrapping."), true);?>
		<?=html_checkbox("logging",gettext("Logging"), $pconfig['logging'] ? true : false, "", gettext("Turn on logging for successful connections."), false);?>
		<?=html_inputbox("maxchild",gettext("max-child"),htmlspecialchars($pconfig['maxchild']),gettext("Specify the default maximum number of simultaneous invocations of each service; the default is unlimited. May be overridden on a per-service basis with the max-child parameter."));?>
		<?=html_inputbox("maxconnections",gettext("max-connections-per-ip-per-minute"),htmlspecialchars($pconfig['maxconnections']),gettext("Specify the default maximum number of times a service can be invoked from a single IP address in one minute; the default is unlimited. May be overridden on a per-service basis with the max-connections-per-ip-per-minute parameter."));?>
		<?=html_inputbox("rate",gettext("Rate"),htmlspecialchars($pconfig['rate']),gettext("Specify the maximum number of times a service can be invoked in one minute; the default is 256. A rate of 0 allows an unlimited number of invocations."));?>
		<?=html_inputbox("maxchildperip",gettext("max-child-per-ip"),htmlspecialchars($pconfig['maxchildperip']),gettext("Specify the maximum number of times a service can be invoked from a single IP address at any one time; the default is unlimited. May be overridden on a per-service basis with the max-child-per-ip parameter."));?>
		<tr>
			<td width="22%" valign="top">&nbsp;</td>
			<td width="78%">
			<input name="Submit" type="submit" class="formbtn" value="<?=gettext("Save and Restart");?>" onClick="enable_change(true)">
			</td>
		</tr>
		</table>
		</form>
	</td>
</tr>
</table>
<script language="JavaScript">
<!--
enable_change(false);
//-->
</script>
<?php include("fend.inc");?>
