#!/usr/local/bin/php
<?php 
/*
	services_ftp.php
	part of FreeNAS (http://www.freenas.org)
	Copyright (C) 2005-2006 Olivier Cochard-Labbé <olivier@freenas.org>.
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

$pgtitle = array(_SERVICES, _SRVFTP_NAMEDESC);


if (!is_array($config['ftp']))
{
	$config['ftp'] = array();
	
}


$pconfig['enable'] = isset($config['ftp']['enable']);
$pconfig['port'] = $config['ftp']['port'];
$pconfig['numberclients'] = $config['ftp']['numberclients'];
$pconfig['maxconperip'] = $config['ftp']['maxconperip'];
$pconfig['timeout'] = $config['ftp']['timeout'];
$pconfig['anonymous'] = $config['ftp']['anonymous'];
$pconfig['pasv_max_port'] = $config['ftp']['pasv_max_port'];
$pconfig['pasv_min_port'] = $config['ftp']['pasv_min_port'];
$pconfig['pasv_address'] = $config['ftp']['pasv_address'];
$pconfig['banner'] = $config['ftp']['banner'];
$pconfig['localuser'] = $config['ftp']['localuser'];


if ($_POST) {

	unset($input_errors);
	$pconfig = $_POST;

	/* input validation */
	$reqdfields = array();
	$reqdfieldsn = array();
	
	if ($_POST['enable']) {
		$reqdfields = array_merge($reqdfields, explode(" ", "numberclients maxconperip timeout port"));
		$reqdfieldsn = array_merge($reqdfieldsn, explode(",", "Numberclients,Maxconperip,Timeout,Port"));
	}
	
	do_input_validation($_POST, $reqdfields, $reqdfieldsn, &$input_errors);
	
	if ($_POST['enable'] && !is_port($_POST['port']))
	{
		$input_errors[] = _SRVFTP_MSGVALIDPORT;
	}
	if ($_POST['enable'] && !is_numericint($_POST['numberclients'])) {
		$input_errors[] = _SRVFTP_MSGVALIDMAXCLIENT;
	}
	
	if ($_POST['enable'] && !is_numericint($_POST['maxconperip'])) {
		$input_errors[] = _SRVFTP_MSGVALIDMAXIP;
	}
	if ($_POST['enable'] && !is_numericint($_POST['timeout'])) {
		$input_errors[] = _SRVFTP_MSGVALIDIDLE;
	}
	
	if ($_POST['enable'] && ($_POST['pasv_address']))
	{
		if (!is_ipaddr($_POST['pasv_address']))
			$input_errors[] = _SRVFTP_MSGVALIDPASVIP;
	}
	
	if ($_POST['enable'] && ($_POST['pasv_max_port']))
	{
		if (!is_port($_POST['pasv_max_port']))
			$input_errors[] = _SRVFTP_MSGVALIDPASVMAX;
	}
	
	if ($_POST['enable'] && ($_POST['pasv_min_port']))
	{
		if (!is_port($_POST['pasv_min_port']))
			$input_errors[] = _SRVFTP_MSGVALIDPASVMIN;
	}
	
	if (!$input_errors)
	{
		$config['ftp']['numberclients'] = $_POST['numberclients'];	
		$config['ftp']['maxconperip'] = $_POST['maxconperip'];
		$config['ftp']['timeout'] = $_POST['timeout'];
		$config['ftp']['port'] = $_POST['port'];
		$config['ftp']['anonymous'] = $_POST['anonymous'];
		$config['ftp']['localuser'] = $_POST['localuser'];
		$config['ftp']['pasv_max_port'] = $_POST['pasv_max_port'];
		$config['ftp']['pasv_min_port'] = $_POST['pasv_min_port'];
		$config['ftp']['pasv_address'] = $_POST['pasv_address'];
		$config['ftp']['banner'] = $_POST['banner'];
		$config['ftp']['enable'] = $_POST['enable'] ? true : false;
		
		write_config();
		
		$retval = 0;
		if (!file_exists($d_sysrebootreqd_path)) {
			/* nuke the cache file */
			config_lock();
			//services_vsftpd_configure();
			services_pureftpd_configure();
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
	document.iform.port.disabled = endis;
	document.iform.timeout.disabled = endis;
	document.iform.numberclients.disabled = endis;
	document.iform.maxconperip.disabled = endis;
	document.iform.anonymous.disabled = endis;
	document.iform.localuser.disabled = endis;
	document.iform.banner.disabled = endis;
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
				  <tr><td class="optsect_s"><strong><?=_SRVFTP_FTPSERVER;?></strong></td>
				  <td align="right" class="optsect_s"><input name="enable" type="checkbox" value="yes" <?php if ($pconfig['enable']) echo "checked"; ?> onClick="enable_change(false)"> <strong><?=_ENABLE; ?></strong></td></tr>
				  </table></td>
                </tr>
                 <tr> 
                  <td width="22%" valign="top" class="vncellreq"><?=_SRVFTP_TCP; ?></td>
                  <td width="78%" class="vtable"> 
                    <?=$mandfldhtml;?><input name="port" type="text" class="formfld" id="port" size="20" value="<?=htmlspecialchars($pconfig['port']);?>"> 
                  </td>
				</tr>
                <tr> 
                  <td width="22%" valign="top" class="vncellreq"><?=_SRVFTP_MAXCLIENT; ?></td>
                  <td width="78%" class="vtable"> 
                    <?=$mandfldhtml;?><input name="numberclients" type="text" class="formfld" id="numberclients" size="20" value="<?=htmlspecialchars($pconfig['numberclients']);?>"> 
                  <br><?=_SRVFTP_MAXCLIENTTEXT; ?></td>
				</tr>
                 <tr> 
                  <td width="22%" valign="top" class="vncellreq"><?=_SRVFTP_MAXIP; ?></td>
                  <td width="78%" class="vtable"> 
                    <?=$mandfldhtml;?><input name="maxconperip" type="text" class="formfld" id="maxconperip" size="20" value="<?=htmlspecialchars($pconfig['maxconperip']);?>">
                    <br><?=_SRVFTP_MAXIPTEXT; ?></td>
				</tr>
                  <tr> 
                  <td width="22%" valign="top" class="vncellreq"><?=_SRVFTP_TIMEOUT ;?></td>
                  <td width="78%" class="vtable"> 
                    <?=$mandfldhtml;?><input name="timeout" type="text" class="formfld" id="timeout" size="20" value="<?=htmlspecialchars($pconfig['timeout']);?>">
                    <br><?=_SRVFTP_TIMEOUTTEXT ;?></td>
				</tr>
				  <td width="22%" valign="top" class="vncell"><?=_SRVFTP_ANONYMOUS; ?></td>
                  <td width="78%" class="vtable">
					<select name="anonymous" class="formfld" id="anonymous">
                      <?php $types = explode(",", "Yes,No");
					        $vals = explode(" ", "yes no");
					  $j = 0; for ($j = 0; $j < count($vals); $j++): ?>
                      <option value="<?=$vals[$j];?>" <?php if ($vals[$j] == $pconfig['anonymous']) echo "selected";?>> 
                      <?=htmlspecialchars($types[$j]);?>
                      </option>
                      <?php endfor; ?>
                    </select>
                    <br><?=_SRVFTP_ANONYMOUSTEXT; ?></td>
                  </tr>
                  <td width="22%" valign="top" class="vncell"><?=_SRVFTP_AUTH; ?></td>
                  <td width="78%" class="vtable">
					<select name="localuser" class="formfld" id="localuser">
                      <?php $types = explode(",", "Yes,No");
					        $vals = explode(" ", "yes no");
					  $j = 0; for ($j = 0; $j < count($vals); $j++): ?>
                      <option value="<?=$vals[$j];?>" <?php if ($vals[$j] == $pconfig['localuser']) echo "selected";?>> 
                      <?=htmlspecialchars($types[$j]);?>
                      </option>
                      <?php endfor; ?>
                    </select>
                    <br><?=_SRVFTP_AUTHTEXT; ?></td>
                    </tr>
                    <tr> 
                  <td width="22%" valign="top" class="vncell"><?=_SRVFTP_BANNER;?></td>
                  <td width="78%" class="vtable"> 
                    <textarea name="banner" cols="65" rows="7" id="banner" class="formpre"><?=htmlspecialchars($pconfig['banner']);?></textarea>
                    <br> 
                    <?=_SRVFTP_BANNERTEXT;?></td>
                </tr>
				<tr> 
                  <td width="22%" valign="top" class="vncell"><?=_SRVFTP_PASVMIN; ?></td>
                  <td width="78%" class="vtable"> 
                    <?=$mandfldhtml;?><input name="pasv_min_port" type="text" class="formfld" id="pasv_min_port" size="20" value="<?=htmlspecialchars($pconfig['pasv_min_port']);?>"> 
                  <br><?=_SRVFTP_PASVMINTEXT; ?></td>
				</tr>
				<tr> 
                  <td width="22%" valign="top" class="vncell"><?=_SRVFTP_PASVMAX; ?></td>
                  <td width="78%" class="vtable"> 
                    <?=$mandfldhtml;?><input name="pasv_max_port" type="text" class="formfld" id="pasv_max_port" size="20" value="<?=htmlspecialchars($pconfig['pasv_max_port']);?>"> 
                  <br><?=_SRVFTP_PASVMAXTEXT; ?></td>
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
