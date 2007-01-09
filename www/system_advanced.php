#!/usr/local/bin/php
<?php 
/*
	system_advanced.php
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

$pgtitle = array(_SYSTEMADVANCEDPHP_NAME, _SYSTEMADVANCEDPHP_NAMEDESC);

$pconfig['cert'] = base64_decode($config['system']['webgui']['certificate']);
$pconfig['key'] = base64_decode($config['system']['webgui']['private-key']);
$pconfig['disableconsolemenu'] = isset($config['system']['disableconsolemenu']);
$pconfig['disablefirmwarecheck'] = isset($config['system']['disablefirmwarecheck']);
$pconfig['expanddiags'] = isset($config['system']['webgui']['expanddiags']);
if ($g['platform'] == "generic-pc")
	$pconfig['harddiskstandby'] = $config['system']['harddiskstandby'];
$pconfig['disablebeep'] = isset($config['system']['disablebeep']);
$pconfig['polling_enable'] = isset($config['system']['polling']);
$pconfig['tune_enable'] = isset($config['system']['tune']);
$pconfig['smart_enable'] = isset($config['system']['smart']);
$pconfig['howl_disable'] = isset($config['system']['howl_disable']);

if ($_POST) {
	unset($input_errors);
	$pconfig = $_POST;

	/* input validation */
	if ($_POST['tcpidletimeout'] && !is_numericint($_POST['tcpidletimeout'])) {
		$input_errors[] = _SYSTEMADVANCEDPHP_MSGVALIDTCP;	
	}
	if (($_POST['cert'] && !$_POST['key']) || ($_POST['key'] && !$_POST['cert'])) {
		$input_errors[] = _SYSTEMADVANCEDPHP_MSGVALIDCERTKEY;
	} else if ($_POST['cert'] && $_POST['key']) {
		if (!strstr($_POST['cert'], "BEGIN CERTIFICATE") || !strstr($_POST['cert'], "END CERTIFICATE"))
			$input_errors[] = _SYSTEMADVANCEDPHP_MSGVALIDCERT;
		if (!strstr($_POST['key'], "BEGIN RSA PRIVATE KEY") || !strstr($_POST['key'], "END RSA PRIVATE KEY"))
			$input_errors[] = _SYSTEMADVANCEDPHP_MSGVALIDKEY;
	}

	if (!$input_errors) {
		$config['bridge']['filteringbridge'] = $_POST['filteringbridge_enable'] ? true : false;
		$oldcert = $config['system']['webgui']['certificate'];
		$oldkey = $config['system']['webgui']['private-key'];
		$config['system']['webgui']['certificate'] = base64_encode($_POST['cert']);
		$config['system']['webgui']['private-key'] = base64_encode($_POST['key']);
		$config['system']['disableconsolemenu'] = $_POST['disableconsolemenu'] ? true : false;
		$config['system']['disablefirmwarecheck'] = $_POST['disablefirmwarecheck'] ? true : false;
		$config['system']['webgui']['expanddiags'] = $_POST['expanddiags'] ? true : false;
		if ($g['platform'] == "generic-pc") {
			$oldharddiskstandby = $config['system']['harddiskstandby'];
			$config['system']['harddiskstandby'] = $_POST['harddiskstandby'];
		}
		$config['system']['webgui']['noantilockout'] = $_POST['noantilockout'] ? true : false;
		$config['filter']['bypassstaticroutes'] = $_POST['bypassstaticroutes'] ? true : false;
		$config['system']['disablebeep'] = $_POST['disablebeep'] ? true : false;
		$config['system']['polling'] = $_POST['polling_enable'] ? true : false;
		$config['system']['tune'] = $_POST['tune_enable'] ? true : false;
		$config['system']['howl_disable'] = $_POST['howl_disable'] ? true : false;
		$config['system']['smart'] = $_POST['smart_enable'] ? true : false;
				
		write_config();
		
		if (($config['system']['webgui']['certificate'] != $oldcert) || ($config['system']['webgui']['private-key'] != $oldkey)) {
			touch($d_sysrebootreqd_path);
		} else if (($g['platform'] == "generic-pc") && ($config['system']['harddiskstandby'] != $oldharddiskstandby)) {
			if (!$config['system']['harddiskstandby']) {
				// Reboot needed to deactivate standby due to a stupid ATA-protocol
				touch($d_sysrebootreqd_path);
				unset($config['system']['harddiskstandby']);
			} else {
				// No need to set the standby-time if a reboot is needed anyway
				system_set_harddisk_standby();
			}
		}
		
		if (isset($config['system']['tune']))
			system_tuning();
		
		$retval = 0;
		if (!file_exists($d_sysrebootreqd_path)) {
			config_lock();
			
			$retval |= system_set_termcap();
			config_unlock();
		}
		$savemsg = get_std_save_message($retval);
	}
}
?>
<?php include("fbegin.inc"); ?>
<?php if ($input_errors) print_input_errors($input_errors); ?>
<?php if ($savemsg) print_info_box($savemsg); ?>
<p><span class="vexpl"><span class="red"><strong><?=_NOTE;?>:</strong></span><br><?=_SYSTEMADVANCEDPHP_NOTE;?></p>
<form action="system_advanced.php" method="post" name="iform" id="iform">
  <table width="100%" border="0" cellpadding="6" cellspacing="0">
    <tr> 
      <td colspan="2" class="list" height="12"></td>
    </tr>
    <tr> 
      <td colspan="2" valign="top" class="listtopic"><?=_SYSTEMADVANCEDPHP_WEBGUISSLKEY;?></td>
    </tr>
    <tr> 
      <td width="22%" valign="top" class="vncell"><?=_SYSTEMADVANCEDPHP_WEBCERT;?></td>
      <td width="78%" class="vtable"> 
        <textarea name="cert" cols="65" rows="7" id="cert" class="formpre"><?=htmlspecialchars($pconfig['cert']);?></textarea><br> 
        <?=_SYSTEMADVANCEDPHP_WEBCERTTEXT;?>
      </td>
    </tr>
    <tr> 
      <td width="22%" valign="top" class="vncell"><?=_SYSTEMADVANCEDPHP_WEBKEY;?></td>
      <td width="78%" class="vtable"> 
        <textarea name="key" cols="65" rows="7" id="key" class="formpre"><?=htmlspecialchars($pconfig['key']);?></textarea><br> 
        <?=_SYSTEMADVANCEDPHP_WEBKEYTEXT;?>
      </td>
    </tr>
    <tr> 
      <td colspan="2" class="list" height="12"></td>
    </tr>
    <tr> 
      <td colspan="2" valign="top" class="listtopic"><?=_SYSTEMADVANCEDPHP_MISC;?></td>
    </tr>
    <tr> 
      <td width="22%" valign="top" class="vncell"><?=_SYSTEMADVANCEDPHP_CON;?></td>
      <td width="78%" class="vtable"> 
        <input name="disableconsolemenu" type="checkbox" id="disableconsolemenu" value="yes" <?php if ($pconfig['disableconsolemenu']) echo "checked"; ?>>
        <strong><?=_SYSTEMADVANCEDPHP_DISCON;?></strong><span class="vexpl"><br><?=_SYSTEMADVANCEDPHP_CONTEXT;?></span>
      </td>
    </tr>
    <tr>
      <td valign="top" class="vncell"><?=_SYSTEMADVANCEDPHP_FIRM;?></td>
      <td class="vtable">
        <input name="disablefirmwarecheck" type="checkbox" id="disablefirmwarecheck" value="yes" <?php if ($pconfig['disablefirmwarecheck']) echo "checked"; ?>>
        <strong><?=_SYSTEMADVANCEDPHP_DISFIRM;?></strong><span class="vexpl"><br><?=_SYSTEMADVANCEDPHP_FIRMTEXT;?></span>
      </td>
    </tr>
		<tr> 
      <td width="22%" valign="top" class="vncell"><?=_SYSTEMADVANCEDPHP_NAV;?></td>
      <td width="78%" class="vtable"> 
        <input name="expanddiags" type="checkbox" id="expanddiags" value="yes" <?php if ($pconfig['expanddiags']) echo "checked"; ?>>
        <strong><?=_SYSTEMADVANCEDPHP_NAVTEXT;?></strong>
      </td>
    </tr>
    <tr> 
      <td width="22%" valign="top" class="vncell"><?=_SYSTEMADVANCEDPHP_BEEP;?></td>
      <td width="78%" class="vtable"> 
        <input name="disablebeep" type="checkbox" id="disablebeep" value="yes" <?php if ($pconfig['disablebeep']) echo "checked"; ?>>
        <strong><?=_SYSTEMADVANCEDPHP_DISBEEP;?></strong>
      </td>
    </tr>
    <tr> 
      <td width="22%" valign="top" class="vncell"><?=_SYSTEMADVANCEDPHP_SMART;?></td>
      <td width="78%" class="vtable"> 
        <input name="smart_enable" type="checkbox" id="smart_enable" value="yes" <?php if ($pconfig['smart_enable']) echo "checked"; ?>>
        <strong><?=_SYSTEMADVANCEDPHP_ENSMART;?></strong><br>
        <?=_SYSTEMADVANCEDPHP_SMARTTEXT;?>
      </td>
    </tr>
    <tr> 
      <td width="22%" valign="top" class="vncell"><?=_SYSTEMADVANCEDPHP_TUNING;?></td>
      <td width="78%" class="vtable"> 
        <input name="tune_enable" type="checkbox" id="tune_enable" value="yes" <?php if ($pconfig['tune_enable']) echo "checked"; ?>>
        <strong><?=_SYSTEMADVANCEDPHP_ENTUNING;?></strong>
      </td>
    </tr>
    <tr> 
      <td width="22%" valign="top" class="vncell"><?=_SYSTEMADVANCEDPHP_ZEROCONF;?></td>
      <td width="78%" class="vtable"> 
        <input name="howl_disable" type="checkbox" id="howl_disable" value="yes" <?php if ($pconfig['howl_disable']) echo "checked"; ?>>
        <strong><?=_SYSTEMADVANCEDPHP_DISZEROCONF;?></strong>
      </td>
    </tr>
    <tr> 
      <td width="22%" valign="top">&nbsp;</td>
      <td width="78%"> 
        <input name="Submit" type="submit" class="formbtn" value="<?=_SAVE;?>" onclick="enable_change(true)"> 
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
