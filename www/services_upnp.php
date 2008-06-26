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

$pgtitle = array(gettext("Services"),gettext("UPnP"));

if(!is_array($config['upnp']))
	$config['upnp'] = array();

if(!is_array($config['upnp']['content']))
	$config['upnp']['content'] = array();

sort($config['upnp']['content']);

$pconfig['enable'] = isset($config['upnp']['enable']);
$pconfig['name'] = $config['upnp']['name'];
$pconfig['if'] = $config['upnp']['if'];
$pconfig['port'] = $config['upnp']['port'];
$pconfig['web'] = isset($config['upnp']['web']);
$pconfig['home'] = $config['upnp']['home'];
$pconfig['profile'] = $config['upnp']['profile'];
$pconfig['deviceip'] = $config['upnp']['deviceip'];

/* Set name to configured hostname if it is not set */
if(!$pconfig['name'])
	$pconfig['name'] = $config['system']['hostname'];

if($_POST) {
	unset($input_errors);
	$pconfig = $_POST;

	// Input validation.
	if ($_POST['enable']) {
		$reqdfields = explode(" ", "name if port home");
		$reqdfieldsn = array(gettext("Name"), gettext("Interface"), gettext("Port"), gettext("Database directory"));
		$reqdfieldst = explode(" ", "string string port string");

		if ("Terratec_Noxon_iRadio" === $_POST['profile']) {
			$reqdfields = array_merge($reqdfields, array("deviceip"));
			$reqdfieldsn = array_merge($reqdfieldsn, array(gettext("Device IP")));
			$reqdfieldst = array_merge($reqdfieldst, array("ipaddr"));
		}

		do_input_validation($_POST, $reqdfields, $reqdfieldsn, &$input_errors);
		do_input_validation_type($_POST, $reqdfields, $reqdfieldsn, $reqdfieldst, &$input_errors);
	}

	if ($_POST['port'] && ((1024 > $_POST['port']) || (65535 < $_POST['port']))) {
		$input_errors[] = sprintf(gettext("Invalid port! Port number must be in the range from %d to %d."), 1025, 65535);
	}

	if(!$input_errors) {
		$config['upnp']['enable'] = $_POST['enable'] ? true : false;
		$config['upnp']['name'] = $_POST['name'];
		$config['upnp']['if'] = $_POST['if'];
		$config['upnp']['port'] = $_POST['port'];
		$config['upnp']['web'] = $_POST['web'] ? true : false;
		$config['upnp']['home'] = $_POST['home'];
		$config['upnp']['profile'] = $_POST['profile'];
		$config['upnp']['deviceip'] = $_POST['deviceip'];

		write_config();

		$retval = 0;
		if(!file_exists($d_sysrebootreqd_path)) {
			config_lock();
			$retval |= rc_update_service("fuppes");
			$retval |= rc_update_service("mdnsresponder");
			config_unlock();
		}

		$savemsg = get_std_save_message($retval);

		if($retval == 0) {
			if(file_exists($d_upnpconfdirty_path))
				unlink($d_upnpconfdirty_path);
		}
	}
}

if($_GET['act'] == "del") {
	/* Remove entry from content list */
	$config['upnp']['content'] = array_diff($config['upnp']['content'],array($config['upnp']['content'][$_GET['id']]));
	write_config();
	touch($d_upnpconfdirty_path);
	header("Location: services_upnp.php");
	exit;
}

$a_interface = get_interface_list();

// Use first interface as default if it is not set.
if (empty($pconfig['if']) && is_array($a_interface))
	$pconfig['if'] = key($a_interface);
?>
<?php include("fbegin.inc");?>
<script language="JavaScript">
<!--
function enable_change(enable_change) {
	var endis = !(document.iform.enable.checked || enable_change);
	document.iform.name.disabled = endis;
	document.iform.if.disabled = endis;
	document.iform.port.disabled = endis;
	document.iform.web.disabled = endis;
	document.iform.home.disabled = endis;
	document.iform.profile.disabled = endis;
}

function profile_change() {
	switch(document.iform.profile.value) {
		case "Terratec_Noxon_iRadio":
			showElementById('deviceip_tr','show');
			break;

		default:
			showElementById('deviceip_tr','hide');
			break;
	}
}

