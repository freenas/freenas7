#!/usr/local/bin/php
<?php
/*
	system.php
	part of FreeNAS (http://www.freenas.org)
	Copyright (C) 2005-2007 Olivier Cochard-Labbe <olivier@freenas.org>.
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

$pgtitle = array(gettext("System"), gettext("General setup"));

$pconfig['hostname'] = $config['system']['hostname'];
$pconfig['domain'] = $config['system']['domain'];
list($pconfig['dns1'],$pconfig['dns2']) = get_dnsserver();
$pconfig['username'] = $config['system']['username'];
if (!$pconfig['username'])
	$pconfig['username'] = "admin";
$pconfig['webguiproto'] = $config['system']['webgui']['protocol'];
if (!$pconfig['webguiproto'])
	$pconfig['webguiproto'] = "http";
$pconfig['webguiport'] = $config['system']['webgui']['port'];
$pconfig['language'] = $config['system']['language'];
$pconfig['timezone'] = $config['system']['timezone'];
$pconfig['timeupdateinterval'] = $config['system']['time-update-interval'];
$pconfig['timeservers'] = $config['system']['timeservers'];
$pconfig['language'] = $config['system']['language'];
if (!$pconfig['language'])
	$pconfig['language'] = "English";

if (!isset($pconfig['timeupdateinterval']))
	$pconfig['timeupdateinterval'] = 300;
if (!$pconfig['timezone'])
	$pconfig['timezone'] = "Etc/UTC";
if (!$pconfig['timeservers'])
	$pconfig['timeservers'] = "pool.ntp.org";

function is_timezone($elt) {
	return !preg_match("/\/$/", $elt);
}

exec('/usr/bin/tar -tzf /usr/share/zoneinfo.tgz', $timezonelist);
$timezonelist = array_filter($timezonelist, 'is_timezone');
sort($timezonelist);

if ($_POST) {
	unset($input_errors);
	$pconfig = $_POST;

	/* input validation */
	$reqdfields = split(" ", "hostname domain username");
	$reqdfieldsn = array(gettext("Hostname"),gettext("Domain"),gettext("Username"));

	do_input_validation($_POST, $reqdfields, $reqdfieldsn, &$input_errors);

	if ($_POST['hostname'] && !is_hostname($_POST['hostname'])) {
		$input_errors[] = gettext("The hostname may only contain the characters a-z, 0-9 and '-'.");
	}
	if ($_POST['domain'] && !is_domain($_POST['domain'])) {
		$input_errors[] = gettext("The domain may only contain the characters a-z, 0-9, '-' and '.'.");
	}
	if (($_POST['dns1'] && !is_ipaddr($_POST['dns1'])) || ($_POST['dns2'] && !is_ipaddr($_POST['dns2']))) {
		$input_errors[] = gettext("A valid IP address must be specified for the primary/secondary DNS server.");
	}
	if ($_POST['username'] && !preg_match("/^[a-zA-Z0-9]*$/", $_POST['username'])) {
		$input_errors[] = gettext("The username may only contain the characters a-z, A-Z and 0-9.");
	}
	if ($_POST['webguiport'] && (!is_numericint($_POST['webguiport']) ||
			($_POST['webguiport'] < 1) || ($_POST['webguiport'] > 65535))) {
		$input_errors[] = gettext("A valid TCP/IP port must be specified for the webGUI port.");
	}
	if (($_POST['password']) && ($_POST['password'] != $_POST['password2'])) {
		$input_errors[] = gettext("The passwords do not match.");
	}
	if ($_POST['language'] && !preg_match("/^[a-zA-Z]*$/", $_POST['language'])) {
		$input_errors[] = gettext("You must select a valid language.");
	}

	$t = (int)$_POST['timeupdateinterval'];
	if (($t < 0) || (($t > 0) && ($t < 6)) || ($t > 1440)) {
		$input_errors[] = gettext("The time update interval must be either 0 (disabled) or between 6 and 1440.");
	}

	foreach (explode(' ', $_POST['timeservers']) as $ts) {
		if (!is_domain($ts)) {
			$input_errors[] = gettext("A NTP Time Server name may only contain the characters a-z, 0-9, '-' and '.'.");
		}
	}

	if (!$input_errors) {
		$config['system']['hostname'] = strtolower($_POST['hostname']);
		$config['system']['domain'] = strtolower($_POST['domain']);
		$oldwebguiproto = $config['system']['webgui']['protocol'];
		$config['system']['username'] = $_POST['username'];
		$config['system']['webgui']['protocol'] = $pconfig['webguiproto'];
		$oldwebguiport = $config['system']['webgui']['port'];
		$config['system']['webgui']['port'] = $pconfig['webguiport'];
		$config['system']['language'] = $_POST['language'];
		$config['system']['timezone'] = $_POST['timezone'];
		$config['system']['timeservers'] = strtolower($_POST['timeservers']);
		$config['system']['time-update-interval'] = $_POST['timeupdateinterval'];

		unset($config['system']['dnsserver']);
		// Only store DNS servers when using static IP.
		if ("dhcp" !== $config['interfaces']['lan']['ipaddr']) {
			if ($_POST['dns1'])
				$config['system']['dnsserver'][] = $_POST['dns1'];
			if ($_POST['dns2'])
				$config['system']['dnsserver'][] = $_POST['dns2'];
		}

		$olddnsallowoverride = $config['system']['dnsallowoverride'];
		$config['system']['dnsallowoverride'] = $_POST['dnsallowoverride'] ? true : false;

		if ($_POST['password']) {
			$config['system']['password'] = crypt($_POST['password']);
		}

		write_config();

		if (($oldwebguiproto != $config['system']['webgui']['protocol']) ||
			($oldwebguiport != $config['system']['webgui']['port']))
			touch($d_sysrebootreqd_path);

		$retval = 0;
		if (!file_exists($d_sysrebootreqd_path)) {
			config_lock();
			$retval = system_hostname_configure();
			$retval |= system_hosts_generate();
			$retval |= system_resolvconf_generate();
			$retval |= system_create_htpasswd();
			$retval |= system_users_create();
			$retval |= system_timezone_configure();
 			$retval |= system_ntp_configure();
 			$retval |= system_tuning();
 			$retval |= services_mdnsresponder_configure(); // Update and announce service via zeroconf.
			config_unlock();
		}
		if (($pconfig['systime'] != "Not Set") && ($pconfig['systime'] != "")) {
			$timefields = split(" ", $pconfig['systime']);
			$dateparts = split("/", $timefields[0]);
			$timeparts = split(":", $timefields[1]);
			$newsystime = substr($dateparts[2],-2).substr("0".$dateparts[0],-2).substr("0".$dateparts[1],-2);
			$newsystime = $newsystime.substr("0".$timeparts[0],-2).substr("0".$timeparts[1],-2);
			$retval = system_systime_set($newsystime);
			$pconfig['systime']="Not Set";
		}

		$savemsg = get_std_save_message($retval);
	}
}
?>
<?php include("fbegin.inc"); ?>
<?php if ($input_errors) print_input_errors($input_errors); ?>
<?php if ($savemsg) print_info_box($savemsg); ?>
<script language="JavaScript" src="datetimepicker.js"></script>
<form action="system.php" method="post" name="iform" id="iform">
  <table width="100%" border="0" cellpadding="6" cellspacing="0">
    <tr>
      <td width="22%" valign="top" class="vncellreq"><?=gettext("Hostname");?></td>
      <td width="78%" class="vtable"><?=$mandfldhtml;?>
        <?=$mandfldhtml;?><input name="hostname" type="text" class="formfld" id="hostname" size="40" value="<?=htmlspecialchars($pconfig['hostname']);?>"><br>
        <span class="vexpl"><?=gettext("Name of the NAS host, without domain part<br>e.g. <em>nas</em>");?></span>
      </td>
    </tr>
    <tr>
      <td width="22%" valign="top" class="vncellreq"><?=gettext("Domain");?></td>
      <td width="78%" class="vtable"><?=$mandfldhtml;?>
        <?=$mandfldhtml;?><input name="domain" type="text" class="formfld" id="domain" size="40" value="<?=htmlspecialchars($pconfig['domain']);?>"><br>
        <span class="vexpl"><?=gettext("e.g. <em>mycorp.com</em>");?></span>
      </td>
    </tr>
    <tr>
      <td width="22%" valign="top" class="vncell"><?=gettext("DNS servers");?></td>
      <td width="78%" class="vtable">
				<?php $dns_ctrl_disabled = ("dhcp" == $config['interfaces']['lan']['ipaddr']) ? "disabled" : "";?>
				<input name="dns1" type="text" class="formfld" id="dns1" size="20" value="<?=htmlspecialchars($pconfig['dns1']);?>" <?=$dns_ctrl_disabled;?>><br>
				<input name="dns2" type="text" class="formfld" id="dns22" size="20" value="<?=htmlspecialchars($pconfig['dns2']);?>" <?=$dns_ctrl_disabled;?>><br>
				<span class="vexpl"><?=gettext("IP addresses");?><br>
      </td>
    </tr>
    <tr>
      <td valign="top" class="vncell"><?=gettext("Username");?></td>
      <td class="vtable">
        <input name="username" type="text" class="formfld" id="username" size="20" value="<?=$pconfig['username'];?>"><br>
        <span class="vexpl"><?=gettext("If you want to change the username for accessing the webGUI, enter it here.");?></span>
      </td>
    </tr>
    <tr>
      <td width="22%" valign="top" class="vncell"><?=gettext("Password");?></td>
      <td width="78%" class="vtable">
        <input name="password" type="password" class="formfld" id="password" size="20"><br>
        <input name="password2" type="password" class="formfld" id="password2" size="20">&nbsp;(<?=gettext("Confirmation");?>)<br>
        <span class="vexpl"><?=gettext("If you want to change the password for accessing the webGUI, enter it here twice.<br>Don't use the character ':'.");?></span>
      </td>
    </tr>
    <tr>
      <td width="22%" valign="top" class="vncell"><?=gettext("WebGUI protocol");?></td>
      <td width="78%" class="vtable">
        <input name="webguiproto" type="radio" value="http" <?php if ($pconfig['webguiproto'] == "http") echo "checked"; ?>>HTTP &nbsp;&nbsp;&nbsp;
        <input type="radio" name="webguiproto" value="https" <?php if ($pconfig['webguiproto'] == "https") echo "checked"; ?>>HTTPS
      </td>
    </tr>
    <tr>
      <td valign="top" class="vncell"><?=gettext("WebGUI port");?></td>
      <td width="78%" class="vtable">
        <input name="webguiport" type="text" class="formfld" id="webguiport" size="20" value="<?=htmlspecialchars($pconfig['webguiport']);?>"><br>
        <span class="vexpl"><?=gettext("Enter a custom port number for the webGUI above if you want to override the default (80 for HTTP, 443 for HTTPS).");?></span>
      </td>
    </tr>
    <tr>
      <td width="22%" valign="top" class="vncell"><?=gettext("Language");?></td>
      <td width="78%" class="vtable">
        <select name="language" id="language">
    			<?php foreach ($g_languages as $language => $value): ?>
    			<option value="<?=htmlspecialchars($language);?>" <?php if ($language == $pconfig['language']) echo "selected"; ?>><?=gettext($language);?></option>
	    		<?php endforeach; ?>
    		</select>
      </td>
    </tr>
		<tr>
      <td width="22%" valign="top" class="vncell"><?=gettext("System Time");?></td>
      <td width="78%" class="vtable">
			  <input name="systime" id="systime" type="text" size="20">
			  <a href="javascript:NewCal('systime','mmddyyyy',true,24)"><img src="cal.gif" width="16" height="16" border="0" align="top" alt="<?=gettext("Pick a date");?>"></a><br>
        <span class="vexpl"><?=gettext("Enter desired system time directly (format mm/dd/yyyy hh:mm) or use icon to select one, then use Save button to update system time. (Mind seconds part will be ignored)");?></span>
			</td>
    </tr>
    <tr>
      <td width="22%" valign="top" class="vncell"><?=gettext("Time zone");?></td>
      <td width="78%" class="vtable">
        <select name="timezone" id="timezone">
          <?php foreach ($timezonelist as $value): ?>
            <option value="<?=htmlspecialchars($value);?>" <?php if ($value == $pconfig['timezone']) echo "selected"; ?>>
            <?=htmlspecialchars($value);?>
            </option>
          <?php endforeach; ?>
        </select><br>
        <span class="vexpl"><?=gettext("Select the location closest to you.");?></span>
      </td>
    </tr>
    <tr>
      <td width="22%" valign="top" class="vncell"><?=gettext("Time update interval");?></td>
      <td width="78%" class="vtable">
        <input name="timeupdateinterval" type="text" class="formfld" id="timeupdateinterval" size="20" value="<?=htmlspecialchars($pconfig['timeupdateinterval']);?>"><br>
        <span class="vexpl"><?=gettext("Minutes between network time sync.; 300	recommended, or 0 to disable.");?></span>
      </td>
    </tr>
    <tr>
      <td width="22%" valign="top" class="vncell"><?=gettext("NTP time server");?></td>
      <td width="78%" class="vtable">
        <input name="timeservers" type="text" class="formfld" id="timeservers" size="40" value="<?=htmlspecialchars($pconfig['timeservers']);?>"><br>
        <span class="vexpl"><?=gettext("Use a space to separate multiple hosts (only one required). Remember to set up at least one DNS server if you enter a host name here!");?></span>
      </td>
    </tr>
    <tr>
      <td width="22%" valign="top">&nbsp;</td>
      <td width="78%">
        <input name="Submit" type="submit" class="formbtn" value="<?=gettext("Save");?>">
      </td>
    </tr>
  </table>
</form>
<?php include("fend.inc"); ?>
