#!/usr/local/bin/php
<?php 
/*
	services_afp.php
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

$pgtitle = array(gettext("Services"),gettext("AFP"));

if (!is_array($config['afp'])) {
	$config['afp'] = array();	
}

$pconfig['enable'] = isset($config['afp']['enable']);
$pconfig['afpname'] = $config['afp']['afpname'];
$pconfig['guest'] = isset($config['afp']['guest']);
$pconfig['local'] = isset($config['afp']['local']);

if ($_POST) {
	unset($input_errors);
	$pconfig = $_POST;

	/* input validation */
	$reqdfields = array();
	$reqdfieldsn = array();

	if ($_POST['enable'] && !($_POST['guest'] || $_POST['local'])) {
		$input_errors[] = gettext("You must select at least one authentication method.");
	}

	if (!$input_errors) {
		$config['afp']['enable'] = $_POST['enable'] ? true : false;
		$config['afp']['guest'] = $_POST['guest'] ? true : false;
		$config['afp']['local'] = $_POST['local'] ? true : false;
		$config['afp']['afpname'] = $_POST['afpname'];
		
		write_config();
		
		$retval = 0;
		if (!file_exists($d_sysrebootreqd_path)) {
			config_lock();
			$retval |= rc_update_service("afpd");
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
	var endis;
	
	endis = !(document.iform.enable.checked || enable_change);
  document.iform.afpname.disabled = endis;
	document.iform.guest.disabled = endis;
	document.iform.local.disabled = endis;
}
//-->
</script>
<?php if ($input_errors) print_input_errors($input_errors); ?>
<?php if ($savemsg) print_info_box($savemsg); ?>
            <form action="services_afp.php" method="post" name="iform" id="iform">
              <table width="100%" border="0" cellpadding="6" cellspacing="0">
                <tr> 
                  <td colspan="2" valign="top" class="optsect_t">
				  <table border="0" cellspacing="0" cellpadding="0" width="100%">
				  <tr><td class="optsect_s"><strong><?=gettext("AFP Server");?></strong></td>
				  <td align="right" class="optsect_s"><input name="enable" type="checkbox" value="yes" <?php if ($pconfig['enable']) echo "checked"; ?> onClick="enable_change(false)"> <strong><?=gettext("Enable");?></strong></td></tr>
				  </table></td>
                </tr>
				<tr>
				 <tr> 
                  <td width="22%" valign="top" class="vncellreq"><?=gettext("Server Name") ;?></td>
                  <td width="78%" class="vtable"> 
                    <?=$mandfldhtml;?><input name="afpname" type="text" class="formfld" id="afpname" size="20" value="<?=htmlspecialchars($pconfig['afpname']);?>"> 
                  </td>
				</tr>
                  <tr>
                <td width="22%" valign="top" class="vncell"><strong><?=gettext("Authentication");?><strong></td>
                		<td width="78%" class="vtable">
                		<input name="guest" id="guest" type="checkbox" value="yes" <?php if ($pconfig['guest']) echo "checked"; ?>>
                		<?=gettext("Enable guest access.");?><br>
						<br>
						<input name="local" id="local" type="checkbox" value="yes" <?php if ($pconfig['local']) echo "checked"; ?>>
						<?=gettext("Enable local user authentication.");?><br>
						<br>
						</td>
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
