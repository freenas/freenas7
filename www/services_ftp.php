#!/usr/local/bin/php
<?php
/*
	services_ftp.php
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

$pgtitle = array(gettext("Services"), gettext("FTP"));

if (!is_array($config['ftp'])) {
	$config['ftp'] = array();
}

$pconfig['enable'] = isset($config['ftp']['enable']);
$pconfig['port'] = $config['ftp']['port'];
$pconfig['numberclients'] = $config['ftp']['numberclients'];
$pconfig['maxconperip'] = $config['ftp']['maxconperip'];
$pconfig['timeout'] = $config['ftp']['timeout'];
$pconfig['anonymous'] = isset($config['ftp']['anonymous']);
$pconfig['localuser'] = isset($config['ftp']['localuser']);
$pconfig['pasv_max_port'] = $config['ftp']['pasv_max_port'];
$pconfig['pasv_min_port'] = $config['ftp']['pasv_min_port'];
$pconfig['pasv_address'] = $config['ftp']['pasv_address'];
$pconfig['banner'] = $config['ftp']['banner'];
$pconfig['natmode'] = isset($config['ftp']['natmode']);
$pconfig['fxp'] = isset($config['ftp']['fxp']);
$pconfig['keepallfiles'] = isset($config['ftp']['keepallfiles']);
$pconfig['permitrootlogin'] = isset($config['ftp']['permitrootlogin']);

if ($_POST) {
	unset($input_errors);
	$pconfig = $_POST;

	/* input validation */
	$reqdfields = array();
	$reqdfieldsn = array();

	if ($_POST['enable']) {
		$reqdfields = array_merge($reqdfields, explode(" ", "port numberclients maxconperip timeout"));
		$reqdfieldsn = array_merge($reqdfieldsn, array(gettext("TCP port"),gettext("Number of clients"),gettext("Max. conn. per IP"),gettext("Timeout")));
	}

	do_input_validation($_POST, $reqdfields, $reqdfieldsn, &$input_errors);

	if ($_POST['enable'] && !is_port($_POST['port'])) {
		$input_errors[] = gettext("The TCP port must be a valid port number.");
	}
	if ($_POST['enable'] && ((1 > $_POST['numberclients']) || (50 < $_POST['numberclients']))) {
		$input_errors[] = gettext("The number of clients must be between 1 and 50.");
	}
	if ($_POST['enable'] && (0 > $_POST['maxconperip'])) {
		$input_errors[] = gettext("The max. connection per IP must be either 0 (unlimited) or greater.");
	}
	if ($_POST['enable'] && !is_numericint($_POST['timeout'])) {
		$input_errors[] = gettext("The maximum idle time be a number.");
	}
	if ($_POST['enable'] && ($_POST['pasv_address'])) {
		if (!is_ipaddr($_POST['pasv_address']))
			$input_errors[] = gettext("The pasv address must be a public IP address.");
	}
	if ($_POST['enable'] && ($_POST['pasv_max_port'] || $_POST['pasv_min_port'])) {
		if (!is_port($_POST['pasv_max_port']))
			$input_errors[] = sprintf(gettext("The %s port must be a valid port number."), gettext("pasv_max_port"));
		if (!is_port($_POST['pasv_min_port']))
			$input_errors[] = sprintf(gettext("The %s port must be a valid port number."), gettext("pasv_min_port"));
	}
	if (!($_POST['anonymous']) && !($_POST['localuser'])) {
		$input_errors[] = gettext("You must select at minium anonymous or/and local user authentication.");
	}

	if (!$input_errors) {
		$config['ftp']['numberclients'] = $_POST['numberclients'];
		$config['ftp']['maxconperip'] = $_POST['maxconperip'];
		$config['ftp']['timeout'] = $_POST['timeout'];
		$config['ftp']['port'] = $_POST['port'];
		$config['ftp']['anonymous'] = $_POST['anonymous'] ? true : false;
		$config['ftp']['localuser'] = $_POST['localuser'] ? true : false;
		$config['ftp']['pasv_max_port'] = $_POST['pasv_max_port'];
		$config['ftp']['pasv_min_port'] = $_POST['pasv_min_port'];
		$config['ftp']['pasv_address'] = $_POST['pasv_address'];
		$config['ftp']['banner'] = $_POST['banner'];
		$config['ftp']['fxp'] = $_POST['fxp'] ? true : false;
		$config['ftp']['natmode'] = $_POST['natmode'] ? true : false;
		$config['ftp']['keepallfiles'] = $_POST['keepallfiles'] ? true : false;
		$config['ftp']['permitrootlogin'] = $_POST['permitrootlogin'] ? true : false;
		$config['ftp']['enable'] = $_POST['enable'] ? true : false;

		write_config();

		$retval = 0;
		if (!file_exists($d_sysrebootreqd_path)) {
			config_lock();
			$retval |= rc_update_service("pureftpd");
			$retval |= rc_update_service("mdnsresponder");
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
	var endis = !(document.iform.enable.checked || enable_change);
	document.iform.port.disabled = endis;
	document.iform.timeout.disabled = endis;
	document.iform.permitrootlogin.disabled = endis;
	document.iform.numberclients.disabled = endis;
	document.iform.maxconperip.disabled = endis;
	document.iform.anonymous.disabled = endis;
	document.iform.localuser.disabled = endis;
	document.iform.banner.disabled = endis;
	document.iform.fxp.disabled = endis;
	document.iform.natmode.disabled = endis;
	document.iform.keepallfiles.disabled = endis;
	document.iform.pasv_max_port.disabled = endis;
	document.iform.pasv_min_port.disabled = endis;
	document.iform.pasv_address.disabled = endis;
}
//-->
</script>
<?php if ($input_errors) print_input_errors($input_errors); ?>
<?php if ($savemsg) print_info_box($savemsg); ?>
<form action="services_ftp.php" method="post" name="iform" id="iform">
  <table width="100%" border="0" cellpadding="6" cellspacing="0">
    <tr>
      <td colspan="2" valign="top" class="optsect_t">
        <table border="0" cellspacing="0" cellpadding="0" width="100%">
				  <tr>
            <td class="optsect_s"><strong><?=gettext("FTP Server");?></strong></td>
				    <td align="right" class="optsect_s"><input name="enable" type="checkbox" value="yes" <?php if ($pconfig['enable']) echo "checked"; ?> onClick="enable_change(false)"> <strong><?=gettext("Enable"); ?></strong></td>
          </tr>
				</table>
      </td>
    </tr>
     <tr>
      <td width="22%" valign="top" class="vncellreq"><?=gettext("TCP port"); ?></td>
      <td width="78%" class="vtable">
        <?=$mandfldhtml;?><input name="port" type="text" class="formfld" id="port" size="20" value="<?=htmlspecialchars($pconfig['port']);?>">
       <br><?=gettext("Default is 21"); ?></td>
    </tr>
    <tr>
      <td width="22%" valign="top" class="vncellreq"><?=gettext("Number of clients"); ?></td>
      <td width="78%" class="vtable">
        <?=$mandfldhtml;?><input name="numberclients" type="text" class="formfld" id="numberclients" size="20" value="<?=htmlspecialchars($pconfig['numberclients']);?>">
      <br><?=gettext("Maximum number of simultaneous clients."); ?></td>
    </tr>
    <tr>
      <td width="22%" valign="top" class="vncellreq"><?=gettext("Max. conn. per IP"); ?></td>
      <td width="78%" class="vtable">
        <?=$mandfldhtml;?><input name="maxconperip" type="text" class="formfld" id="maxconperip" size="20" value="<?=htmlspecialchars($pconfig['maxconperip']);?>">
        <br><?=gettext("Maximum number of connections per IP address (0 = unlimited)."); ?></td>
    </tr>
    <tr>
      <td width="22%" valign="top" class="vncellreq"><?=gettext("Timeout") ;?></td>
      <td width="78%" class="vtable">
        <?=$mandfldhtml;?><input name="timeout" type="text" class="formfld" id="timeout" size="20" value="<?=htmlspecialchars($pconfig['timeout']);?>">
        <br><?=gettext("Maximum idle time in minutes.") ;?></td>
    </tr>
    <tr> 
			<td width="22%" valign="top" class="vncell"><?=gettext("Permit root login");?></td>
			<td width="78%" class="vtable">
				<input name="permitrootlogin" type="checkbox" id="permitrootlogin" value="yes" <?php if ($pconfig['permitrootlogin']) echo "checked"; ?>>
				<?=gettext("Specifies whether it is allowed to login as superuser (root) directly.");?>
			</td>
		</tr>
    <tr>
      <td width="22%" valign="top" class="vncell"><?=gettext("Anonymous login");?></td>
      <td width="78%" class="vtable">
        <input name="anonymous" type="checkbox" id="anonymous" value="yes" <?php if ($pconfig['anonymous']) echo "checked"; ?>>
        <?=gettext("Enable anonymous login.");?></td>
    </tr>
    <tr>
      <td width="22%" valign="top" class="vncell"><?=gettext("Local User");?></td>
      <td width="78%" class="vtable">
        <input name="localuser" type="checkbox" id="localuser" value="yes" <?php if ($pconfig['localuser']) echo "checked"; ?>>
        <?=gettext("Enable local user login.");?></td>
    </tr>
        <tr>
      <td width="22%" valign="top" class="vncell"><?=gettext("Banner");?></td>
      <td width="78%" class="vtable">
        <textarea name="banner" cols="65" rows="7" id="banner" class="formpre"><?=htmlspecialchars($pconfig['banner']);?></textarea>
        <br>
        <?=gettext("Greeting banner displayed by FTP when a connection first comes in.");?></td>
    </tr>
    <tr>
      <td width="22%" valign="top" class="vncell"><?=gettext("FXP");?></td>
      <td width="78%" class="vtable">
        <input name="fxp" type="checkbox" id="fxp" value="yes" <?php if ($pconfig['fxp']) echo "checked"; ?>>
        <?=gettext("Enable FXP protocol.");?><span class="vexpl"><br>
        <?=gettext("FXP allows transfers between two remote servers without any file data going to the client asking for the transfer (insecure!).");?></span></td>
    </tr>
    <tr>
      <td width="22%" valign="top" class="vncell"><?=gettext("NAT mode");?></td>
      <td width="78%" class="vtable">
        <input name="natmode" type="checkbox" id="natmode" value="yes" <?php if ($pconfig['natmode']) echo "checked"; ?>>
        <?=gettext("Force NAT mode.");?><span class="vexpl"><br>
        <?=gettext("Enable this if your FTP server is behind a NAT box that doesn't support applicative FTP proxying.");?></span></td>
    </tr>
    <tr>
      <td width="22%" valign="top" class="vncell"><?=gettext("Resume");?></td>
      <td width="78%" class="vtable">
        <input name="keepallfiles" type="checkbox" id="keepallfiles" value="yes" <?php if ($pconfig['keepallfiles']) echo "checked"; ?>>
        <?=gettext("Enable resume mode.");?><span class="vexpl"><br>
        <?=gettext("Use this option to enable resuming broken transfers at the point of interruption.");?></span></td>
    </tr>
    <tr>
      <td width="22%" valign="top" class="vncell"><?=gettext("Passive IP address"); ?></td>
      <td width="78%" class="vtable">
        <input name="pasv_address" type="text" class="formfld" id="pasv_address" size="20" value="<?=htmlspecialchars($pconfig['pasv_address']);?>">
        <br><?=gettext("Use this option to override the IP address that FTP daemon will advertise in response to the PASV command."); ?></td>
  	</tr>
  	<tr>
      <td width="22%" valign="top" class="vncellbg"><?=gettext("pasv_min_port"); ?></td>
      <td width="78%" class="">
        <?=$mandfldhtml;?><input name="pasv_min_port" type="text" class="formfld" id="pasv_min_port" size="20" value="<?=htmlspecialchars($pconfig['pasv_min_port']);?>">
      <br><?=gettext("The minimum port to allocate for PASV style data connections (0 = use any port)."); ?></td>
  	</tr>
  	<tr>
      <td width="22%" valign="top" class="vncell"><?=gettext("pasv_max_port"); ?></td>
      <td width="78%" class="vtable">
        <?=$mandfldhtml;?><input name="pasv_max_port" type="text" class="formfld" id="pasv_max_port" size="20" value="<?=htmlspecialchars($pconfig['pasv_max_port']);?>">
      <br><?=gettext("The maximum port to allocate for PASV style data connections (0 = use any port)."); ?></td>
  	</tr>
  	<tr>
      <td width="22%" valign="top">&nbsp;</td>
      <td width="78%">
        <input name="Submit" type="submit" class="formbtn" value="<?=gettext("Save and Restart");?>" onClick="enable_change(true)">
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
