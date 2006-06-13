#!/usr/local/bin/php
<?php 
/*
	acces_nis.php
	part of FreeNAS (http://www.freenas.org)
	Copyright (C) 2005-2006 Olivier Cochard <olivier@freenas.org>.
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

$pgtitle = array("Access", "NIS");
require("guiconfig.inc");


if (!is_array($config['nis']))
{
	$config['nis'] = array();
	
}

$pconfig['enable'] = isset($config['nis']['enable']);
$pconfig['nis_domain'] = $config['nis']['nis_domain'];
$pconfig['nis_master_name'] = $config['nis']['nis_master_name'];
$pconfig['nis_slave_name'] = $config['nis']['nis_slave_name'];



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
	/*
	if ($_POST['enable'] &&  !is_port($_POST['port']))
	{
		$input_errors[] = "The TCP port must be a valid port number.";
	}
	
	if ($_POST['enable'] && !is_ipaddr($_POST['radiusip'])){
  		$input_errors[] = "A valid IP address must be specified.";
  	}
	*/
	if (!$input_errors)
	{
		$config['nis']['nis_domain'] = $_POST['nis_domain'];
		$config['nis']['nis_master_name'] = $_POST['nis_master_name'];
		$config['nis']['nis_slave_name'] = $_POST['nis_slave_name'];
		$config['nis']['enable'] = $_POST['enable'] ? true : false;
		
		write_config();
		
		$retval = 0;
		if (!file_exists($d_sysrebootreqd_path))
		{
			/* nuke the cache file */
			config_lock();
			services_nis_configure();
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
	document.iform.nis_domain.disabled = endis;
	document.iform.nis_master_name.disabled = endis;
	document.iform.nis_slave_name.disabled = endis;
}
//-->
</script>
<?php if ($input_errors) print_input_errors($input_errors); ?>
<?php if ($savemsg) print_info_box($savemsg); ?>
            <form action="access_nis.php" method="post" name="iform" id="iform">
              <table width="100%" border="0" cellpadding="6" cellspacing="0">
                <tr> 
                  <td colspan="2" valign="top" class="optsect_t">
				  <table border="0" cellspacing="0" cellpadding="0" width="100%">
				  <tr><td class="optsect_s"><strong>NIS Authentication</strong></td>
				  <td align="right" class="optsect_s"><input name="enable" type="checkbox" value="yes" <?php if ($pconfig['enable']) echo "checked"; ?> onClick="enable_change(false)"> <strong>Enable</strong></td></tr>
				  </table></td>
                </tr>
                 <tr> 
                  <td width="22%" valign="top" class="vncellreq">Domain</td>
                  <td width="78%" class="vtable"> 
                    <?=$mandfldhtml;?><input name="nis_domain" type="text" class="formfld" id="nis_domain" size="20" value="<?=htmlspecialchars($pconfig['nis_domain']);?>"> 
                  <br>Enter the NIS domain name.</td>
				</tr>
                 <tr> 
                  <td width="22%" valign="top" class="vncellreq">NIS master server name</td>
                  <td width="78%" class="vtable"> 
                    <?=$mandfldhtml;?><input name="nis_master_name" type="text" class="formfld" id="nis_master_name" size="20" value="<?=htmlspecialchars($pconfig['nis_master_name']);?>"> 
                  <br>Enter the NIS Master server hostname.</td>
				</tr>
                <tr> 
                  <td width="22%" valign="top" class="vncellreq">NIS slave server name</td>
                  <td width="78%" class="vtable"> 
                    <?=$mandfldhtml;?><input name="nis_slave_name" type="text" class="formfld" id="nis_slave_name" size="20" value="<?=htmlspecialchars($pconfig['nis_slave_name']);?>"> 
                  <br>Enter the NIS Slave server hostname.</td>
				</tr>
				<tr> 
                  <td width="22%" valign="top">&nbsp;</td>
                  <td width="78%"> 
                    <input name="Submit" type="submit" class="formbtn" value="Save" onClick="enable_change(true)"> 
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
