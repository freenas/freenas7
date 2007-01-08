#!/usr/local/bin/php
<?php
/*
	system.php
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

$pgtitle = array(_SYSTEMPHP_MODULE_NAME, _SYSTEMPHP_MODULE_NAME_DESCRIPTION);

$pconfig['hostname'] = $config['system']['hostname'];
$pconfig['domain'] = $config['system']['domain'];
list($pconfig['dns1'],$pconfig['dns2']) = $config['system']['dnsserver'];
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

/* Get the language list */
$language_list = system_language_getall();

if ($_POST) {

	unset($input_errors);
	$pconfig = $_POST;

	/* input validation */
	$reqdfields = split(" ", "hostname domain username");
	$reqdfieldsn = array(_SYSTEMPHP_HOSTNAME,_SYSTEMPHP_DOMAIN,_SYSTEMPHP_USERNAME);

	do_input_validation($_POST, $reqdfields, $reqdfieldsn, &$input_errors);

	if ($_POST['hostname'] && !is_hostname($_POST['hostname'])) {
		$input_errors[] = _SYSTEMPHP_MSGHOSTNAME;
	}
	if ($_POST['domain'] && !is_domain($_POST['domain'])) {
		$input_errors[] = _SYSTEMPHP_MSGDOMAIN;
	}
	if (($_POST['dns1'] && !is_ipaddr($_POST['dns1'])) || ($_POST['dns2'] && !is_ipaddr($_POST['dns2']))) {
		$input_errors[] = _SYSTEMPHP_MSGDNS;
	}
	if ($_POST['username'] && !preg_match("/^[a-zA-Z0-9]*$/", $_POST['username'])) {
		$input_errors[] = _SYSTEMPHP_MSGUSERNAME;
	}
	if ($_POST['webguiport'] && (!is_numericint($_POST['webguiport']) ||
			($_POST['webguiport'] < 1) || ($_POST['webguiport'] > 65535))) {
		$input_errors[] = _SYSTEMPHP_MSGWEBGUIPORT;
	}
	if (($_POST['password']) && ($_POST['password'] != $_POST['password2'])) {
		$input_errors[] = _SYSTEMPHP_MSGPASSWORD;
	}
	if ($_POST['language'] && !preg_match("/^[a-zA-Z]*$/", $_POST['language'])) {
		$input_errors[] = _SYSTEMPHP_MSGLANGUAGE;
	}

	$t = (int)$_POST['timeupdateinterval'];
	if (($t < 0) || (($t > 0) && ($t < 6)) || ($t > 1440)) {
		$input_errors[] = _SYSTEMPHP_MSGTIMEUPDATEINTERVAL;
	}

	foreach (explode(' ', $_POST['timeservers']) as $ts) {
		if (!is_domain($ts)) {
			$input_errors[] = _SYSTEMPHP_MSGTIMESERVERS;
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
		if ($_POST['dns1'])
			$config['system']['dnsserver'][] = $_POST['dns1'];
		if ($_POST['dns2'])
			$config['system']['dnsserver'][] = $_POST['dns2'];

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
			$retval |= system_password_configure();
			$retval |= system_timezone_configure();
 			$retval |= system_ntp_configure();
 			$retval |= system_tuning();

			config_unlock();
		}

		$savemsg = get_std_save_message($retval);
	}
}
?>
<?php include("fbegin.inc"); ?>
<?php if ($input_errors) print_input_errors($input_errors); ?>
<?php if ($savemsg) print_info_box($savemsg); ?>
<form action="system.php" method="post" name="iform" id="iform">
  <table width="100%" border="0" cellpadding="6" cellspacing="0">
    <tr>
      <td width="22%" valign="top" class="vncellreq"><?=_SYSTEMPHP_HOSTNAME;?></td>
      <td width="78%" class="vtable"><?=$mandfldhtml;?>
        <?=$mandfldhtml;?><input name="hostname" type="text" class="formfld" id="hostname" size="40" value="<?=htmlspecialchars($pconfig['hostname']);?>"><br>
        <span class="vexpl"><?=_SYSTEMPHP_HOSTNAMETEXT;?></span>
      </td>
    </tr>
    <tr>
      <td width="22%" valign="top" class="vncellreq"><?=_SYSTEMPHP_DOMAIN;?></td>
      <td width="78%" class="vtable"><?=$mandfldhtml;?>
        <?=$mandfldhtml;?><input name="domain" type="text" class="formfld" id="domain" size="40" value="<?=htmlspecialchars($pconfig['domain']);?>"><br>
        <span class="vexpl"><?=_SYSTEMPHP_DOMAINTEXT;?></span>
      </td>
    </tr>
    <tr>
      <td width="22%" valign="top" class="vncell"><?=_SYSTEMPHP_DNS;?></td>
      <td width="78%" class="vtable">
          <input name="dns1" type="text" class="formfld" id="dns1" size="20" value="<?=htmlspecialchars($pconfig['dns1']);?>"><br>
          <input name="dns2" type="text" class="formfld" id="dns22" size="20" value="<?=htmlspecialchars($pconfig['dns2']);?>"><br>
          <span class="vexpl"><?=_SYSTEMPHP_DNSTEXT;?><br>
      </td>
    </tr>
    <tr>
      <td valign="top" class="vncell"><?=_SYSTEMPHP_USERNAME;?></td>
      <td class="vtable">
        <input name="username" type="text" class="formfld" id="username" size="20" value="<?=$pconfig['username'];?>"><br>
        <span class="vexpl"><?=_SYSTEMPHP_USERNAMETEXT;?></span>
      </td>
    </tr>
    <tr>
      <td width="22%" valign="top" class="vncell"><?=_SYSTEMPHP_PASSWORD;?></td>
      <td width="78%" class="vtable">
        <input name="password" type="password" class="formfld" id="password" size="20"><br>
        <input name="password2" type="password" class="formfld" id="password2" size="20">&nbsp;(<?=_CONFIRMATION;?>)<br>
        <span class="vexpl"><?=_SYSTEMPHP_PASSWORDTEXT;?></span>
      </td>
    </tr>
    <tr>
      <td width="22%" valign="top" class="vncell"><?=_SYSTEMPHP_WEBGUIPROTOCOL;?></td>
      <td width="78%" class="vtable">
        <input name="webguiproto" type="radio" value="http" <?php if ($pconfig['webguiproto'] == "http") echo "checked"; ?>>HTTP &nbsp;&nbsp;&nbsp;
        <input type="radio" name="webguiproto" value="https" <?php if ($pconfig['webguiproto'] == "https") echo "checked"; ?>>HTTPS
      </td>
    </tr>
    <tr>
      <td valign="top" class="vncell"><?=_SYSTEMPHP_WEBGUIPORT;?></td>
      <td width="78%" class="vtable">
        <input name="webguiport" type="text" class="formfld" id="webguiport" size="20" value="<?=htmlspecialchars($pconfig['webguiport']);?>"><br>
        <span class="vexpl"><?=_SYSTEMPHP_WEBGUIPORTTEXT;?></span>
      </td>
    </tr>
    <tr>
      <td width="22%" valign="top" class="vncell"><?=_SYSTEMPHP_LANGUAGE;?></td>
      <td width="78%" class="vtable">
        <select name="language" id="language">
    		<?php foreach ($language_list as $value): ?>
    			<option value="<?=htmlspecialchars($value);?>"
    			<?php if ($value == system_language_getlang()) echo "selected"; ?>>
    			<?=htmlspecialchars($value);?>
          </option>
    		<?php endforeach; ?>
    		</select>
      </td>
    </tr>
    <tr>
      <td width="22%" valign="top" class="vncell"><?=_SYSTEMPHP_TIMEZONE;?></td>
      <td width="78%" class="vtable">
        <select name="timezone" id="timezone">
          <?php foreach ($timezonelist as $value): ?>
            <option value="<?=htmlspecialchars($value);?>" <?php if ($value == $pconfig['timezone']) echo "selected"; ?>>
            <?=htmlspecialchars($value);?>
            </option>
          <?php endforeach; ?>
        </select><br>
        <span class="vexpl"><?=_SYSTEMPHP_TIMEZONETEXT;?></span>
      </td>
    </tr>
    <tr>
      <td width="22%" valign="top" class="vncell"><?=_SYSTEMPHP_TIMEUPDATEINT;?></td>
      <td width="78%" class="vtable">
        <input name="timeupdateinterval" type="text" class="formfld" id="timeupdateinterval" size="20" value="<?=htmlspecialchars($pconfig['timeupdateinterval']);?>"><br>
        <span class="vexpl"><?=_SYSTEMPHP_TIMEUPDATEINTTEXT;?></span>
      </td>
    </tr>
    <tr>
      <td width="22%" valign="top" class="vncell"><?=_SYSTEMPHP_NTPSERVER;?></td>
      <td width="78%" class="vtable">
        <input name="timeservers" type="text" class="formfld" id="timeservers" size="40" value="<?=htmlspecialchars($pconfig['timeservers']);?>"><br>
        <span class="vexpl"><?=_SYSTEMPHP_NTPSERVERTEXT;?></span>
      </td>
    </tr>
    <tr>
      <td width="22%" valign="top">&nbsp;</td>
      <td width="78%">
        <input name="Submit" type="submit" class="formbtn" value="<?=_SAVE;?>">
      </td>
    </tr>
  </table>
</form>
<?php include("fend.inc"); ?>
