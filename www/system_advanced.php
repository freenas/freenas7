#!/usr/local/bin/php
<?php
/*
	system_advanced.php
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

$pgtitle = array(gettext("System"), gettext("Advanced"));

$pconfig['disableconsolemenu'] = isset($config['system']['disableconsolemenu']);
$pconfig['disablefirmwarecheck'] = isset($config['system']['disablefirmwarecheck']);
$pconfig['disablebeep'] = isset($config['system']['disablebeep']);
$pconfig['tune_enable'] = isset($config['system']['tune']);
$pconfig['zeroconf'] = isset($config['system']['zeroconf']);
$pconfig['powerd'] = isset($config['system']['powerd']);

if ($_POST) {
	unset($input_errors);
	$pconfig = $_POST;

	// Input validation.
	if ($_POST['tcpidletimeout'] && !is_numericint($_POST['tcpidletimeout'])) {
		$input_errors[] = gettext("The TCP idle timeout must be an integer.");
	}

	if (!$input_errors) {
		// Process system tuning.
		if ($_POST['tune_enable']) {
			sysctl_tune(1);
		} else if (isset($config['system']['tune']) && (!$_POST['tune_enable'])) {
			// Simply force a reboot to reset to default values.
			// This makes programming easy :-) Also we are sure that
			// system will use origin values (maybe default values
			// change from one FreeBSD release to the next. This will
			// reduce maintenance).
			sysctl_tune(0);
			touch($d_sysrebootreqd_path);
		}

		$config['system']['disableconsolemenu'] = $_POST['disableconsolemenu'] ? true : false;
		$config['system']['disablefirmwarecheck'] = $_POST['disablefirmwarecheck'] ? true : false;
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
			$retval |= rc_update_service("mdnsresponder");
			if (isset($config['system']['tune']))
				$retval |= rc_update_service("sysctl");
			config_unlock();
		}

		$savemsg = get_std_save_message($retval);
	}
}

function sysctl_tune($mode) {
	global $config;

	if (!is_array($config['system']['sysctl']['param']))
		$config['system']['sysctl']['param'] = array();

	array_sort_key($config['system']['sysctl']['param'], "name");
	$a_sysctlvar = &$config['system']['sysctl']['param'];

	$a_mib = array(
		"net.inet.tcp.delayed_ack" => 0,
		"net.inet.tcp.sendspace" => 65536,
		"net.inet.tcp.recvspace" => 65536,
		"net.inet.udp.recvspace" => 65536,
		"net.inet.udp.maxdgram" => 57344,
		"net.local.stream.recvspace" => 65536,
		"net.local.stream.sendspace" => 65536,
		"kern.ipc.maxsockbuf" => 2097152,
		"kern.ipc.somaxconn" => 8192,
		"kern.ipc.nmbclusters" => 32768,
		"kern.maxfiles" => 65536,
		"kern.maxfilesperproc" => 32768,
		"net.inet.tcp.inflight.enable" => 0,
		"net.inet.tcp.path_mtu_discovery" => 0
	);

	switch ($mode) {
		case 0:
			// Remove system tune MIB's.
			while (list($name, $value) = each($a_mib)) {
				$id = array_search_ex($name, $a_sysctlvar, "name");
				if (false === $id)
					continue;
				unset($a_sysctlvar[$id]);
			}
			break;
			
		case 1:
			// Add system tune MIB's.
			while (list($name, $value) = each($a_mib)) {
				$id = array_search_ex($name, $a_sysctlvar, "name");
				if (false !== $id)
					continue;

				$param = array();
				$param['uuid'] = uuid();
				$param['name'] = $name;
				$param['value'] = $value;
				$param['comment'] = gettext("System tuning");

				$a_sysctlvar[] = $param;
			}
			break;
	}
}
?>
<?php include("fbegin.inc");?>
<table width="100%" border="0" cellpadding="0" cellspacing="0">
  <tr>
    <td class="tabnavtbl">
      <ul id="tabnav">
        <li class="tabact"><a href="system_advanced.php" title="<?=gettext("Reload page");?>"><span><?=gettext("Advanced");?></span></a></li>
        <li class="tabinact"><a href="system_email.php"><span><?=gettext("Email");?></span></a></li>
        <li class="tabinact"><a href="system_proxy.php"><span><?=gettext("Proxy");?></span></a></li>
        <li class="tabinact"><a href="system_swap.php"><span><?=gettext("Swap");?></span></a></li>
        <li class="tabinact"><a href="system_rc.php"><span><?=gettext("Command scripts");?></span></a></li>
        <li class="tabinact"><a href="system_cron.php"><span><?=gettext("Cron");?></span></a></li>
        <li class="tabinact"><a href="system_rcconf.php"><span><?=gettext("rc.conf");?></span></a></li>
        <li class="tabinact"><a href="system_sysctl.php"><span><?=gettext("sysctl.conf");?></span></a></li>
      </ul>
    </td>
  </tr>
  <tr>
    <td class="tabcont">
			<form action="system_advanced.php" method="post" name="iform" id="iform">
				<?php if ($input_errors) print_input_errors($input_errors);?>
				<?php if ($savemsg) print_info_box($savemsg);?>
			  <table width="100%" border="0" cellpadding="6" cellspacing="0">
			    <tr>
			      <td colspan="2" valign="top" class="listtopic"><?=gettext("Miscellaneous");?></td>
			    </tr>
			    <?php html_checkbox("disableconsolemenu", gettext("Console menu"), $pconfig['disableconsolemenu'] ? true : false, gettext("Disable console menu"), gettext("Changes to this option will take effect after a reboot."));?>
			    <?php if ("full" !== $g['platform']):?>
			    <?php html_checkbox("disablefirmwarecheck", gettext("Firmware version check"), $pconfig['disablefirmwarecheck'] ? true : false, gettext("Disable firmware version check"), sprintf(gettext("This will cause %s not to check for newer firmware versions when the <a href=%s>%s</a> page is viewed."), get_product_name(), "system_firmware.php", gettext("System").": ".gettext("Firmware")));?>
			    <?php endif;?>
			    <?php html_checkbox("disablebeep", gettext("System Beep"), $pconfig['disablebeep'] ? true : false, gettext("Disable speaker beep on startup and shutdown"));?>
			    <?php html_checkbox("tune_enable", gettext("Tuning"), $pconfig['tune_enable'] ? true : false, gettext("Enable tuning of some kernel variables"));?>
					<?php html_checkbox("powerd", gettext("Power Daemon"), $pconfig['powerd'] ? true : false, gettext("Enable the system power control utility"), gettext("The powerd utility monitors the system state and sets various power control options accordingly."));?>			    
					<?php html_checkbox("zeroconf", gettext("Zeroconf/Bonjour"), $pconfig['zeroconf'] ? true : false, gettext("Enable Zeroconf/Bonjour to advertise services of this device"));?>
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
