#!/usr/local/bin/php
<?php
/*
	services_sshd.php
	part of FreeNAS (http://www.freenas.org)
	Copyright (C) 2005-2007 Olivier Cochard-Labbe <olivier@freenas.org>.
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

$pgtitle = array(gettext("Services"),gettext("SSHD"));

if (!is_array($config['sshd'])) {
	$config['sshd'] = array();
}

$pconfig['port'] = $config['sshd']['port'];
$pconfig['permitrootlogin'] = isset($config['sshd']['permitrootlogin']);
$pconfig['tcpforwarding'] = isset($config['sshd']['tcpforwarding']);
$pconfig['enable'] = isset($config['sshd']['enable']);
$pconfig['key'] = base64_decode($config['sshd']['private-key']);
$pconfig['passwordauthentication'] = isset($config['sshd']['passwordauthentication']);
$pconfig['compression'] = $config['sshd']['compression'];

if ($_POST)
{
	unset($input_errors);
	$pconfig = $_POST;

	/* input validation */
	$reqdfields = array();
	$reqdfieldsn = array();

	if ($_POST['enable']) {
		$reqdfields = array_merge($reqdfields, explode(" ", "port"));
		$reqdfieldsn = array_merge($reqdfieldsn, array(gettext("TCP port")));
	}

	do_input_validation($_POST, $reqdfields, $reqdfieldsn, &$input_errors);

	if (($_POST['port']) && !is_port($_POST['port'])) {
		$input_errors[] = gettext("The TCP port must be a valid port number.");
	}

	if ($_POST['key']) {
		if (!strstr($_POST['key'], "BEGIN DSA PRIVATE KEY") || !strstr($_POST['key'], "END DSA PRIVATE KEY"))
			$input_errors[] = gettext("This key does not appear to be valid.");
	}

	if (!$input_errors) {
		$config['sshd']['port'] = $_POST['port'];
		$config['sshd']['permitrootlogin'] = $_POST['permitrootlogin'] ? true : false;
		$config['sshd']['tcpforwarding'] = $_POST['tcpforwarding'] ? true : false;
		$config['sshd']['enable'] = $_POST['enable'] ? true : false;
		$config['sshd']['private-key'] = base64_encode($_POST['key']);
		$config['sshd']['passwordauthentication'] = $_POST['passwordauthentication'] ? true : false;
		$config['sshd']['compression'] = $_POST['compression'] ? true : false;
		write_config();

		$retval = 0;
		if (!file_exists($d_sysrebootreqd_path)) {
			config_lock();
			$retval |= rc_update_service("sshd");
			$retval |= rc_update_service("mdnsresponder");
			config_unlock();
		}
		$savemsg = get_std_save_message($retval);
	}
}
?>
<?php include("fbegin.inc");?>
<script language="JavaScript">
<!--
function enable_change(enable_change) {
	var endis = !(document.iform.enable.checked || enable_change);
	document.iform.port.disabled = endis;
	document.iform.key.disabled = endis;
	document.iform.permitrootlogin.disabled = endis;
	document.iform.passwordauthentication.disabled = endis;
	document.iform.tcpforwarding.disabled = endis;
	document.iform.compression.disabled = endis;
}
//-->
</script>
<form action="services_sshd.php" method="post" name="iform" id="iform">
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
									<td class="optsect_s"><strong><?=gettext("SSH Daemon");?></strong></td>
									<td align="right" class="optsect_s"><input name="enable" type="checkbox" value="yes" <?php if ($pconfig['enable']) echo "checked"; ?> onClick="enable_change(false)"> <strong><?=gettext("Enable");?></strong></td>
								</tr>
							</table>
						</td>
			    </tr>
			    <tr>
			      <td width="22%" valign="top" class="vncellreq"><?=gettext("TCP port");?></td>
			      <td width="78%" class="vtable">
							<input name="port" type="text" class="formfld" id="port" size="20" value="<?=htmlspecialchars($pconfig['port']);?>">
							<br><?=gettext("Alternate TCP port. Default is 22");?></td>
			      </td>
			    </tr>
			    <tr>
			      <td width="22%" valign="top" class="vncell"><?=gettext("Permit root login");?></td>
			      <td width="78%" class="vtable">
			        <input name="permitrootlogin" type="checkbox" id="permitrootlogin" value="yes" <?php if ($pconfig['permitrootlogin']) echo "checked"; ?>>
			        <?=gettext("Specifies whether it is allowed to login as superuser (root) directly.");?>
			    </tr>
					<tr>
						<td width="22%" valign="top" class="vncell"><?=gettext("Password authentication");?></td>
						<td width="78%" class="vtable">
							<input name="passwordauthentication" type="checkbox" id="passwordauthentication" value="yes" <?php if ($pconfig['passwordauthentication']) echo "checked"; ?>>
							<?=gettext("Enable keyboard-interactive authentication.");?>
					</tr>
			    <tr>
			      <td width="22%" valign="top" class="vncell"><?=gettext("TCP forwarding");?></td>
			      <td width="78%" class="vtable">
			        <input name="tcpforwarding" type="checkbox" id="tcpforwarding" value="yes" <?php if ($pconfig['tcpforwarding']) echo "checked"; ?>>
			        <?=gettext("Permit to do SSH Tunneling.");?>
			    </tr>
			    <tr>
			      <td width="22%" valign="top" class="vncell"><?=gettext("Compression");?></td>
			      <td width="78%" class="vtable">
			        <input name="compression" type="checkbox" id="compression" value="yes" <?php if ($pconfig['compression']) echo "checked"; ?>>
			        <?=gettext("Enable compression.");?>
			    </tr>
					<tr>
						<td colspan="2" class="list" height="12"></td>
					</tr>
			    <tr>
			      <td colspan="2" valign="top" class="listtopic"><?=gettext("SSH key");?></td>
			    </tr>
			    <tr>
			      <td width="22%" valign="top" class="vncell"><?=gettext("Private Key");?></td>
			      <td width="78%" class="vtable">
			        <textarea name="key" cols="65" rows="7" id="key" class="formpre"><?=htmlspecialchars($pconfig['key']);?></textarea>
			        <br>
			        <?=gettext("Paste a DSA PRIVATE KEY in PEM format here.");?></td>
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
