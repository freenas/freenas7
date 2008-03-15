#!/usr/local/bin/php
<?php
/*
	services_iscsitarget.php
	part of FreeNAS (http://www.freenas.org)
	Copyright (C) 2005-2008 Olivier Cochard-Labbé <olivier@freenas.org>.
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

$pgtitle = array(gettext("Services"),gettext("iSCSI Target"));

if (!is_array($config['iscsitarget']['extent']))
	$config['iscsitarget']['extent'] = array();

if (!is_array($config['iscsitarget']['device']))
	$config['iscsitarget']['device'] = array();

if (!is_array($config['iscsitarget']['target']))
	$config['iscsitarget']['target'] = array();

array_sort_key($config['iscsitarget']['extent'], "name");
array_sort_key($config['iscsitarget']['device'], "name");
array_sort_key($config['iscsitarget']['extent'], "name");

$a_iscsitarget_extent = &$config['iscsitarget']['extent'];
$a_iscsitarget_device = &$config['iscsitarget']['device'];
$a_iscsitarget_target = &$config['iscsitarget']['target'];

$pconfig['enable'] = isset($config['iscsitarget']['enable']);

if ($_POST) {
	$pconfig = $_POST;

	$config['iscsitarget']['enable'] = $_POST['enable'] ? true : false;

	write_config();

	$retval = 0;
	if (!file_exists($d_sysrebootreqd_path)) {
		config_lock();
		$retval |= rc_update_service("iscsi_target");
		config_unlock();
	}

	$savemsg = get_std_save_message($retval);

	if ($retval == 0) {
		if (file_exists($d_iscsitargetdirty_path))
			unlink($d_iscsitargetdirty_path);
	}
}

if ($_GET['act'] == "del") {
	switch ($_GET['type']) {
		case "extent":
			if ($a_iscsitarget_extent[$_GET['id']])
				unset($a_iscsitarget_extent[$_GET['id']]);
			break;

		case "device":
			if ($a_iscsitarget_device[$_GET['id']])
				unset($a_iscsitarget_device[$_GET['id']]);
			break;

		case "target":
			if ($a_iscsitarget_target[$_GET['id']])
				unset($a_iscsitarget_target[$_GET['id']]);
			break;
	}

	write_config();
	touch($d_iscsitargetdirty_path);
	header("Location: services_iscsitarget.php");
	exit;
}
?>
<?php include("fbegin.inc");?>
<form action="services_iscsitarget.php" method="post">
	<table width="100%" border="0" cellpadding="0" cellspacing="0">
	  <tr>
	    <td class="tabcont">
			  <?php if ($savemsg) print_info_box($savemsg);?>
			  <?php if (file_exists($d_iscsitargetdirty_path)): ?><p>
			  <?php print_info_box_np(gettext("The iSCSI target list has been changed.<br>You must apply the changes in order for them to take effect."));?><br>
			  <input name="apply" type="submit" class="formbtn" id="apply" value="<?=gettext("Apply changes");?>"></p>
			  <?php endif;?>
			  <table width="100%" border="0" cellpadding="6" cellspacing="0">
			    <tr>
			      <td colspan="2" valign="top" class="optsect_t">
			        <table border="0" cellspacing="0" cellpadding="0" width="100%">
							  <tr>
			            <td class="optsect_s"><strong><?=gettext("iSCSI Target");?></strong></td>
							    <td align="right" class="optsect_s"><input name="enable" type="checkbox" value="yes" <?php if ($pconfig['enable']) echo "checked";?>"> <strong><?=gettext("Enable");?></strong></td>
			          </tr>
							</table>
			      </td>
			    </tr>
			    <tr>
			    	<td width="22%" valign="top" class="vncell"><?=gettext("Extent");?></td>
						<td width="78%" class="vtable">
				      <table width="100%" border="0" cellpadding="0" cellspacing="0">
				        <tr>
									<td width="20%" class="listhdrr"><?=gettext("Name");?></td>
									<td width="50%" class="listhdrr"><?=gettext("Path");?></td>
									<td width="20%" class="listhdrr"><?=gettext("Size");?></td>
									<td width="10%" class="list"></td>
				        </tr>
							  <?php $i = 0; foreach($a_iscsitarget_extent as $extent): ?>
				        <tr>
				          <td class="listlr"><?=htmlspecialchars($extent['name']);?>&nbsp;</td>
									<td class="listr"><?php echo htmlspecialchars($extent['path']);?>&nbsp;</td>
									<td class="listr"><?=htmlspecialchars($extent['size']);?>MB&nbsp;</td>
				          <td valign="middle" nowrap class="list">
				          	<a href="services_iscsitarget_extent_edit.php?id=<?=$i;?>"><img src="e.gif" title="<?=gettext("Edit extent");?>" width="17" height="17" border="0"></a>
				            <a href="services_iscsitarget.php?act=del&type=extent&id=<?=$i;?>" onclick="return confirm('<?=gettext("Do you really want to delete this extent?");?>')"><img src="x.gif" title="<?=gettext("Delete extent");?>" width="17" height="17" border="0"></a>
				          </td>
				        </tr>
				        <?php $i++; endforeach;?>
				        <tr>
				          <td class="list" colspan="3"></td>
				          <td class="list"><a href="services_iscsitarget_extent_edit.php"><img src="plus.gif" title="<?=gettext("Add extent");?>" width="17" height="17" border="0"></a></td>
						    </tr>
							</table>
							<?=gettext("Extents must be defined before they can be used, and extents cannot be used more than once.");?>
						</td>
					</tr>
			    <tr>
			    	<td width="22%" valign="top" class="vncell"><?=gettext("Device");?></td>
						<td width="78%" class="vtable">
				      <table width="100%" border="0" cellpadding="0" cellspacing="0">
				        <tr>
									<td width="20%" class="listhdrr"><?=gettext("Name");?></td>
									<td width="5%" class="listhdrr"><?=gettext("Type");?></td>
									<td width="65%" class="listhdrr"><?=gettext("Storage");?></td>
									<td width="10%" class="list"></td>
				        </tr>
							  <?php $i = 0; foreach($a_iscsitarget_device as $device):?>
				        <tr>
				          <td class="listlr"><?=htmlspecialchars($device['name']);?>&nbsp;</td>
				          <td class="listr"><?=htmlspecialchars($device['type']);?>&nbsp;</td>
									<td class="listr">
										<?php foreach($device['storage'] as $storage):?>
										<?=htmlspecialchars($storage);?>&nbsp;
										<?php endforeach;?>
										&nbsp;
									</td>
				          <td valign="middle" nowrap class="list">
				          	<a href="services_iscsitarget_device_edit.php?id=<?=$i;?>"><img src="e.gif" title="<?=gettext("Edit device");?>" width="17" height="17" border="0"></a>
				            <a href="services_iscsitarget.php?act=del&type=device&id=<?=$i;?>" onclick="return confirm('<?=gettext("Do you really want to delete this device?");?>')"><img src="x.gif" title="<?=gettext("Delete device");?>" width="17" height="17" border="0"></a>
				          </td>
				        </tr>
				        <?php $i++; endforeach;?>
				        <tr>
				          <td class="list" colspan="3"></td>
				          <td class="list"><a href="services_iscsitarget_device_edit.php"><img src="plus.gif" title="<?=gettext("Add device");?>" width="17" height="17" border="0"></a></td>
						    </tr>
							</table>
							<?=gettext("Devices are used to combine extents or other devices. Extents and devices must be defined before they can be used, and they cannot be used more than once.");?>
						</td>
					</tr>
			    <tr>
			    	<td width="22%" valign="top" class="vncell"><?=gettext("Target");?></td>
						<td width="78%" class="vtable">
				      <table width="100%" border="0" cellpadding="0" cellspacing="0">
				        <tr>
									<td width="40%" class="listhdrr"><?=gettext("Name");?></td>
									<td width="5%" class="listhdrr"><?=gettext("Flags");?></td>
									<td width="25%" class="listhdrr"><?=gettext("Storage");?></td>
									<td width="20%" class="listhdrr"><?=gettext("Network");?></td>
									<td width="10%" class="list"></td>
				        </tr>
							  <?php $i = 0; foreach($a_iscsitarget_target as $target): ?>
				        <tr>
									<td class="listlr">iqn.1994-04.org.netbsd.iscsi-target:<?=htmlspecialchars($target['name']);?>&nbsp;</td>
									<td class="listr"><?=htmlspecialchars($target['flags']);?>&nbsp;</td>
									<td class="listr">
										<?php foreach($target['storage'] as $storage):?>
										<?=htmlspecialchars($storage);?>&nbsp;
										<?php endforeach;?>
										&nbsp;
									</td>
				          <td class="listr"><?=htmlspecialchars($target['ipaddr'])."/".htmlspecialchars($target['subnet']);?>&nbsp;</td>
				          <td valign="middle" nowrap class="list">
				          	<a href="services_iscsitarget_target_edit.php?id=<?=$i;?>"><img src="e.gif" title="<?=gettext("Edit target");?>" width="17" height="17" border="0"></a>
				            <a href="services_iscsitarget.php?act=del&type=target&id=<?=$i;?>" onclick="return confirm('<?=gettext("Do you really want to delete this target?");?>')"><img src="x.gif" title="<?=gettext("Delete target");?>" width="17" height="17" border="0"></a>
				          </td>
				        </tr>
				        <?php $i++; endforeach;?>
				        <tr>
				          <td class="list" colspan="4"></td>
				          <td class="list"><a href="services_iscsitarget_target_edit.php"><img src="plus.gif" title="<?=gettext("Add target");?>" width="17" height="17" border="0"></a></td>
						    </tr>
							</table>
							<?=gettext("At the highest level, a target is what is presented to the initiator, and is made up of one or more devices, and/or one or more extents.");?>
						</td>
					</tr>
					<tr>
			      <td width="22%" valign="top">&nbsp;</td>
			      <td width="78%">
			        <input name="Submit" type="submit" class="formbtn" value="<?=gettext("Save and Restart");?>">
			      </td>
			    </tr>
					<tr>
						<td width="22%" valign="top">&nbsp;</td>
						<td width="78%">
							<span class="red"><strong><?=gettext("Note");?>:</strong></span><br>
							<?=gettext("You must have a minimum of 256MB of RAM for using iSCSI target.");?>
						</td>
					</tr>
				</table>
			</td>
		</tr>
	</table>
</form>
<?php include("fend.inc");?>
