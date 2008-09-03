#!/usr/local/bin/php
<?php
/*
	services_websrv.php
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

$pgtitle = array(gettext("Services"),gettext("Webserver"));

if(!is_array($config['websrv']))
	$config['websrv'] = array();

if (!is_array($config['websrv']['authentication']['url']))
	$config['websrv']['authentication']['url'] = array();

array_sort_key($config['websrv']['authentication']['url'], "path");

$a_authurl = &$config['websrv']['authentication']['url'];

$pconfig['enable'] = isset($config['websrv']['enable']);
$pconfig['protocol'] = $config['websrv']['protocol'];
$pconfig['port'] = $config['websrv']['port'];
$pconfig['documentroot'] = $config['websrv']['documentroot'];
$pconfig['privatekey'] = base64_decode($config['websrv']['privatekey']);
$pconfig['certificate'] = base64_decode($config['websrv']['certificate']);
$pconfig['authentication'] = isset($config['websrv']['authentication']['enable']);
$pconfig['dirlisting'] = isset($config['websrv']['dirlisting']);

if ($_POST) {
	unset($input_errors);
	$pconfig = $_POST;

	// Input validation.
	if ($_POST['enable']) {
		$reqdfields = explode(" ", "port documentroot");
		$reqdfieldsn = array(gettext("Port"), gettext("Document root"));
		$reqdfieldst = explode(" ", "port string");

		if ("https" === $_POST['protocol']) {
			$reqdfields = array_merge($reqdfields, explode(" ", "certificate privatekey"));
			$reqdfieldsn = array_merge($reqdfieldsn, array(gettext("Certificate"), gettext("Private key")));
			$reqdfieldst = array_merge($reqdfieldst, explode(" ", "certificate privatekey"));
		}

		do_input_validation($_POST, $reqdfields, $reqdfieldsn, &$input_errors);
		do_input_validation_type($_POST, $reqdfields, $reqdfieldsn, $reqdfieldst, &$input_errors);

		// Check if port is already used.
		if (isset($config['system']['webgui']['port']) && ($config['system']['webgui']['port'] === $_POST['port'])) {
			$input_errors[] = gettext("Port is already used by WebGUI.");
		}
		if (!isset($config['system']['webgui']['port']) && ("80" === $_POST['port'])) {
			$input_errors[] = gettext("Port is already used by WebGUI.");
		}
	}

	if (!$input_errors) {
		$config['websrv']['enable'] = $_POST['enable'] ? true : false;
		$config['websrv']['protocol'] = $_POST['protocol'];
		$config['websrv']['port'] = $_POST['port'];
		$config['websrv']['documentroot'] = $_POST['documentroot'];
		$config['websrv']['privatekey'] = base64_encode($_POST['privatekey']);
		$config['websrv']['certificate'] = base64_encode($_POST['certificate']);
		$config['websrv']['authentication']['enable'] = $_POST['authentication'] ? true : false;
		$config['websrv']['dirlisting'] = $_POST['dirlisting'] ? true : false;

		write_config();

		$retval = 0;
		if (!file_exists($d_sysrebootreqd_path)) {
			config_lock();
			$retval |= rc_exec_service("websrv_htpasswd");
			$retval |= rc_update_service("websrv");
			config_unlock();
		}

		$savemsg = get_std_save_message($retval);

		if (0 == $retval) {
			if(file_exists($d_websrvconfdirty_path))
				unlink($d_websrvconfdirty_path);
		}
	}
}

if($_GET['act'] === "del") {
	unset($config['websrv']['authentication']['url'][$_GET['id']]);
	write_config();
	touch($d_websrvconfdirty_path);
	header("Location: services_websrv.php");
	exit;
}
?>
<?php include("fbegin.inc");?>
<script language="JavaScript">
<!--
function enable_change(enable_change) {
	var endis = !(document.iform.enable.checked || enable_change);
	document.iform.protocol.disabled = endis;
	document.iform.port.disabled = endis;
	document.iform.documentroot.disabled = endis;
	document.iform.privatekey.disabled = endis;
	document.iform.certificate.disabled = endis;
	document.iform.authentication.disabled = endis;
	document.iform.dirlisting.disabled = endis;
}

function protocol_change() {
	switch(document.iform.protocol.selectedIndex) {
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

function authentication_change() {
	switch(document.iform.authentication.checked) {
		case false:
			showElementById('authdirs_tr','hide');
			break;

		case true:
			showElementById('authdirs_tr','show');
			break;
	}
}
//-->
</script>
<form action="services_websrv.php" method="post" name="iform" id="iform">
	<table width="100%" border="0" cellpadding="0" cellspacing="0">
	  <tr>
	    <td class="tabcont">
	    	<?php if ($input_errors) print_input_errors($input_errors);?>
				<?php if ($savemsg) print_info_box($savemsg);?>
				<?php if (file_exists($d_websrvconfdirty_path)) print_config_change_box();?>
			  <table width="100%" border="0" cellpadding="6" cellspacing="0">
			    <tr>
			      <td colspan="2" valign="top" class="optsect_t">
			  		  <table border="0" cellspacing="0" cellpadding="0" width="100%">
			  		  <tr>
			          <td class="optsect_s"><strong><?=gettext("Webserver");?></strong></td>
			  			  <td align="right" class="optsect_s"><input name="enable" type="checkbox" value="yes" <?php if ($pconfig['enable']) echo "checked"; ?> onClick="enable_change(false)"> <strong><?=gettext("Enable");?></strong></td>
			        </tr>
			  		  </table>
			      </td>
			    </tr>
					<?php html_combobox("protocol", gettext("Protocol"), $pconfig['protocol'], array("http" => "HTTP", "https" => "HTTPS"), "", true, false, "protocol_change()");?>
					<?php html_inputbox("port", gettext("Port"), $pconfig['port'], gettext("TCP port to bind the server to."), true, 5);?>
					<?php html_filechooser("documentroot", gettext("Document root"), $pconfig['documentroot'], gettext("Document root of the webserver. Home of the web page files."), "/mnt", true, 60);?>
					<?php html_textarea("certificate", gettext("Certificate"), $pconfig['certificate'], gettext("Paste a signed certificate in X.509 PEM format here."), true, 65, 7);?>
					<?php html_textarea("privatekey", gettext("Private key"), $pconfig['privatekey'], gettext("Paste an private key in PEM format here."), true, 65, 7);?>
			    <?php html_checkbox("authentication", gettext("Authentication"), $pconfig['authentication'] ? true : false, gettext("Enable authentication."), gettext("Give only local users access to the web page."), false, "authentication_change()");?>
					<tr id="authdirs_tr">
						<td width="22%" valign="top" class="vncell">&nbsp;</td>
						<td width="78%" class="vtable">
							<table width="100%" border="0" cellpadding="0" cellspacing="0">
								<tr>
									<td width="45%" class="listhdrr"><?=gettext("URL");?></td>
									<td width="45%" class="listhdrr"><?=gettext("Realm");?></td>
									<td width="10%" class="list"></td>
								</tr>
								<?php $i = 0; foreach($a_authurl as $urlv):?>
								<tr>
									<td class="listlr"><?=htmlspecialchars($urlv['path']);?>&nbsp;</td>
									<td class="listr"><?=htmlspecialchars($urlv['realm']);?>&nbsp;</td>
									<td valign="middle" nowrap class="list">
										<?php if(isset($config['websrv']['enable'])):?>
										<a href="services_websrv_authurl.php?id=<?=$i;?>"><img src="e.gif" title="<?=gettext("Edit URL");?>" border="0"></a>&nbsp;
										<a href="services_websrv.php?act=del&id=<?=$i;?>" onclick="return confirm('<?=gettext("Do you really want to delete this URL?");?>')"><img src="x.gif" title="<?=gettext("Delete URL");?>" border="0"></a>
										<?php endif;?>
									</td>
								</tr>
								<?php $i++; endforeach;?>
								<tr>
									<td class="list" colspan="2"></td>
									<td class="list"><a href="services_websrv_authurl.php"><img src="plus.gif" title="<?=gettext("Add URL");?>" border="0"></a></td>
								</tr>
							</table>
							<span class="vexpl"><?=gettext("Define directories/URL's that require authentication.");?></span>
						</td>
					</tr>
					<?php html_checkbox("dirlisting", gettext("Directory listing"), $pconfig['dirlisting'] ? true : false, gettext("Enable directory listing."), gettext("A directory listing is generated if a directory is requested and no index-file (index.php, index.html, index.htm or default.htm) was found in that directory."), false);?>
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
protocol_change();
authentication_change();
//-->
</script>
<?php include("fend.inc");?>
