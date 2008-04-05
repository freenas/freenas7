#!/usr/local/bin/php
<?php
/*
	system.php
	part of FreeNAS (http://www.freenas.org)
	Copyright (C) 2005-2008 Olivier Cochard-Labbe <olivier@freenas.org>.
	All rights reserved.
	Set time function added by Paul Wheels (pwheels@users.sourceforge.net)

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

$pgtitle = array(gettext("System"), gettext("General Setup"));

$pconfig['hostname'] = $config['system']['hostname'];
$pconfig['domain'] = $config['system']['domain'];
list($pconfig['dns1'],$pconfig['dns2']) = get_ipv4dnsserver();
list($pconfig['ipv6dns1'],$pconfig['ipv6dns2']) = get_ipv6dnsserver();
$pconfig['username'] = $config['system']['username'];
$pconfig['webguiproto'] = $config['system']['webgui']['protocol'];
$pconfig['webguiport'] = $config['system']['webgui']['port'];
$pconfig['language'] = $config['system']['language'];
$pconfig['timezone'] = $config['system']['timezone'];
$pconfig['ntp_enable'] = isset($config['system']['ntp']['enable']);
$pconfig['ntp_timeservers'] = $config['system']['ntp']['timeservers'];
$pconfig['ntp_updateinterval'] = $config['system']['ntp']['updateinterval'];
$pconfig['language'] = $config['system']['language'];
$pconfig['certificate'] = base64_decode($config['system']['webgui']['certificate']);
$pconfig['privatekey'] = base64_decode($config['system']['webgui']['privatekey']);

// Set default values if necessary.
if (!$pconfig['language'])
	$pconfig['language'] = "English";
if (!$pconfig['timezone'])
	$pconfig['timezone'] = "Etc/UTC";
if (!$pconfig['webguiproto'])
	$pconfig['webguiproto'] = "http";
if (!$pconfig['username'])
	$pconfig['username'] = "admin";
if (!$pconfig['ntp_timeservers'])
	$pconfig['ntp_timeservers'] = "pool.ntp.org";
if (!isset($pconfig['ntp_updateinterval']))
	$pconfig['ntp_updateinterval'] = 300;

function is_timezone($elt) {
	return !preg_match("/\/$/", $elt);
}

exec('/usr/bin/tar -tf /usr/share/zoneinfo.tgz -W strip-components=1', $timezonelist);
$timezonelist = array_filter($timezonelist, 'is_timezone');
sort($timezonelist);

if ($_POST) {
	unset($input_errors);
	$pconfig = $_POST;

	// Input validation.
	$reqdfields = explode(" ", "hostname domain username");
	$reqdfieldsn = array(gettext("Hostname"),gettext("Domain"),gettext("Username"));
	$reqdfieldst = explode(" ", "hostname domain string");

	if (isset($_POST['ntp_enable'])) {
		$reqdfields = array_merge($reqdfields, explode(" ", "ntp_timeservers ntp_updateinterval"));
		$reqdfieldsn = array_merge($reqdfieldsn, array(gettext("NTP time server"), gettext("Time update interval")));
		$reqdfieldst = array_merge($reqdfieldst, explode(" ", "string numeric"));
	}

	if ("https" === $_POST['webguiproto']) {
		$reqdfields = array_merge($reqdfields, explode(" ", "certificate privatekey"));
		$reqdfieldsn = array_merge($reqdfieldsn, array(gettext("Certificate"), gettext("Private key")));
		$reqdfieldst = array_merge($reqdfieldst, explode(" ", "certificate privatekey"));
	}

	if ($_POST['webguiport']) {
		$reqdfields = array_merge($reqdfields, array("webguiport"));
		$reqdfieldsn = array_merge($reqdfieldsn, array(gettext("Port")));
		$reqdfieldst = array_merge($reqdfieldst, array("port"));
	}

	do_input_validation($_POST, $reqdfields, $reqdfieldsn, &$input_errors);
	do_input_validation_type($_POST, $reqdfields, $reqdfieldsn, $reqdfieldst, &$input_errors);

	if (($_POST['dns1'] && !is_ipv4addr($_POST['dns1'])) || ($_POST['dns2'] && !is_ipv4addr($_POST['dns2']))) {
		$input_errors[] = gettext("A valid IPv4 address must be specified for the primary/secondary DNS server.");
	}
	if (($_POST['ipv6dns1'] && !is_ipv6addr($_POST['ipv6dns1'])) || ($_POST['ipv6dns2'] && !is_ipv6addr($_POST['ipv6dns2']))) {
		$input_errors[] = gettext("A valid IPv6 address must be specified for the primary/secondary DNS server.");
	}
	if ($_POST['username'] && !preg_match("/^[a-zA-Z0-9]*$/", $_POST['username'])) {
		$input_errors[] = gettext("The username may only contain the characters a-z, A-Z and 0-9.");
	}

	if (isset($_POST['ntp_enable'])) {
		$t = (int)$_POST['ntp_updateinterval'];
		if (($t < 0) || (($t > 0) && ($t < 6)) || ($t > 1440)) {
			$input_errors[] = gettext("The time update interval must be either between 6 and 1440.");
		}

		foreach (explode(' ', $_POST['ntp_timeservers']) as $ts) {
			if (!is_domain($ts)) {
				$input_errors[] = gettext("A NTP time server name may only contain the characters a-z, 0-9, '-' and '.'.");
			}
		}
	}

	if (!$input_errors) {
		$oldcert = $config['system']['webgui']['certificate'];
		$oldkey = $config['system']['webgui']['privatekey'];
		$oldwebguiproto = $config['system']['webgui']['protocol'];
		$oldwebguiport = $config['system']['webgui']['port'];

		$config['system']['hostname'] = strtolower($_POST['hostname']);
		$config['system']['domain'] = strtolower($_POST['domain']);
		$config['system']['username'] = $_POST['username'];
		$config['system']['webgui']['protocol'] = $pconfig['webguiproto'];
		$config['system']['webgui']['port'] = $pconfig['webguiport'];
		$config['system']['language'] = $_POST['language'];
		$config['system']['timezone'] = $_POST['timezone'];
		$config['system']['ntp']['enable'] = $_POST['ntp_enable'] ? true : false;
		$config['system']['ntp']['timeservers'] = strtolower($_POST['ntp_timeservers']);
		$config['system']['ntp']['updateinterval'] = $_POST['ntp_updateinterval'];
		$config['system']['webgui']['certificate'] = base64_encode($_POST['certificate']);
		$config['system']['webgui']['privatekey'] =  base64_encode($_POST['privatekey']);

		unset($config['system']['dnsserver']);
		// Only store IPv4 DNS servers when using static IPv4.
		if ("dhcp" !== $config['interfaces']['lan']['ipaddr']) {
			if ($_POST['dns1'])
				$config['system']['dnsserver'][] = $_POST['dns1'];
			if ($_POST['dns2'])
				$config['system']['dnsserver'][] = $_POST['dns2'];
		}
		// Only store IPv6 DNS servers when using static IPv6.
		if ("auto" !== $config['interfaces']['lan']['ipv6addr']) {
			if ($_POST['ipv6dns1'])
				$config['system']['ipv6dnsserver'][] = $_POST['ipv6dns1'];
			if ($_POST['ipv6dns2'])
				$config['system']['ipv6dnsserver'][] = $_POST['ipv6dns2'];
		}

		$olddnsallowoverride = $config['system']['dnsallowoverride'];
		$config['system']['dnsallowoverride'] = $_POST['dnsallowoverride'] ? true : false;

		write_config();

		// Check if a reboot is required.
		if (($oldwebguiproto != $config['system']['webgui']['protocol']) ||
			($oldwebguiport != $config['system']['webgui']['port'])) {
			touch($d_sysrebootreqd_path);
		}
		if (($config['system']['webgui']['certificate'] != $oldcert) || ($config['system']['webgui']['privatekey'] != $oldkey)) {
			touch($d_sysrebootreqd_path);
		}

		$retval = 0;

		if (!file_exists($d_sysrebootreqd_path)) {
			config_lock();
			$retval |= rc_exec_service("rcconf.sh"); // Update /etc/rc.conf
			$retval |= rc_exec_service("resolv"); // Update /etc/resolv
			$retval |= rc_exec_service("hosts"); // Update /etc/hosts
			$retval |= rc_restart_service("hostname"); // Set hostname
			$retval |= rc_exec_service("userdb");
			$retval |= rc_exec_service("htpasswd");
			$retval |= rc_exec_service("websrv_htpasswd");
			$retval |= rc_exec_service("timezone");
 			$retval |= rc_update_service("msntp");
 			$retval |= rc_update_service("mdnsresponder");
 			$retval |= rc_update_service("bsnmpd");
			config_unlock();
		}

		if (($pconfig['systime'] !== "Not Set") && (!empty($pconfig['systime']))) {
			$timestamp = strtotime($pconfig['systime']);
			if (FALSE !== $timestamp) {
				$timestamp = strftime("%g%m%d%H%M", $timestamp);
				// The date utility exits 0 on success, 1 if unable to set the date,
				// and 2 if able to set the local date, but unable to set it globally.
				$retval |= mwexec("/bin/date -n {$timestamp}");
				$pconfig['systime'] = "Not Set";
			}
		}

		$savemsg = get_std_save_message($retval);

		// Update DNS server controls.
		list($pconfig['dns1'],$pconfig['dns2']) = get_ipv4dnsserver();
		list($pconfig['ipv6dns1'],$pconfig['ipv6dns2']) = get_ipv6dnsserver();
	}
}
?>
<?php include("fbegin.inc");?>
<script language="JavaScript">
<!--
function ntp_change(enable_change) {
	var endis = !(document.iform.ntp_enable.checked || enable_change);
	document.iform.ntp_timeservers.disabled = endis;
	document.iform.ntp_updateinterval.disabled = endis;
}

function webguiproto_change() {
	switch(document.iform.webguiproto.selectedIndex) {
		case 0:
			showElementById('privatekey_tr','hide');
			showElementById('certificate_tr','hide');
			break;

		default:
			showElementById('privatekey_tr','show');
			showElementById('certificate_tr','show');
			break;
	}
}
//-->
</script>
<script language="JavaScript" src="datechooser.js"></script>
<link rel="stylesheet" type="text/css" href="datechooser.css">
<table width="100%" border="0" cellpadding="0" cellspacing="0">
	<tr>
    <td class="tabnavtbl">
      <ul id="tabnav">
      	<li class="tabact"><a href="system.php" title="<?=gettext("Reload page");?>"><?=gettext("General");?></a></li>
      	<li class="tabinact"><a href="system_password.php"><?=gettext("Password");?></a></li>
      </ul>
    </td>
  </tr>
  <tr>
    <td class="tabcont">
			<form action="system.php" method="post" name="iform" id="iform">
				<?php if ($input_errors) print_input_errors($input_errors);?>
				<?php if ($savemsg) print_info_box($savemsg);?>
			  <table width="100%" border="0" cellpadding="6" cellspacing="0">
			  	<tr>
						<td colspan="2" valign="top" class="listtopic"><?=gettext("Hostname");?></td>
					</tr>
			    <tr>
			      <td width="22%" valign="top" class="vncellreq"><?=gettext("Hostname");?></td>
			      <td width="78%" class="vtable">
			        <input name="hostname" type="text" class="formfld" id="hostname" size="40" value="<?=htmlspecialchars($pconfig['hostname']);?>"><br>
			        <span class="vexpl"><?=sprintf(gettext("Name of the NAS host, without domain part e.g. %s."), "<em>" . strtolower(get_product_name()) ."</em>");?></span>
			      </td>
			    </tr>
			    <tr>
			      <td width="22%" valign="top" class="vncellreq"><?=gettext("Domain");?></td>
			      <td width="78%" class="vtable">
			        <input name="domain" type="text" class="formfld" id="domain" size="40" value="<?=htmlspecialchars($pconfig['domain']);?>"><br>
			        <span class="vexpl"><?=sprintf(gettext("e.g. %s"), "<em>com, local</em>");?></span>
			      </td>
			    </tr>
			    <tr>
						<td colspan="2" class="list" height="12"></td>
					</tr>
					<tr>
						<td colspan="2" valign="top" class="listtopic"><?=gettext("DNS settings");?></td>
					</tr>
			    <tr>
			      <td width="22%" valign="top" class="vncell"><?=gettext("IPv4 DNS servers");?></td>
			      <td width="78%" class="vtable">
							<?php $dns_ctrl_disabled = ("dhcp" == $config['interfaces']['lan']['ipaddr']) ? "disabled" : "";?>
							<input name="dns1" type="text" class="formfld" id="dns1" size="20" value="<?=htmlspecialchars($pconfig['dns1']);?>" <?=$dns_ctrl_disabled;?>><br>
							<input name="dns2" type="text" class="formfld" id="dns2" size="20" value="<?=htmlspecialchars($pconfig['dns2']);?>" <?=$dns_ctrl_disabled;?>><br>
							<span class="vexpl"><?=gettext("IPv4 addresses");?><br>
			      </td>
			    </tr>
				  <tr>
			      <td width="22%" valign="top" class="vncell"><?=gettext("IPv6 DNS servers");?></td>
			      <td width="78%" class="vtable">
							<?php $dns_ctrl_disabled = ("auto" == $config['interfaces']['lan']['ipv6addr']) ? "disabled" : "";?>
							<input name="ipv6dns1" type="text" class="formfld" id="ipv6dns1" size="20" value="<?=htmlspecialchars($pconfig['ipv6dns1']);?>" <?=$dns_ctrl_disabled;?>><br>
							<input name="ipv6dns2" type="text" class="formfld" id="ipv6dns2" size="20" value="<?=htmlspecialchars($pconfig['ipv6dns2']);?>" <?=$dns_ctrl_disabled;?>><br>
							<span class="vexpl"><?=gettext("IPv6 addresses");?><br>
			      </td>
			    </tr>
			    <tr>
						<td colspan="2" class="list" height="12"></td>
					</tr>
					<tr>
						<td colspan="2" valign="top" class="listtopic"><?=gettext("WebGUI");?></td>
					</tr>
			    <tr>
			      <td valign="top" class="vncell"><?=gettext("Username");?></td>
			      <td class="vtable">
			        <input name="username" type="text" class="formfld" id="username" size="20" value="<?=$pconfig['username'];?>"><br>
			        <span class="vexpl"><?=gettext("If you want to change the username for accessing the WebGUI, enter it here.");?></span>
			      </td>
			    </tr>
			    <tr>
			      <td width="22%" valign="top" class="vncell"><?=gettext("Protocol");?></td>
			      <td width="78%" class="vtable">
			        <select name="webguiproto" class="formfld" id="webguiproto" onClick="webguiproto_change()">
								<?php $types = array(gettext("HTTP"),gettext("HTTPS")); $vals = explode(" ", "http https");?>
								<?php $j = 0; for ($j = 0; $j < count($vals); $j++):?>
								<option value="<?=$vals[$j];?>" <?php if ($vals[$j] === $pconfig['webguiproto']) echo "selected";?>><?=htmlspecialchars($types[$j]);?></option>
								<?php endfor;?>
							</select>
			      </td>
			    </tr>
			    <tr>
			      <td valign="top" class="vncell"><?=gettext("Port");?></td>
			      <td width="78%" class="vtable">
			        <input name="webguiport" type="text" class="formfld" id="webguiport" size="20" value="<?=htmlspecialchars($pconfig['webguiport']);?>"><br>
			        <span class="vexpl"><?=gettext("Enter a custom port number for the WebGUI above if you want to override the default (80 for HTTP, 443 for HTTPS).");?></span>
			      </td>
			    </tr>
			    <tr id="certificate_tr">
						<td width="22%" valign="top" class="vncellreq"><?=gettext("Certificate");?></td>
						<td width="78%" class="vtable">
							<textarea name="certificate" cols="65" rows="7" id="certificate" class="formpre"><?=htmlspecialchars($pconfig['certificate']);?></textarea></br>
							<span class="vexpl"><?=gettext("Paste a signed certificate in X.509 PEM format here.");?></span>
						</td>
					</tr>
			    <tr id="privatekey_tr">
						<td width="22%" valign="top" class="vncellreq"><?=gettext("Private key");?></td>
						<td width="78%" class="vtable">
							<textarea name="privatekey" cols="65" rows="7" id="privatekey" class="formpre"><?=htmlspecialchars($pconfig['privatekey']);?></textarea></br>
							<span class="vexpl"><?=gettext("Paste an private key in PEM format here.");?></span>
						</td>
					</tr>
			    <tr>
			      <td width="22%" valign="top" class="vncell"><?=gettext("Language");?></td>
			      <td width="78%" class="vtable">
			        <select name="language" id="language">
			    			<?php foreach ($g_languages as $langk => $langv): ?>
			    			<option value="<?=$langk;?>" <?php if ($langk === $pconfig['language']) echo "selected";?>><?=gettext($langv['desc']);?></option>
				    		<?php endforeach; ?>
			    		</select>
			      </td>
			    </tr>
					<tr>
						<td colspan="2" class="list" height="12"></td>
					</tr>
					<tr>
						<td colspan="2" valign="top" class="listtopic"><?=gettext("Time");?></td>
					</tr>
			    <tr>
			      <td width="22%" valign="top" class="vncell"><?=gettext("Time zone");?></td>
			      <td width="78%" class="vtable">
			        <select name="timezone" id="timezone">
			          <?php foreach ($timezonelist as $value):?>
								<option value="<?=htmlspecialchars($value);?>" <?php if ($value === $pconfig['timezone']) echo "selected";?>><?=htmlspecialchars($value);?></option>
								<?php endforeach;?>
			        </select><br>
			        <span class="vexpl"><?=gettext("Select the location closest to you.");?></span>
			      </td>
			    </tr>
			    <tr>
						<td width="22%" valign="top" class="vncell"><?=gettext("System time");?></td>
						<td width="78%" class="vtable">
							<input id="systime" size="20" maxlength="20" name="systime" type="text">
							<img src="cal.gif" onclick="showChooser(this, 'systime', 'chooserSpan', 1950, 2010, Date.patterns.Default, true);">
							<div id="chooserSpan" class="dateChooser select-free" style="display: none; visibility: hidden; width: 160px;"></div><br/>
							<span class="vexpl"><?=gettext("Enter desired system time directly (format mm/dd/yyyy hh:mm) or use icon to select it.");?></span>
						</td>
			    </tr>
					<tr>
						<td width="22%" valign="top" class="vncell"><?=gettext("Enable NTP");?></td>
						<td width="78%" class="vtable">
							<input name="ntp_enable" type="checkbox" id="ntp_enable" value="yes" <?php if ($pconfig['ntp_enable']) echo "checked";?> onClick="ntp_change(false)">
							<span class="vexpl"><?=gettext("Use the specified NTP server.");?></span>
						</td>
					</tr>
					<tr>
						<td width="22%" valign="top" class="vncell"><?=gettext("NTP time server");?></td>
						<td width="78%" class="vtable">
							<input name="ntp_timeservers" type="text" class="formfld" id="ntp_timeservers" size="40" value="<?=htmlspecialchars($pconfig['ntp_timeservers']);?>"><br>
							<span class="vexpl"><?=gettext("Use a space to separate multiple hosts (only one required). Remember to set up at least one DNS server if you enter a host name here!");?></span>
						</td>
					</tr>
					<tr>
						<td width="22%" valign="top" class="vncell"><?=gettext("Time update interval");?></td>
						<td width="78%" class="vtable">
							<input name="ntp_updateinterval" type="text" class="formfld" id="ntp_updateinterval" size="20" value="<?=htmlspecialchars($pconfig['ntp_updateinterval']);?>"><br>
							<span class="vexpl"><?=gettext("Minutes between network time sync.");?></span>
						</td>
					</tr>
			    <tr>
			      <td width="22%" valign="top">&nbsp;</td>
			      <td width="78%">
			        <input name="Submit" type="submit" class="formbtn" value="<?=gettext("Save");?>" onClick="ntp_change(true)">
			      </td>
			    </tr>
			  </table>
			</form>
		</td>
  </tr>
</table>
<script language="JavaScript">
<!--
ntp_change(false);
webguiproto_change();
//-->
</script>
<?php include("fend.inc");?>
