#!/usr/local/bin/php
<?php
/*
	services_iscsitarget.php
	part of FreeNAS (http://www.freenas.org)
	Copyright (C) 2005-2009 Olivier Cochard-Labbe <olivier@freenas.org>.
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

$pconfig['enable'] = isset($config['iscsitarget']['enable']);

if ($_POST) {
	$pconfig = $_POST;

	$config['iscsitarget']['enable'] = $_POST['enable'] ? true : false;

	write_config();

	$retval = 0;
	if (!file_exists($d_sysrebootreqd_path)) {
		$retval |= updatenotify_process("iscsitarget_extent", "iscsitargetextent_process_updatenotification");
		$retval |= updatenotify_process("iscsitarget_device", "iscsitargetdevice_process_updatenotification");
		$retval |= updatenotify_process("iscsitarget_target", "iscsitargettarget_process_updatenotification");
		config_lock();
		$retval |= rc_update_service("iscsi_target");
		config_unlock();
	}
	$savemsg = get_std_save_message($retval);
	if ($retval == 0) {
		updatenotify_delete("iscsitarget_extent");
		updatenotify_delete("iscsitarget_device");
		updatenotify_delete("iscsitarget_target");
	}
}

if (!is_array($config['iscsitarget']['extent']))
	$config['iscsitarget']['extent'] = array();

if (!is_array($config['iscsitarget']['device']))
	$config['iscsitarget']['device'] = array();

if (!is_array($config['iscsitarget']['target']))
	$config['iscsitarget']['target'] = array();

array_sort_key($config['iscsitarget']['extent'], "name");
array_sort_key($config['iscsitarget']['device'], "name");
array_sort_key($config['iscsitarget']['extent'], "name");

if ($_GET['act'] === "del") {
	switch ($_GET['type']) {
		case "extent":
			updatenotify_set("iscsitarget_extent", UPDATENOTIFY_MODE_DIRTY, $_GET['uuid']);
			break;

		case "device":
			updatenotify_set("iscsitarget_device", UPDATENOTIFY_MODE_DIRTY, $_GET['uuid']);
			break;

		case "target":
			updatenotify_set("iscsitarget_target", UPDATENOTIFY_MODE_DIRTY, $_GET['uuid']);
			break;
	}
	header("Location: services_iscsitarget.php");
	exit;
}

function iscsitargetextent_process_updatenotification($mode, $data) {
	global $config;

	$retval = 0;

	switch ($mode) {
		case UPDATENOTIFY_MODE_DIRTY:
			if (is_array($config['iscsitarget']['extent'])) {
				$index = array_search_ex($data, $config['iscsitarget']['extent'], "uuid");
				if (false !== $index) {
					unset($config['iscsitarget']['extent'][$index]);
					write_config();
				}
			}
			break;
	}

	return $retval;
}

function iscsitargetdevice_process_updatenotification($mode, $data) {
	global $config;

	$retval = 0;

	switch ($mode) {
		case UPDATENOTIFY_MODE_DIRTY:
			if (is_array($config['iscsitarget']['device'])) {
				$index = array_search_ex($data, $config['iscsitarget']['device'], "uuid");
				if (false !== $index) {
					unset($config['iscsitarget']['device'][$index]);
					write_config();
				}
			}
			break;
	}

	return $retval;
}

function iscsitargettarget_process_updatenotification($mode, $data) {
	global $config;

	$retval = 0;

	switch ($mode) {
		case UPDATENOTIFY_MODE_DIRTY:
			if (is_array($config['iscsitarget']['target'])) {
				$index = array_search_ex($data, $config['iscsitarget']['target'], "uuid");
				if (false !== $index) {
					unset($config['iscsitarget']['target'][$index]);
					write_config();
				}
			}
			break;
	}

	return $retval;
}
?>
<?php include("fbegin.inc");?>
<form action="services_iscsitarget.php" method="post" name="iform" id="iform">
	<table width="100%" border="0" cellpadding="0" cellspacing="0">
	  <tr>
	    <td class="tabcont">
			  <?php if ($savemsg) print_info_box($savemsg);?>
			  <?php if (updatenotify_exists("iscsitarget_extent") || updatenotify_exists("iscsitarget_device") || updatenotify_exists("iscsitarget_target")) print_config_change_box();?>
			  <table width="100%" border="0" cellpadding="6" cellspacing="0">
					<?php html_titleline_checkbox("enable", gettext("iSCSI Target"), $pconfig['enable'] ? true : false, gettext("Enable"), "enable_change(false)");?>
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
							  <?php foreach($config['iscsitarget']['extent'] as $extent):?>
							  <?php $notificationmode = updatenotify_get_mode("iscsitarget_extent", $extent['uuid']);?>
				        <tr>
				          <td class="listlr"><?=htmlspecialchars($extent['name']);?>&nbsp;</td>
									<td class="listr"><?php echo htmlspecialchars($extent['path']);?>&nbsp;</td>
									<td class="listr"><?=htmlspecialchars($extent['size']);?>MB&nbsp;</td>
									<?php if (UPDATENOTIFY_MODE_DIRTY != $notificationmode):?>
				          <td valign="middle" nowrap class="list">
				          	<a href="services_iscsitarget_extent_edit.php?uuid=<?=$extent['uuid'];?>"><img src="e.gif" title="<?=gettext("Edit extent");?>" border="0"></a>
				            <a href="services_iscsitarget.php?act=del&type=extent&uuid=<?=$extent['uuid'];?>" onclick="return confirm('<?=gettext("Do you really want to delete this extent?");?>')"><img src="x.gif" title="<?=gettext("Delete extent");?>" border="0"></a>
				          </td>
				          <?php else:?>
									<td valign="middle" nowrap class="list">
										<img src="del.gif" border="0">
									</td>
									<?php endif;?>
				        </tr>
				        <?php endforeach;?>
				        <tr>
				          <td class="list" colspan="3"></td>
				          <td class="list"><a href="services_iscsitarget_extent_edit.php"><img src="plus.gif" title="<?=gettext("Add extent");?>" border="0"></a></td>
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
							  <?php foreach($config['iscsitarget']['device'] as $device):?>
							  <?php $notificationmode = updatenotify_get_mode("iscsitarget_device", $device['uuid']);?>
				        <tr>
				          <td class="listlr"><?=htmlspecialchars($device['name']);?>&nbsp;</td>
				          <td class="listr"><?=htmlspecialchars($device['type']);?>&nbsp;</td>
									<td class="listr">
										<?php foreach($device['storage'] as $storage):?>
										<?=htmlspecialchars($storage);?>&nbsp;
										<?php endforeach;?>
										&nbsp;
									</td>
									<?php if (UPDATENOTIFY_MODE_DIRTY != $notificationmode):?>
				          <td valign="middle" nowrap class="list">
				          	<a href="services_iscsitarget_device_edit.php?uuid=<?=$device['uuid'];?>"><img src="e.gif" title="<?=gettext("Edit device");?>" border="0"></a>
				            <a href="services_iscsitarget.php?act=del&type=device&uuid=<?=$device['uuid'];?>" onclick="return confirm('<?=gettext("Do you really want to delete this device?");?>')"><img src="x.gif" title="<?=gettext("Delete device");?>" border="0"></a>
				          </td>
				          <?php else:?>
									<td valign="middle" nowrap class="list">
										<img src="del.gif" border="0">
									</td>
									<?php endif;?>
				        </tr>
				        <?php endforeach;?>
				        <tr>
				          <td class="list" colspan="3"></td>
				          <td class="list"><a href="services_iscsitarget_device_edit.php"><img src="plus.gif" title="<?=gettext("Add device");?>" border="0"></a></td>
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
							  <?php foreach($config['iscsitarget']['target'] as $target):?>
							  <?php $notificationmode = updatenotify_get_mode("iscsitarget_target", $target['uuid']);?>
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
				          <?php if (UPDATENOTIFY_MODE_DIRTY != $notificationmode):?>
				          <td valign="middle" nowrap class="list">
				          	<a href="services_iscsitarget_target_edit.php?uuid=<?=$target['uuid'];?>"><img src="e.gif" title="<?=gettext("Edit target");?>" border="0"></a>
				            <a href="services_iscsitarget.php?act=del&type=target&uuid=<?=$target['uuid'];?>" onclick="return confirm('<?=gettext("Do you really want to delete this target?");?>')"><img src="x.gif" title="<?=gettext("Delete target");?>" border="0"></a>
				          </td>
				          <?php else:?>
									<td valign="middle" nowrap class="list">
										<img src="del.gif" border="0">
									</td>
									<?php endif;?>
				        </tr>
				        <?php endforeach;?>
				        <tr>
				          <td class="list" colspan="4"></td>
				          <td class="list"><a href="services_iscsitarget_target_edit.php"><img src="plus.gif" title="<?=gettext("Add target");?>" border="0"></a></td>
						    </tr>
							</table>
							<?=gettext("At the highest level, a target is what is presented to the initiator, and is made up of one or more devices, and/or one or more extents.");?>
						</td>
					</tr>
				</table>
				<div id="submit">
					<input name="Submit" type="submit" class="formbtn" value="<?=gettext("Save and Restart");?>">
				</div>
				<div id="remarks">
					<?php html_remark("note", gettext("Note"), gettext("You must have a minimum of 256MB of RAM for using iSCSI target."));?>
				</div>
			</td>
		</tr>
	</table>
</form>
<?php include("fend.inc");?>
