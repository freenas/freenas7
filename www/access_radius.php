#!/usr/local/bin/php
<?php 
/*
	acces_radius_ftp.php
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

$pgtitle = array("Access", "RADIUS");
require("guiconfig.inc");


if (!is_array($config['radius']))
{
	$config['radius'] = array();
	
}

$pconfig['enable'] = isset($config['radius']['enable']);
$pconfig['port'] = $config['radius']['port'];
$pconfig['secret'] = $config['radius']['secret'];
$pconfig['radiusip'] = $config['radius']['radiusip'];
$pconfig['timeout'] = $config['radius']['timeout'];
$pconfig['maxretry'] = $config['radius']['maxretry'];


if ($_POST) {

	unset($input_errors);
	$pconfig = $_POST;

	/* input validation */
	$reqdfields = array();
	$reqdfieldsn = array();
	
	if ($_POST['enable']) {
		$reqdfields = array_merge($reqdfields, explode(" ", "secret radiusip port"));
		$reqdfieldsn = array_merge($reqdfieldsn, explode(",", "Secret,RadiusIP,Port"));
	}
	
	do_input_validation($_POST, $reqdfields, $reqdfieldsn, &$input_errors);
	
	if ($_POST['enable'] &&  !is_port($_POST['port']))
	{
		$input_errors[] = "The TCP port must be a valid port number.";
	}
	
	if ($_POST['enable'] && !is_ipaddr($_POST['radiusip'])){
  		$input_errors[] = "A valid IP address must be specified.";
  	}
	
	if (!$input_errors)
	{
		$config['radius']['port'] = $_POST['port'];	
		$config['radius']['secret'] = $_POST['secret'];
		$config['radius']['timeout'] = $_POST['timeout'];
		$config['radius']['radiusip'] = $_POST['radiusip'];
		$config['radius']['maxretry'] = $_POST['maxretry'];
		$config['radius']['enable'] = $_POST['enable'] ? true : false;
		
		write_config();
		
		$retval = 0;
		if (!file_exists($d_sysrebootreqd_path))
		{
			/* nuke the cache file */
			config_lock();
			/* services_ftpd_configure(); */
			services_radius_configure();
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
	var endis;
	
	endis = !(document.iform.enable.checked || enable_change);
	document.iform.port.disabled = endis;
	document.iform.timeout.disabled = endis;
	document.iform.secret.disabled = endis;
	document.iform.radiusip.disabled = endis;
	document.iform.maxretry.disabled = endis;
}
//-->
</script>
<?php if ($input_errors) print_input_errors($input_errors); ?>
<?php if ($savemsg) print_info_box($savemsg); ?>
            <form action="access_radius.php" method="post" name="iform" id="iform">
              <table width="100%" border="0" cellpadding="6" cellspacing="0">
                <tr> 
                  <td colspan="2" valign="top" class="optsect_t">
				  <table border="0" cellspacing="0" cellpadding="0" width="100%">
				  <tr><td class="optsect_s"><strong>Radius Authentication</strong></td>
				  <td align="right" class="optsect_s"><input name="enable" type="checkbox" value="yes" <?php if ($pconfig['enable']) echo "checked"; ?> onClick="enable_change(false)"> <strong>Enable</strong></td></tr>
				  </table></td>
                </tr>
                 <tr> 
                  <td width="22%" valign="top" class="vncellreq">Radius IP</td>
                  <td width="78%" class="vtable"> 
                    <?=$mandfldhtml;?><input name="radiusip" type="text" class="formfld" id="radiusip" size="20" value="<?=htmlspecialchars($pconfig['radiusip']);?>"> 
                  <br>IP address of radius server.</td>
				</tr>
                 <tr> 
                  <td width="22%" valign="top" class="vncellreq">TCP port</td>
                  <td width="78%" class="vtable"> 
                    <?=$mandfldhtml;?><input name="port" type="text" class="formfld" id="port" size="20" value="<?=htmlspecialchars($pconfig['port']);?>"> 
                  <br>Use 1812 for standard and 1645 for obsolete.</td>
				</tr>
                <tr> 
                  <td width="22%" valign="top" class="vncellreq">Shared secret</td>
                  <td width="78%" class="vtable"> 
                    <?=$mandfldhtml;?><input name="secret" type="text" class="formfld" id="secret" size="20" value="<?=htmlspecialchars($pconfig['secret']);?>"> 
                  <br>Shared password for radius server.</td>
				</tr>
                 <tr> 
                  <td width="22%" valign="top" class="vncell">Maximum retry</td>
                  <td width="78%" class="vtable"> 
                    <?=$mandfldhtml;?><input name="maxretry" type="text" class="formfld" id="maxretry" size="20" value="<?=htmlspecialchars($pconfig['maxretry']);?>">
                    <br>Maximum connection per IP adress.</td>
				</tr>
                  <tr> 
                  <td width="22%" valign="top" class="vncell">Timeout</td>
                  <td width="78%" class="vtable"> 
                    <?=$mandfldhtml;?><input name="timeout" type="text" class="formfld" id="timeout" size="20" value="<?=htmlspecialchars($pconfig['timeout']);?>">
                    <br>Maximum idle time in seconds.</td>
				</tr>
				<tr> 
                  <td width="22%" valign="top">&nbsp;</td>
                  <td width="78%"> 
                    <input name="Submit" type="submit" class="formbtn" value="<?=gettext("Save");?>" onClick="enable_change(true)"> 
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