function web_change() {
	switch(document.iform.web.checked) {
		case false:
			showElementById('url_tr','hide');
			break;

		case true:
			showElementById('url_tr','show');
			break;
	}
}
//-->
</script>
<form action="services_upnp.php" method="post" name="iform" id="iform">
	<table width="100%" border="0" cellpadding="0" cellspacing="0">
	  <tr>
	    <td class="tabcont">
				<?php if ($input_errors) print_input_errors($input_errors); ?>
				<?php if ($savemsg) print_info_box($savemsg); ?>
				<?php if (file_exists($d_upnpconfdirty_path)) print_config_change_box();?>
			  <table width="100%" border="0" cellpadding="6" cellspacing="0">
			  	<?php html_titleline_checkbox("enable", gettext("UPnP A/V Media Server"), $pconfig['enable'] ? true : false, gettext("Enable"), "enable_change(false)");?>
					<?php html_inputbox("name", gettext("Name"), $pconfig['name'], gettext("UPnP friendly name."), true, 20);?>
					<!--
					<?php html_interfacecombobox("if", gettext("Interface"), $pconfig['if'], gettext("Interface to listen to."), true);?>
					-->
			    <tr>
			      <td width="22%" valign="top" class="vncellreq"><?=gettext("Interface");?></td>
			      <td width="78%" class="vtable">
			        <select name="if" class="formfld" id="if">
			          <?php foreach($a_interface as $if => $ifinfo):?>
								<?php $ifinfo = get_interface_info($if); if (("up" == $ifinfo['status']) || ("associated" == $ifinfo['status'])):?>
								<option value="<?=$if;?>"<?php if ($if == $pconfig['if']) echo "selected";?>><?=$if?></option>
								<?php endif;?>
			          <?php endforeach;?>
			        </select>
			        <br><?=gettext("Interface to listen to.");?>
			      </td>
			    </tr>
					<?php html_inputbox("port", gettext("Port"), $pconfig['port'], sprintf(gettext("Port to listen on. Only dynamic or private ports can be used (from %d through %d). Default port is %d."), 1025, 65535, 49152), true, 5);?>
					<?php html_filechooser("home", gettext("Database directory"), $pconfig['home'], gettext("Location where the content database file will be stored."), "/mnt", true, 60);?>
			    <tr>
			      <td width="22%" valign="top" class="vncellreq"><?=gettext("Content");?></td>
			      <td width="78%" class="vtable">
			        <table width="100%" border="0" cellpadding="0" cellspacing="0">
			          <tr>
			            <td width="90%" class="listhdrr"><?=gettext("Directory");?></td>
			            <td width="10%" class="list"></td>
			          </tr>
								<?php if (is_array($config['upnp']['content'])):?>
								<?php $i = 0; foreach($config['upnp']['content'] as $contentv): ?>
								<tr>
									<td class="listlr"><?=htmlspecialchars($contentv);?> &nbsp;</td>
									<td valign="middle" nowrap class="list">
										<?php if(isset($config['upnp']['enable'])):?>
										<a href="services_upnp_edit.php?id=<?=$i;?>"><img src="e.gif" title="<?=gettext("Edit directory");?>" width="17" height="17" border="0"></a>&nbsp;
										<a href="services_upnp.php?act=del&id=<?=$i;?>" onclick="return confirm('<?=gettext("Do you really want to delete this directory entry?");?>')"><img src="x.gif" title="<?=gettext("Delete directory"); ?>" width="17" height="17" border="0"></a>
										<?php endif; ?>
									</td>
								</tr>
								<?php $i++; endforeach;?>
								<?php endif;?>
								<tr>
									<td class="list" colspan="1"></td>
									<td class="list">
										<a href="services_upnp_edit.php"><img src="plus.gif" title="<?=gettext("Add directory");?>" width="17" height="17" border="0"></a>
									</td>
								</tr>
							</table>
							<?=gettext("Location of the files to share.");?>
						</td>
					</tr>
					<?php html_combobox("profile", gettext("Profile"), $pconfig['profile'], array("default" => gettext("Default"), "DLNA" => "DLNA", "PS3" => "Sony Playstation 3", "Telegent_TG100" => "Telegent TG100", "ZyXEL_DMA1000" => "ZyXEL DMA-1000", "Helios_X3000" => "Helios X3000", "DLink_DSM320" => "D-Link DSM320", "Microsoft_XBox360" => "Microsoft XBox 360", "Terratec_Noxon_iRadio" => "Terratec Noxon iRadio", "Yamaha_RXN600" => "Yamaha RX-N600"), gettext("Compliant profile to be used."), true, false, "profile_change()");?>
					<?php html_inputbox("deviceip", gettext("Device IP"), $pconfig['deviceip'], gettext("The device's IP address."), true, 20);?>
					<?php html_separator();?>
					<?php html_titleline(gettext("Administrative WebGUI"));?>
					<?php html_checkbox("web", gettext("Enable"), $pconfig['web'] ? true : false, gettext("Enable web user interface."), "", false, "web_change()");?>
					<?php
					$if = get_ifname($pconfig['if']);
					$ipaddr = get_ipaddr($if);
					$url = "http://{$ipaddr}:{$pconfig['port']}";
					$text = "<a href='{$url}' target='_blank'>{$url}</a>";
					?>
					<?php html_text("url", gettext("URL"), $text);?>
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
profile_change();
web_change();
enable_change(false);
//-->
</script>
<?php include("fend.inc");?>
