#!/usr/local/bin/php
<?php
/*
	services_upnp.php
	Copyright © 2006-2007 Volker Theile (votdev@gmx.de)
  All rights reserved.

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
require("guiconfig.inc");

$pgtitle = array(gettext("Services"),gettext("UPnP"));

if(!is_array($config['upnp']))
	$config['upnp'] = array();

if(!is_array($config['upnp']['content']))
	$config['upnp']['content'] = array();

sort($config['upnp']['content']);

$pconfig['enable'] = isset($config['upnp']['enable']);
$pconfig['name'] = $config['upnp']['name'];
$pconfig['if'] = $config['upnp']['if'];
$pconfig['port'] = $config['upnp']['port'];
$pconfig['profile'] = $config['upnp']['profile'];
$pconfig['web'] = isset($config['upnp']['web']);

/* Set name to configured hostname if it is not set */
if(!$pconfig['name'])
	$pconfig['name'] = $config['system']['hostname'];

if($_POST) {
	unset($input_errors);

	$pconfig = $_POST;

	/* input validation */
	if($_POST['enable']) {
		$reqdfields = explode(" ", "name interface");
		$reqdfieldsn = array(gettext("Name"), gettext("Interface"));

		do_input_validation($_POST, $reqdfields, $reqdfieldsn, &$input_errors);
	}

	if(!$input_errors) {
    $config['upnp']['enable'] = $_POST['enable'] ? true : false;
		$config['upnp']['name'] = $_POST['name'];
		$config['upnp']['if'] = $_POST['interface'];
		$config['upnp']['port'] = $_POST['port'];
		$config['upnp']['profile'] = $_POST['profile'];
		$config['upnp']['web'] = $_POST['web'] ? true : false;

		write_config();

		$retval = 0;
		if(!file_exists($d_sysrebootreqd_path)) {
			config_lock();
			$retval |= rc_update_service("ushare");
			$retval |= rc_update_service("mdnsresponder");
			config_unlock();
		}

		$savemsg = get_std_save_message($retval);

		if($retval == 0) {
			if(file_exists($d_upnpconfdirty_path))
				unlink($d_upnpconfdirty_path);
		}
	}
}

if($_GET['act'] == "del") {
	/* Remove entry from content list */
	$config['upnp']['content'] = array_diff($config['upnp']['content'],array($config['upnp']['content'][$_GET['id']]));
	write_config();
	touch($d_upnpconfdirty_path);
	header("Location: services_upnp.php");
	exit;
}

