#!/usr/local/bin/php
<?php
/*
	system_advanced.php
	part of FreeNAS (http://www.freenas.org)
	Copyright (C) 2005-2008 Olivier Cochard-Labbé <olivier@freenas.org>.
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

$pgtitle = array(gettext("System"), gettext("Advanced"), gettext("Advanced"));

$pconfig['disableconsolemenu'] = isset($config['system']['disableconsolemenu']);
$pconfig['disablefirmwarecheck'] = isset($config['system']['disablefirmwarecheck']);
$pconfig['expanddiags'] = isset($config['system']['webgui']['expanddiags']);
$pconfig['disablebeep'] = isset($config['system']['disablebeep']);
$pconfig['tune_enable'] = isset($config['system']['tune']);
$pconfig['zeroconf'] = isset($config['system']['zeroconf']);
$pconfig['powerd'] = isset($config['system']['powerd']);

if ($_POST) {
	unset($input_errors);
	$pconfig = $_POST;

	/* input validation */
	if ($_POST['tcpidletimeout'] && !is_numericint($_POST['tcpidletimeout'])) {
		$input_errors[] = gettext("The TCP idle timeout must be an integer.");
	}

	if (!$input_errors) {
		$config['system']['disableconsolemenu'] = $_POST['disableconsolemenu'] ? true : false;
		$config['system']['disablefirmwarecheck'] = $_POST['disablefirmwarecheck'] ? true : false;
		$config['system']['webgui']['expanddiags'] = $_POST['expanddiags'] ? true : false;
		$config['system']['webgui']['noantilockout'] = $_POST['noantilockout'] ? true : false;
		$config['system']['disablebeep'] = $_POST['disablebeep'] ? true : false;
		$config['system']['tune'] = $_POST['tune_enable'] ? true : false;
		$config['system']['zeroconf'] = $_POST['zeroconf'] ? true : false;
		$config['system']['powerd'] = $_POST['powerd'] ? true : false;

		write_config();

		$retval = 0;
		if (!file_exists($d_sysrebootreqd_path)) {
			config_lock();
			$retval |= rc_update_service("powerd");
			$retval |= rc_update_service("systune");
			$retval |= rc_update_service("mdnsresponder");
			config_unlock();
		}

		$savemsg = get_std_save_message($retval);
	}
}
?>
<?php include("fbegin.inc");?>
<?php if ($input_errors) print_input_errors($input_errors);?>
<?php if ($savemsg) print_info_box($savemsg);?>
<table width="100%" border="0" cellpadding="0" cellspacing="0">
  <tr>
    <td class="tabnavtbl">
      <ul id="tabnav">
        <li class="tabact"><a href="system_advanced.php" title="<?=gettext("Reload page");?>"><?=gettext("Advanced");?></a></li>
        <li class="tabinact"><a href="system_email.php"><?=gettext("Email");?></a></li>
        <li class="tabinact"><a href="system_proxy.php"><?=gettext("Proxy");?></a></li>
        <li class="tabinact"><a href="system_swap.php"><?=gettext("Swap");?></a></li>
        <li class="tabinact"><a href="system_rc.php"><?=gettext("Command scripts");?></a></li>
        <li class="tabinact"><a href="system_cron.php"><?=gettext("Cron");?></a></li>
      </ul>
    </td>
  </tr>
  <tr>
    <td class="tabcont">
			<form action="system_advanced.php" method="post" name="iform" id="iform">
			  <table width="100%" border="0" cellpadding="6" cellspacing="0">
			    <tr>
			      <td colspan="2" valign="top" class="listtopic"><?=gettext("Miscellaneous");?></td>
			    </tr>
			    <tr>
			      <td width="22%" valign="top" class="vncell"><?=gettext("Console menu");?></td>
			      <td width="78%" class="vtable">
			        <input name="disableconsolemenu" type="checkbox" id="disableconsolemenu" value="yes" <?php if ($pconfig['disableconsolemenu']) echo "checked";?>>
			        <?=gettext("Disable console menu");?></br>
							<span class="vexpl"><?=gettext("Changes to this option will take effect after a reboot.");?></span>
			      </td>
			    </tr>
			    <?php if ("full" !== $g['platform']):?>
			    <tr>
			      <td valign="top" class="vncell"><?=gettext("Firmware version check");?></td>
			      <td class="vtable">
			        <input name="disablefirmwarecheck" type="checkbox" id="disablefirmwarecheck" value="yes" <?php if ($pconfig['disablefirmwarecheck']) echo "checked";?>>
			        <?=gettext("Disable firmware version check");?></br>
							<span class="vexpl"><?php echo sprintf(gettext("This will cause %s not to check for newer firmware versions when the <a href=%s>%s</a> page is viewed."), get_product_name(), "system_firmware.php", gettext("System").": ".gettext("Firmware"));?></span>
			      </td>
			    </tr>
			    <?php endif;?>
					<tr>
			      <td width="22%" valign="top" class="vncell"><?=gettext("Navigation");?></td>
			      <td width="78%" class="vtable">
			        <input name="expanddiags" type="checkbox" id="expanddiags" value="yes" <?php if ($pconfig['expanddiags']) echo "checked";?>>
			        <?=gettext("Keep diagnostics in navigation expanded");?>
			      </td>
			    </tr>
			    <tr>
			      <td width="22%" valign="top" class="vncell"><?=gettext("System Beep");?></td>
			      <td width="78%" class="vtable">
			        <input name="disablebeep" type="checkbox" id="disablebeep" value="yes" <?php if ($pconfig['disablebeep']) echo "checked";?>>
			        <?=gettext("Disable speaker beep on startup and shutdown");?>
			      </td>
			    </tr>
			    <tr>
			      <td width="22%" valign="top" class="vncell"><?=gettext("Tuning");?></td>
			      <td width="78%" class="vtable">
			        <input name="tune_enable" type="checkbox" id="tune_enable" value="yes" <?php if ($pconfig['tune_enable']) echo "checked";?>>
			        <?=gettext("Enable tuning of some kernel variables");?>
			      </td>
			    </tr>
			    <tr>
			      <td width="22%" valign="top" class="vncell"><?=gettext("Power Daemon");?></td>
			      <td width="78%" class="vtable">
			        <input name="powerd" type="checkbox" id="powerd" value="yes" <?php if ($pconfig['powerd']) echo "checked"; ?>>
			        <?=gettext("Enable the system power control utility");?></br>
							<span class="vexpl"><?=gettext("The powerd utility monitors the system state and sets various power control options accordingly.");?></span>
			      </td>
			    </tr>
			    <tr>
			      <td width="22%" valign="top" class="vncell"><?=gettext("Zeroconf");?></td>
			      <td width="78%" class="vtable">
			        <input name="zeroconf" type="checkbox" id="zeroconf" value="yes" <?php if ($pconfig['zeroconf']) echo "checked";?>>
			        <?=gettext("Enable Zeroconf");?>
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
    </td>
  </tr>
</table>
<?php include("fend.inc");?>
