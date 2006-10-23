#!/usr/local/bin/php
<?php 
/*
	services_sshd.php
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

require("guiconfig.inc");

$pgtitle = array(_SERVICES,_SRVSSHD_NAMEDESC);

if (!is_array($config['sshd'])) {
	$config['sshd'] = array();
}

$pconfig['readonly'] = $config['sshd']['readonly'];
$pconfig['port'] = $config['sshd']['port'];
$pconfig['permitrootlogin'] = isset($config['sshd']['permitrootlogin']);
$pconfig['enable'] = isset($config['sshd']['enable']);
$pconfig['key'] = base64_decode($config['sshd']['private-key']);

if ($_POST)
{
	unset($input_errors);
	$pconfig = $_POST;

	/* input validation */
	$reqdfields = array();
	$reqdfieldsn = array();
	if ($_POST['enable'])
	{
		$reqdfields = array_merge($reqdfields, explode(" ", "readonly"));
		$reqdfieldsn = array_merge($reqdfieldsn, explode(",", "Readonly"));
	}
	
	do_input_validation($_POST, $reqdfields, $reqdfieldsn, &$input_errors);
	
	if (($_POST['port']) && !is_port($_POST['port']))
	{
		$input_errors[] = _SRVSSHD_MSGVALIDTCPPORT;
	}
	
	if ($_POST['key']) {
		if (!strstr($_POST['key'], "BEGIN DSA PRIVATE KEY") || !strstr($_POST['key'], "END DSA PRIVATE KEY"))
			$input_errors[] = _SRVSSHD_MSGVALIDKEY;
	}
	
	if (!$input_errors)
	{
		$config['sshd']['readonly'] = $_POST['readonly'];	
		$config['sshd']['port'] = $_POST['port'];
		$config['sshd']['permitrootlogin'] = $_POST['permitrootlogin'] ? true : false;
		$config['sshd']['enable'] = $_POST['enable'] ? true : false;
		$config['sshd']['private-key'] = base64_encode($_POST['key']);
		
		write_config();
		
		$retval = 0;
		if (!file_exists($d_sysrebootreqd_path))
		{
			/* nuke the cache file */
			config_lock();
			services_sshd_configure();
			services_mdnsresponder_configure();
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
	document.iform.key.disabled = endis;
}
//-->
</script>
<?php if ($input_errors) print_input_errors($input_errors); ?>
<?php if ($savemsg) print_info_box($savemsg); ?>
            <form action="services_sshd.php" method="post" name="iform" id="iform">
              <table width="100%" border="0" cellpadding="6" cellspacing="0">
                <tr> 
                  <td colspan="2" valign="top" class="optsect_t">
				  <table border="0" cellspacing="0" cellpadding="0" width="100%">
				  <tr><td class="optsect_s"><strong><?=_SRVSSHD_SSHD;?></strong></td>
				  <td align="right" class="optsect_s"><input name="enable" type="checkbox" value="yes" <?php if ($pconfig['enable']) echo "checked"; ?> onClick="enable_change(false)"> <strong><?_ENABLE;?></strong></td></tr>
				  </table></td>
                </tr>
                <tr> 
                  <td width="22%" valign="top" class="vncellreq"><?=_SRVSSHD_READONLY;?></td>
                  <td width="78%" class="vtable">
					<select name="readonly" class="formfld" id="readonly">
                      <?php $types = array(_YES,_NO);
					        $vals = explode(" ", "yes no");
					  $j = 0; for ($j = 0; $j < count($vals); $j++): ?>
                      <option value="<?=$vals[$j];?>" <?php if ($vals[$j] == $pconfig['readonly']) echo "selected";?>> 
                      <?=htmlspecialchars($types[$j]);?>
                      </option>
                      <?php endfor; ?>
                    </select></td>
				        </tr>
                <tr> 
                  <td width="22%" valign="top" class="vncellreq"><?=_SRVSSHD_TCPORT;?></td>
                  <td width="78%" class="vtable"> 
                    <?=$mandfldhtml;?><input name="port" type="text" class="formfld" id="port" size="20" value="<?=htmlspecialchars($pconfig['port']);?>"> 
                     <br><?=_SRVSSHD_TCPORTTEXT;?></td>
                  </td>
				        </tr>
				        <tr> 
                  <td width="22%" valign="top" class="vncell"><?=_SRVSSHD_PERMITROOTLOGIN;?></td>
                  <td width="78%" class="vtable"> 
                    <input name="permitrootlogin" type="checkbox" id="permitrootlogin" value="yes" <?php if ($pconfig['permitrootlogin']) echo "checked"; ?>>
                    <?=_SRVSSHD_PERMITROOTLOGINTEXT;?>
                </tr>
                <tr> 
                  <td colspan="2" valign="top" class="listtopic"><?=_SRVSSHD_KEY;?></td>
                </tr>
                <tr> 
                  <td width="22%" valign="top" class="vncell"><?=_SRVSSHD_PRIVATEKEY;?></td>
                  <td width="78%" class="vtable"> 
                    <textarea name="key" cols="65" rows="7" id="key" class="formpre"><?=htmlspecialchars($pconfig['key']);?></textarea>
                    <br> 
                    <?=_SRVSSHD_PRIVATEKEYTEXT;?></td>
                </tr>
				        <tr> 
                  <td width="22%" valign="top">&nbsp;</td>
                  <td width="78%"> 
                    <input name="Submit" type="submit" class="formbtn" value="<?=_SAVE;?>" onClick="enable_change(true)"> 
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
