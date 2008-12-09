#!/usr/local/bin/php
<?php
/*
	services_ftp.php
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

$pgtitle = array(gettext("Services"), gettext("FTP"));

if (!is_array($config['ftpd'])) {
	$config['ftpd'] = array();
}

$pconfig['enable'] = isset($config['ftpd']['enable']);
$pconfig['port'] = $config['ftpd']['port'];
$pconfig['numberclients'] = $config['ftpd']['numberclients'];
$pconfig['maxconperip'] = $config['ftpd']['maxconperip'];
$pconfig['timeout'] = $config['ftpd']['timeout'];
$pconfig['anonymousonly'] = isset($config['ftpd']['anonymousonly']);
$pconfig['localusersonly'] = isset($config['ftpd']['localusersonly']);
$pconfig['pasv_max_port'] = $config['ftpd']['pasv_max_port'];
$pconfig['pasv_min_port'] = $config['ftpd']['pasv_min_port'];
$pconfig['pasv_address'] = $config['ftpd']['pasv_address'];
$pconfig['userbandwidthup'] = $config['ftpd']['userbandwidth']['up'];
$pconfig['userbandwidthdown'] = $config['ftpd']['userbandwidth']['down'];
$pconfig['anonymousbandwidthup'] = $config['ftpd']['anonymousbandwidth']['up'];
$pconfig['anonymousbandwidthdown'] = $config['ftpd']['anonymousbandwidth']['down'];
$pconfig['extraoptions'] = $config['ftpd']['extraoptions'];

if ($config['ftpd']['filemask']) {
	$pconfig['filemask'] = $config['ftpd']['filemask'];
} else {
	$pconfig['filemask'] = "077";
}

if ($config['ftpd']['directorymask']) {
	$pconfig['directorymask'] = $config['ftpd']['directorymask'];
} else {
	$pconfig['directorymask'] = "022";
}

$pconfig['banner'] = $config['ftpd']['banner'];
$pconfig['natmode'] = isset($config['ftpd']['natmode']);
$pconfig['fxp'] = isset($config['ftpd']['fxp']);
$pconfig['keepallfiles'] = isset($config['ftpd']['keepallfiles']);
$pconfig['permitrootlogin'] = isset($config['ftpd']['permitrootlogin']);
$pconfig['chrooteveryone'] = isset($config['ftpd']['chrooteveryone']);
$pconfig['tls'] = $config['ftpd']['tls'];
$pconfig['privatekey'] = base64_decode($config['ftpd']['privatekey']);
$pconfig['certificate'] = base64_decode($config['ftpd']['certificate']);

if ($_POST) {
	unset($input_errors);
	$pconfig = $_POST;

	if ($_POST['enable']) {
		// Input validation.
		$reqdfields = explode(" ", "port numberclients maxconperip timeout");
		$reqdfieldsn = array(gettext("TCP port"), gettext("Number of clients"), gettext("Max. conn. per IP"), gettext("Timeout"));
		$reqdfieldst = explode(" ", "numeric numeric numeric numeric");

		if ("0" != $_POST['tls']) {
			$reqdfields = array_merge($reqdfields, explode(" ", "certificate privatekey"));
			$reqdfieldsn = array_merge($reqdfieldsn, array(gettext("Certificate"), gettext("Private key")));
			$reqdfieldst = array_merge($reqdfieldst, explode(" ", "certificate privatekey"));
		}

		do_input_validation($_POST, $reqdfields, $reqdfieldsn, &$input_errors);
		do_input_validation_type($_POST, $reqdfields, $reqdfieldsn, $reqdfieldst, &$input_errors);

		if (!is_port($_POST['port'])) {
			$input_errors[] = gettext("The TCP port must be a valid port number.");
		}

		if ((1 > $_POST['numberclients']) || (50 < $_POST['numberclients'])) {
			$input_errors[] = gettext("The number of clients must be between 1 and 50.");
		}

		if (0 > $_POST['maxconperip']) {
			$input_errors[] = gettext("The max. connection per IP must be either 0 (unlimited) or greater.");
		}

		if (!is_numericint($_POST['timeout'])) {
			$input_errors[] = gettext("The maximum idle time be a number.");
		}

		if (!("0" === $_POST['pasv_min_port']) && !is_port($_POST['pasv_min_port'])) {
			$input_errors[] = sprintf(gettext("The %s port must be a valid port number."), gettext("pasv_min_port"));
		}

		if (!("0" === $_POST['pasv_max_port']) && !is_port($_POST['pasv_max_port'])) {
			$input_errors[] = sprintf(gettext("The %s port must be a valid port number."), gettext("pasv_max_port"));
		}

		if ($_POST['anonymousonly'] && $_POST['localusersonly']) {
			$input_errors[] = gettext("It is impossible to enable 'Anonymous users only' and 'Local users only' authentication simultaneously.");
		}
	}

	if (!$input_errors) {
		$config['ftpd']['enable'] = $_POST['enable'] ? true : false;
		$config['ftpd']['numberclients'] = $_POST['numberclients'];
		$config['ftpd']['maxconperip'] = $_POST['maxconperip'];
		$config['ftpd']['timeout'] = $_POST['timeout'];
		$config['ftpd']['port'] = $_POST['port'];
		$config['ftpd']['anonymousonly'] = $_POST['anonymousonly'] ? true : false;
		$config['ftpd']['localusersonly'] = $_POST['localusersonly'] ? true : false;
		$config['ftpd']['pasv_max_port'] = $_POST['pasv_max_port'];
		$config['ftpd']['pasv_min_port'] = $_POST['pasv_min_port'];
		$config['ftpd']['pasv_address'] = $_POST['pasv_address'];
		$config['ftpd']['banner'] = $_POST['banner'];
		$config['ftpd']['filemask'] = $_POST['filemask'];
		$config['ftpd']['directorymask'] = $_POST['directorymask'];
		$config['ftpd']['fxp'] = $_POST['fxp'] ? true : false;
		$config['ftpd']['natmode'] = $_POST['natmode'] ? true : false;
		$config['ftpd']['keepallfiles'] = $_POST['keepallfiles'] ? true : false;
		$config['ftpd']['permitrootlogin'] = $_POST['permitrootlogin'] ? true : false;
		$config['ftpd']['chrooteveryone'] = $_POST['chrooteveryone'] ? true : false;
		$config['ftpd']['tls'] = $_POST['tls'];
		$config['ftpd']['privatekey'] = base64_encode($_POST['privatekey']);
		$config['ftpd']['certificate'] = base64_encode($_POST['certificate']);
		$config['ftpd']['userbandwidth']['up'] = $pconfig['userbandwidthup'];
		$config['ftpd']['userbandwidth']['down'] = $pconfig['userbandwidthdown'];
		$config['ftpd']['anonymousbandwidth']['up'] = $pconfig['anonymousbandwidthup'];
		$config['ftpd']['anonymousbandwidth']['down'] = $pconfig['anonymousbandwidthdown'];
		$config['ftpd']['extraoptions'] = $_POST['extraoptions'];

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
<?php include("fbegin.inc");?>
<script language="JavaScript">
<!--
function enable_change(enable_change) {
	var endis = !(document.iform.enable.checked || enable_change);
	document.iform.port.disabled = endis;
	document.iform.timeout.disabled = endis;
	document.iform.permitrootlogin.disabled = endis;
	document.iform.numberclients.disabled = endis;
	document.iform.maxconperip.disabled = endis;
	document.iform.anonymousonly.disabled = endis;
	document.iform.localusersonly.disabled = endis;
	document.iform.banner.disabled = endis;
	document.iform.fxp.disabled = endis;
	document.iform.natmode.disabled = endis;
	document.iform.keepallfiles.disabled = endis;
	document.iform.pasv_max_port.disabled = endis;
	document.iform.pasv_min_port.disabled = endis;
	document.iform.pasv_address.disabled = endis;
	document.iform.filemask.disabled = endis;
	document.iform.directorymask.disabled = endis;
	document.iform.chrooteveryone.disabled = endis;
	document.iform.tls.disabled = endis;
	document.iform.privatekey.disabled = endis;
	document.iform.certificate.disabled = endis;
	document.iform.userbandwidthup.disabled = endis;
	document.iform.userbandwidthdown.disabled = endis;
	document.iform.anonymousbandwidthup.disabled = endis;
	document.iform.anonymousbandwidthdown.disabled = endis;
	document.iform.extraoptions.disabled = endis;
}

function tls_change() {
	switch (document.iform.tls.selectedIndex) {
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

function localusersonly_change() {
	switch (document.iform.localusersonly.checked) {
		case true:
			showElementById('anonymousbandwidthup_tr','hide');
			showElementById('anonymousbandwidthdown_tr','hide');
			break;

		case false:
			showElementById('anonymousbandwidthup_tr','show');
			showElementById('anonymousbandwidthdown_tr','show');
			break;
	}
}

function anonymousonly_change() {
	switch (document.iform.anonymousonly.checked) {
		case true:
			showElementById('userbandwidthup_tr','hide');
			showElementById('userbandwidthdown_tr','hide');
			break;

		case false:
			showElementById('userbandwidthup_tr','show');
			showElementById('userbandwidthdown_tr','show');
			break;
	}
}
//-->
</script>
<form action="services_ftp.php" method="post" name="iform" id="iform">
	<table width="100%" border="0" cellpadding="0" cellspacing="0">
	  <tr>
	    <td class="tabcont">
	    	<?php if ($input_errors) print_input_errors($input_errors);?>
				<?php if ($savemsg) print_info_box($savemsg);?>
			  <table width="100%" border="0" cellpadding="6" cellspacing="0">
			  	<?php html_titleline_checkbox("enable", gettext("File Transfer Protocol"), $pconfig['enable'] ? true : false, gettext("Enable"), "enable_change(false)");?>
					<tr>
			      <td width="22%" valign="top" class="vncellreq"><?=gettext("TCP port"); ?></td>
			      <td width="78%" class="vtable">
			        <input name="port" type="text" class="formfld" id="port" size="20" value="<?=htmlspecialchars($pconfig['port']);?>">
							<br><?=gettext("Default is 21");?>
						</td>
			    </tr>
			    <tr>
			      <td width="22%" valign="top" class="vncellreq"><?=gettext("Number of clients"); ?></td>
			      <td width="78%" class="vtable">
			      	<input name="numberclients" type="text" class="formfld" id="numberclients" size="20" value="<?=htmlspecialchars($pconfig['numberclients']);?>">
			      	<br><?=gettext("Maximum number of simultaneous clients.");?>
						</td>
			    </tr>
			    <tr>
			      <td width="22%" valign="top" class="vncellreq"><?=gettext("Max. conn. per IP"); ?></td>
			      <td width="78%" class="vtable">
			        <input name="maxconperip" type="text" class="formfld" id="maxconperip" size="20" value="<?=htmlspecialchars($pconfig['maxconperip']);?>">
			        <br><?=gettext("Maximum number of connections per IP address (0 = unlimited).");?>
						</td>
			    </tr>
			    <tr>
			      <td width="22%" valign="top" class="vncellreq"><?=gettext("Timeout") ;?></td>
			      <td width="78%" class="vtable">
			        <input name="timeout" type="text" class="formfld" id="timeout" size="20" value="<?=htmlspecialchars($pconfig['timeout']);?>">
			        <br><?=gettext("Maximum idle time in minutes.");?>
						</td>
			    </tr>
			    <tr>
						<td width="22%" valign="top" class="vncell"><?=gettext("Permit root login");?></td>
						<td width="78%" class="vtable">
							<input name="permitrootlogin" type="checkbox" id="permitrootlogin" value="yes" <?php if ($pconfig['permitrootlogin']) echo "checked"; ?>>
							<?=gettext("Specifies whether it is allowed to login as superuser (root) directly.");?>
						</td>
					</tr>
					<?php html_checkbox("anonymousonly", gettext("Anonymous users only"), $pconfig['anonymousonly'] ? true : false, gettext("Only allow anonymous users. Use this on a public FTP site with no remote FTP access to real accounts."), "", false, "anonymousonly_change()");?>
					<?php html_checkbox("localusersonly", gettext("Local users only"), $pconfig['localusersonly'] ? true : false, gettext("Only allow authenticated users. Anonymous logins are prohibited."), "", false, "localusersonly_change()");?>
					<?php html_textarea("banner", gettext("Banner"), $pconfig['banner'], gettext("Greeting banner displayed by FTP when a connection first comes in."), false, 65, 7);?>
					<?php html_separator();?>
					<?php html_titleline(gettext("Advanced settings"));?>
					<tr id="filemask">
						<td width="22%" valign="top" class="vncell"><?=gettext("Create mask"); ?></td>
						<td width="78%" class="vtable">
							<input name="filemask" type="text" class="formfld" id="filemask" size="30" value="<?=htmlspecialchars($pconfig['filemask']);?>">
							<br><?=gettext("Use this option to override the file creation mask (077 by default).");?>
						</td>
					</tr>
					<tr id="directorymask">
						<td width="22%" valign="top" class="vncell"><?=gettext("Directory mask"); ?></td>
						<td width="78%" class="vtable">
							<input name="directorymask" type="text" class="formfld" id="directorymask" size="30" value="<?=htmlspecialchars($pconfig['directorymask']);?>">
							<br><?=gettext("Use this option to override the directory creation mask (022 by default).");?>
						</td>
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
			      <td width="22%" valign="top" class="vncell"><?=gettext("Keep all files");?></td>
			      <td width="78%" class="vtable">
			        <input name="keepallfiles" type="checkbox" id="keepallfiles" value="yes" <?php if ($pconfig['keepallfiles']) echo "checked"; ?>>
			        <?=gettext("Allow users to resume and upload files, but NOT to delete or rename them. Directories can be removed, but only if they are empty. However, overwriting existing files is still allowed.");?>
			      </td>
			    </tr>
			    <tr>
						<td width="22%" valign="top" class="vncell"><?=gettext("chroot everyone");?></td>
						<td width="78%" class="vtable">
							<input name="chrooteveryone" type="checkbox" id="chrooteveryone" value="yes" <?php if ($pconfig['chrooteveryone']) echo "checked"; ?>>
							<?=gettext("chroot() everyone, but root.");?>
						</td>
					</tr>
					<tr>
						<td width="22%" valign="top" class="vncell"><?=gettext("Passive IP address");?></td>
						<td width="78%" class="vtable">
							<input name="pasv_address" type="text" class="formfld" id="pasv_address" size="20" value="<?=htmlspecialchars($pconfig['pasv_address']);?>">
							<br><?=gettext("Force the specified IP address in reply to a PASV/EPSV/SPSV command. If the server is behind a masquerading (NAT) box that doesn't properly handle stateful FTP masquerading, put the ip address of that box here. If you have a dynamic IP address, you can put the public host name of your gateway, that will be resolved every time a new client will connect.");?>
						</td>
					</tr>
					<tr>
						<td width="22%" valign="top" class="vncellbg"><?=gettext("Passive mode ports");?></td>
						<td width="78%" class="">
							<input name="pasv_min_port" type="text" class="formfld" id="pasv_min_port" size="20" value="<?=htmlspecialchars($pconfig['pasv_min_port']);?>"><br/>
							<span class="vexpl"><?=gettext("The minimum port to allocate for PASV style data connections (0 = use any port).");?></span>
						</td>
					</tr>
					<tr>
						<td width="22%" valign="top" class="vncell">&nbsp;</td>
						<td width="78%" class="vtable">
							<input name="pasv_max_port" type="text" class="formfld" id="pasv_max_port" size="20" value="<?=htmlspecialchars($pconfig['pasv_max_port']);?>"><br/>
							<span class="vexpl"><?=gettext("The maximum port to allocate for PASV style data connections (0 = use any port).");?></span><br/><br/>
							<span class="vexpl"><?=gettext("Only ports in the range min. port to max. port inclusive are used for passive-mode downloads. This is especially useful if the server is behind a firewall without FTP connection tracking. Use high ports (40000-50000 for instance), where no regular server should be listening.");?></span>
						</td>
					</tr>
					<?php html_inputbox("userbandwidthup", gettext("Local user bandwidth"), $pconfig['userbandwidthup'], gettext("Local user upload bandwith in KB/s. An empty field means infinity."), false, 5);?>
					<?php html_inputbox("userbandwidthdown", "", $pconfig['userbandwidthdown'], gettext("Local user download bandwith in KB/s. An empty field means infinity."), false, 5);?>
					<?php html_inputbox("anonymousbandwidthup", gettext("Anonymous user bandwidth"), $pconfig['anonymousbandwidthup'], gettext("Anonymous user upload bandwith in KB/s. An empty field means infinity."), false, 5);?>
					<?php html_inputbox("anonymousbandwidthdown", "", $pconfig['anonymousbandwidthdown'], gettext("Anonymous user download bandwith in KB/s. An empty field means infinity."), false, 5);?>
					<tr>
						<td width="22%" valign="top" class="vncell"><?=gettext("SSL/TLS");?></td>
						<td width="78%" class="vtable">
							<select name="tls" class="formfld" id="tls" onchange="tls_change()">
								<?php $types = array(gettext("Disable"),gettext("TLS + cleartext"),gettext("Enforce TLS")); $vals = explode(" ", "0 1 2");?>
								<?php $j = 0; for ($j = 0; $j < count($vals); $j++):?>
								<option value="<?=$vals[$j];?>" <?php if ($vals[$j] === $pconfig['tls']) echo "selected";?>><?=htmlspecialchars($types[$j]);?></option>
								<?php endfor;?>
							</select><br/>
							<span class="vexpl"><?=gettext("Use SSL/TLS encryption layer.");?></span>
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
					<?php html_inputbox("extraoptions", gettext("Extra options"), $pconfig['extraoptions'], gettext("Extra options (usually empty)."), false, 40);?>
			  </table>
				<div id="submit">
					<input name="Submit" type="submit" class="formbtn" value="<?=gettext("Save and Restart");?>" onClick="enable_change(true)">
				</div>
			</td>
		</tr>
	</table>
</form>
<script language="JavaScript">
<!--
enable_change(false);
anonymousonly_change();
localusersonly_change();
tls_change();
//-->
</script>
<?php include("fend.inc");?>
