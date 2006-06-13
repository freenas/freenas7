#!/usr/local/bin/php
<?php 
/*
	services_rsyncd.php
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

$pgtitle = array("Services", "rsyncd");
require("guiconfig.inc");

if (!is_array($config['rsync']))
{
	$config['rsync'] = array();
	
}

$pconfig['readonly'] = $config['rsyncd']['readonly'];
$pconfig['port'] = $config['rsyncd']['port'];
$pconfig['enable'] = isset($config['rsyncd']['enable']);


if ($_POST)
{

	unset($input_errors);
	$pconfig = $_POST;

	/* input validation */
	$reqdfields = array();
	$reqdfieldsn = array();
	if ($_POST['enable'])
	{
		$reqdfields = array_merge($reqdfields, explode(" ", "readonly port"));
		$reqdfieldsn = array_merge($reqdfieldsn, explode(",", "Readonly,Port"));
	}
	
	do_input_validation($_POST, $reqdfields, $reqdfieldsn, &$input_errors);
	
	if (!is_port($_POST['port']))
	{
		$input_errors[] = "The TCP port must be a valid port number.";
	}
	
	if (!$input_errors)
	{
		$config['rsyncd']['readonly'] = $_POST['readonly'];	
		$config['rsyncd']['port'] = $_POST['port'];
		$config['rsyncd']['enable'] = $_POST['enable'] ? true : false;
		
		write_config();
		
		$retval = 0;
		if (!file_exists($d_sysrebootreqd_path))
		{
			/* nuke the cache file */
			config_lock();
			services_rsyncd_configure();
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
	document.iform.readonly.disabled = endis;
	document.iform.port.disabled = endis;
	
}
//-->
</script>
<?php if ($input_errors) print_input_errors($input_errors); ?>
<?php if ($savemsg) print_info_box($savemsg); ?>
<table width="100%" border="0" cellpadding="0" cellspacing="0">
  <tr><td class="tabnavtbl">
  <ul id="tabnav">
	<li class="tabact"><a href="services_rsyncd.php" style="color:black" title="reload page">Server</a></li>
    <li class="tabinact"><a href="services_rsyncd_client.php">Client</a></li>
  </ul>
  </td></tr>
  <tr> 
    <td class="tabcont">
            <form action="services_rsyncd.php" method="post" name="iform" id="iform">
              <table width="100%" border="0" cellpadding="6" cellspacing="0">
                <tr> 
                  <td colspan="2" valign="top" class="optsect_t">
				  <table border="0" cellspacing="0" cellpadding="0" width="100%">
				  <tr><td class="optsect_s"><strong>Rsync Daemon</strong></td>
				  <td align="right" class="optsect_s"><input name="enable" type="checkbox" value="yes" <?php if ($pconfig['enable']) echo "checked"; ?> onClick="enable_change(false)"> <strong>Enable</strong></td></tr>
				  </table></td>
                </tr>
                <tr> 
                  <td width="22%" valign="top" class="vncellreq">Read only</td>
                  <td width="78%" class="vtable">
					<select name="readonly" class="formfld" id="readonly">
                      <?php $types = explode(",", "Yes,No");
					        $vals = explode(" ", "yes no");
					  $j = 0; for ($j = 0; $j < count($vals); $j++): ?>
                      <option value="<?=$vals[$j];?>" <?php if ($vals[$j] == $pconfig['readonly']) echo "selected";?>> 
                      <?=htmlspecialchars($types[$j]);?>
                      </option>
                      <?php endfor; ?>
                    </select></td>
				</tr>
                <tr> 
                  <td width="22%" valign="top" class="vncellreq">TCP Port</td>
                  <td width="78%" class="vtable"> 
                    <?=$mandfldhtml;?><input name="port" type="text" class="formfld" id="port" size="20" value="<?=htmlspecialchars($pconfig['port']);?>"> 
                     <br>Alternate TCP port.</td>
                  </td>
				</tr>
                            
				<tr> 
                  <td width="22%" valign="top">&nbsp;</td>
                  <td width="78%"> 
                    <input name="Submit" type="submit" class="formbtn" value="Save" onClick="enable_change(true)"> 
                  </td>
                </tr>
                </table>
</form>
	</td>
  </tr>
</table>
<script language="JavaScript">
<!--
enable_change(false);
//-->
</script>
<?php include("fend.inc"); ?>
