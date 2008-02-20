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

$pconfig['enable'] = isset($config['websrv']['enable']);
$pconfig['protocol'] = $config['websrv']['protocol'];
$pconfig['port'] = $config['websrv']['port'];
$pconfig['documentroot'] = $config['websrv']['documentroot'];
$pconfig['privatekey'] = $config['websrv']['privatekey'];
$pconfig['certificate'] = $config['websrv']['certificate'];

if($_POST) {
	unset($input_errors);

	$pconfig = $_POST;

	// Input validation.
	if($_POST['enable']) {
		$reqdfields = explode(" ", "port documentroot");
		$reqdfieldsn = array(gettext("Port"), gettext("Document root"));
		$reqdfieldst = explode(" ", "numeric string");

		if ("https" === $_POST['protocol']) {
			$reqdfields = array_merge($reqdfields, explode(" ", "privatekey certificate"));
			$reqdfieldsn = array_merge($reqdfieldsn, array(gettext("Private key"), gettext("Certificate")));
			$reqdfieldst = array_merge($reqdfieldst, explode(" ", "string string"));
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

	if(!$input_errors) {
		$config['websrv']['enable'] = $_POST['enable'] ? true : false;
		$config['websrv']['protocol'] = $_POST['protocol'];
		$config['websrv']['port'] = $_POST['port'];
		$config['websrv']['documentroot'] = $_POST['documentroot'];
		$config['websrv']['privatekey'] = $_POST['privatekey'];
		$config['websrv']['certificate'] = $_POST['certificate'];

		write_config();

		$retval = 0;
		if(!file_exists($d_sysrebootreqd_path)) {
			config_lock();
			$retval |= rc_update_service("websrv");
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
	document.iform.protocol.disabled = endis;
	document.iform.port.disabled = endis;
	document.iform.documentroot.disabled = endis;
	document.iform.privatekey.disabled = endis;
	document.iform.certificate.disabled = endis;
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
//-->
</script>
<form action="services_websrv.php" method="post" name="iform" id="iform">
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
			          <td class="optsect_s"><strong><?=gettext("Webserver");?></strong></td>
			  			  <td align="right" class="optsect_s"><input name="enable" type="checkbox" value="yes" <?php if ($pconfig['enable']) echo "checked"; ?> onClick="enable_change(false)"> <strong><?=gettext("Enable");?></strong></td>
			        </tr>
			  		  </table>
			      </td>
			    </tr>
			    <tr>
						<td width="22%" valign="top" class="vncellreq"><?=gettext("Protocol");?></td>
						<td width="78%" class="vtable">
							<select name="protocol" class="formfld" id="protocol" onchange="protocol_change()">
								<?php $types = array(gettext("HTTP"),gettext("HTTPS")); $vals = explode(" ", "http https");?>
								<?php $j = 0; for ($j = 0; $j < count($vals); $j++):?>
								<option value="<?=$vals[$j];?>" <?php if ($vals[$j] === $pconfig['protocol']) echo "selected";?>><?=htmlspecialchars($types[$j]);?></option>
								<?php endfor;?>
							</select>
						</td>
					</tr>
					<tr>
			      <td width="22%" valign="top" class="vncellreq"><?=gettext("Port");?></td>
			      <td width="78%" class="vtable">
			        <input name="port" type="text" class="formfld" id="port" size="5" value="<?=htmlspecialchars($pconfig['port']);?>"><br/>
							<span class="vexpl"><?=gettext("TCP port to bind the server to.");?></span>
			      </td>
			    </tr>
					<tr>
			      <td width="22%" valign="top" class="vncellreq"><?=gettext("Document root");?></td>
			      <td width="78%" class="vtable">
			        <input name="documentroot" type="text" class="formfld" id="documentroot" size="60" value="<?=htmlspecialchars($pconfig['documentroot']);?>">
			        <input name="browse" type="button" class="formbtn" id="Browse" onClick='ifield = form.documentroot; filechooser = window.open("filechooser.php?p="+escape(ifield.value)+"&sd=/mnt", "filechooser", "scrollbars=yes,toolbar=no,menubar=no,statusbar=no,width=550,height=300"); filechooser.ifield = ifield; window.ifield = ifield;' value="..." \><br/>
			        <span class="vexpl"><?=gettext("Document root of the webserver. Home of the web page files.");?></span>
			      </td>
			    </tr>
			    <tr id="privatekey_tr">
						<td width="22%" valign="top" class="vncellreq"><?=gettext("Private key");?></td>
						<td width="78%" class="vtable">
							<textarea name="privatekey" cols="65" rows="7" id="privatekey" class="formpre"><?=htmlspecialchars($pconfig['privatekey']);?></textarea></br>
							<span class="vexpl"><?=gettext("Paste an private key in PEM format here.");?></span>
						</td>
					</tr>  	
					<tr id="certificate_tr">
						<td width="22%" valign="top" class="vncellreq"><?=gettext("Certificate");?></td>
						<td width="78%" class="vtable">
							<textarea name="certificate" cols="65" rows="7" id="certificate" class="formpre"><?=htmlspecialchars($pconfig['certificate']);?></textarea></br>
							<span class="vexpl"><?=gettext("Paste a signed certificate in X.509 PEM format here.");?></span>
						</td>
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
protocol_change();
//-->
</script>
<?php include("fend.inc");?>