$a_interface = get_interface_list();
?>
<?php include("fbegin.inc");?>
<script language="JavaScript">
<!--
function enable_change(enable_change) {
	var endis = !(document.iform.enable.checked || enable_change);
	document.iform.name.disabled = endis;
	document.iform.interface.disabled = endis;
	document.iform.port.disabled = endis;
	document.iform.web.disabled = endis;
	document.iform.profile.disabled = endis;
}
//-->
</script>
<form action="services_upnp.php" method="post" name="iform" id="iform">
	<?php if ($input_errors) print_input_errors($input_errors); ?>
	<?php if ($savemsg) print_info_box($savemsg); ?>
	<?php if (file_exists($d_upnpconfdirty_path)): ?><p>
		<?php print_info_box_np(gettext("The content directory list has been changed.<br>You must apply the changes in order for them to take effect."));?><br>
		<input name="apply" type="submit" class="formbtn" id="apply" value="<?=gettext("Apply changes");?>"></p>
	<?php endif; ?>
  <table width="100%" border="0" cellpadding="6" cellspacing="0">
    <tr>
      <td colspan="2" valign="top" class="optsect_t">
  		  <table border="0" cellspacing="0" cellpadding="0" width="100%">
  		  <tr>
          <td class="optsect_s"><strong><?=gettext("UPnP A/V Media Server");?></strong></td>
  			  <td align="right" class="optsect_s"><input name="enable" type="checkbox" value="yes" <?php if ($pconfig['enable']) echo "checked"; ?> onClick="enable_change(false)"> <strong><?=gettext("Enable");?></strong></td>
        </tr>
  		  </table>
      </td>
    </tr>
    <tr>
      <td width="22%" valign="top" class="vncellreq"><?=gettext("Name");?></td>
      <td width="78%" class="vtable">
        <?=$mandfldhtml;?>
        <input name="name" type="text" class="formfld" id="name" size="20" value="<?=htmlspecialchars($pconfig['name']);?>">
        <br><?=gettext("UPnP friendly name.");?>
      </td>
    </tr>
    <tr>
      <td width="22%" valign="top" class="vncellreq"><?=gettext("Interface");?></td>
      <td width="78%" class="vtable">
        <?=$mandfldhtml;?>
        <select name="interface" class="formfld" id="interface">
          <?php foreach($a_interface as $if => $ifinfo): ?>
					<?php $ifinfo = get_interface_info($if); if($ifinfo['status'] == "up"): ?>
					<option value="<?=$if;?>"<?php if ($if == $pconfig['if']) echo "selected";?>><?=$if?></option>
					<?php endif; ?>
          <?php endforeach; ?>
        </select>
        <br><?=gettext("Interface to listen to.");?>
      </td>
    </tr>
    <tr>
      <td width="22%" valign="top" class="vncellreq"><?=gettext("Content");?></td>
      <td width="78%" class="vtable">
        <?=$mandfldhtml;?>
        <table width="100%" border="0" cellpadding="0" cellspacing="0">
          <tr>
            <td width="90%" class="listhdrr"><?=gettext("Directory");?></td>
            <td width="10%" class="list"></td>
          </tr>
					<?php if (is_array($config['upnp']['content'])):?>
					<?php $i = 0; foreach($config['upnp']['content'] as $contentv): ?>
					<tr>
						<td class="listlr"><?=htmlspecialchars($contentv);?> &nbsp;</td>
						<td valign="middle" nowrap class="list">
							<?php if(isset($config['upnp']['enable'])): ?>
							<a href="services_upnp_edit.php?id=<?=$i;?>"><img src="e.gif" title="<?=gettext("Edit directory");?>" width="17" height="17" border="0"></a>&nbsp;
							<a href="services_upnp.php?act=del&id=<?=$i;?>" onclick="return confirm('<?=gettext("Do you really want to delete this directory entry?");?>')"><img src="x.gif" title="<?=gettext("Delete directory"); ?>" width="17" height="17" border="0"></a>
							<?php endif; ?>
						</td>
					</tr>
					<?php $i++; endforeach; ?>
					<?php endif;?>
					<tr>
						<td class="list" colspan="1"></td>
						<td class="list">
							<a href="services_upnp_edit.php"><img src="plus.gif" title="<?=gettext("Add directory");?>" width="17" height="17" border="0"></a>
						</td>
					</tr>
				</table>
				<?=gettext("Directories to be shared.");?>
			</td>
		</tr>
		<tr>
			<td width="22%" valign="top" class="vncell"><?=gettext("Port");?></td>
			<td width="78%" class="vtable">
				<input name="port" type="text" class="formfld" id="port" size="20" value="<?=htmlspecialchars($pconfig['port']);?>">
				<br><?=gettext("Enter a custom port number for the HTTP server if you want to override the default (49152). Only dynamic or private ports can be used (from 49152 through 65535).");?>
			</td>
		</tr>
		<tr>
      <td width="22%" valign="top" class="vncell"><?=gettext("Profile"); ?></td>
      <td width="78%" class="vtable">
        <select name="profile" class="formfld" id="profile">
        <?php $types = array(gettext("Default"),gettext("XboX 360"),gettext("DLNA")); $vals = explode(" ", "default xbox dlna");?>
        <?php $j = 0; for ($j = 0; $j < count($vals); $j++): ?>
          <option value="<?=$vals[$j];?>" <?php if ($vals[$j] == $pconfig['profile']) echo "selected";?>>
          <?=htmlspecialchars($types[$j]);?>
          </option>
        <?php endfor; ?>
        </select>
        <br/><?=gettext("Compliant profile to be used.");?>
      </td>
    </tr>
		<tr>
			<td width="22%" valign="top" class="vncell"><?=gettext("Control web page");?></td>
			<td width="78%" class="vtable">
				<input name="web" type="checkbox" id="web" value="yes" <?php if ($pconfig['web']) echo "checked"; ?>>
				<?=gettext("Enable control web page.");?>
				<br><?=gettext("Accessible through 'http://ip_address:port/web/ushare.html'.");?>
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
<?php include("fend.inc");?>
