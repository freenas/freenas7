#!/usr/local/bin/php
<?php
/*
	disks_manage_edit.php
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

$id = $_GET['id'];
if (isset($_POST['id']))
	$id = $_POST['id'];

$pgtitle = array(gettext("Disks"),gettext("Management"),gettext("Disk"),isset($id)?gettext("Edit"):gettext("Add"));

// Get all physical disks including CDROM.
$a_phy_disk = array_merge((array)get_physical_disks_list(), (array)get_cdrom_list());

if (!is_array($config['disks']['disk']))
	$config['disks']['disk'] = array();

array_sort_key($config['disks']['disk'], "name");

$a_disk = &$config['disks']['disk'];

if (isset($id) && $a_disk[$id]) {
	$pconfig['uuid'] = $a_disk[$id]['uuid'];
	$pconfig['name'] = $a_disk[$id]['name'];
	$pconfig['harddiskstandby'] = $a_disk[$id]['harddiskstandby'];
	$pconfig['acoustic'] = $a_disk[$id]['acoustic'];
	$pconfig['fstype'] = $a_disk[$id]['fstype'];
	$pconfig['apm'] = $a_disk[$id]['apm'];
	$pconfig['transfermode'] = $a_disk[$id]['transfermode'];
	$pconfig['devicespecialfile'] = $a_disk[$id]['devicespecialfile'];
	$pconfig['smart'] = isset($a_disk[$id]['smart']);
	$pconfig['desc'] = $a_disk[$id]['desc'];
} else {
	$pconfig['uuid'] = uuid();
	$pconfig['name'] = "";
	$pconfig['transfermode'] = "auto";
	$pconfig['harddiskstandby'] = "0";
	$pconfig['apm'] = "0";
	$pconfig['acoustic'] = "0";
	$pconfig['fstype'] = "";
	$pconfig['smart'] = false;
	$pconfig['desc'] = "";
}

if ($_POST) {
	unset($input_errors);
	$pconfig = $_POST;

	// Input validation.
	foreach ($a_disk as $disk) {
		if (isset($id) && ($a_disk[$id]) && ($a_disk[$id] === $disk))
			continue;

		if ($disk['name'] == $_POST['name']) {
			$input_errors[] = gettext("This disk already exists in the disk list.");
			break;
		}
	}

	if (!$input_errors) {
		$devname = $_POST['name'];

		$disks = array();
		$disks['uuid'] = $_POST['uuid'];
		$disks['name'] = $devname;
		$disks['devicespecialfile'] = "/dev/{$devname}";
		$disks['harddiskstandby'] = $_POST['harddiskstandby'];
		$disks['acoustic'] = $_POST['acoustic'];
		if ($_POST['fstype']) $disks['fstype'] = $_POST['fstype'];
		$disks['apm'] = $_POST['apm'];
		$disks['transfermode'] = $_POST['transfermode'];
		$disks['type'] = $a_phy_disk[$devname]['type'];
		$disks['desc'] = (empty($_POST['desc'])) ? $a_phy_disk[$devname]['desc'] : $_POST['desc'];
		$disks['size'] = $a_phy_disk[$devname]['size'];
		$disks['smart'] = $_POST['smart'] ? true : false;

		if (isset($id) && $a_disk[$id]) {
			$a_disk[$id] = $disks;
			$mode = UPDATENOTIFY_MODE_MODIFIED;
		} else {
			$a_disk[] = $disks;
			$mode = UPDATENOTIFY_MODE_NEW;
		}

		updatenotify_set("device", $mode, $disks['uuid']);
		write_config();

		header("Location: disks_manage.php");
		exit;
	}
}
?>
<?php include("fbegin.inc");?>
<script language="JavaScript">
<!--
function enable_change(enable_change) {
	document.iform.name.disabled = !enable_change;
	document.iform.fstype.disabled = !enable_change;
}
// -->
</script>
<table width="100%" border="0" cellpadding="0" cellspacing="0">
	<tr>
		<td class="tabnavtbl">
			<ul id="tabnav">
				<li class="tabact"><a href="disks_manage.php" title="<?=gettext("Reload page");?>"><span><?=gettext("Management");?></span></a></li>
				<li class="tabinact"><a href="disks_manage_smart.php"><span><?=gettext("S.M.A.R.T.");?></span></a></li>
				<li class="tabinact"><a href="disks_manage_iscsi.php"><span><?=gettext("iSCSI Initiator");?></span></a></li>
			</ul>
		</td>
	</tr>
	<tr>
		<td class="tabcont">
			<form action="disks_manage_edit.php" method="post" name="iform" id="iform">
				<?php if ($input_errors) print_input_errors($input_errors); ?>
				<table width="100%" border="0" cellpadding="6" cellspacing="0">
					<tr>
						<td width="22%" valign="top" class="vncellreq"><?=gettext("Disk");?></td>
						<td width="78%" class="vtable">
							<select name="name" class="formfld" id="name">
								<?php foreach ($a_phy_disk as $diskk => $diskv):?>
								<?php // Do not display disks that are already configured. (Create mode);?>
								<?php if (!isset($id) && (false !== array_search_ex($diskk,$a_disk,"name"))) continue;?>
								<option value="<?=$diskk;?>" <?php if ($diskk == $pconfig['name']) echo "selected";?>><?php echo htmlspecialchars($diskk . ": " .$diskv['size'] . " (" . $diskv['desc'] . ")");?></option>
								<?php endforeach;?>
							</select>
					  </td>
					</tr>
					<?php html_inputbox("desc", gettext("Description"), $pconfig['desc'], gettext("You may enter a description here for your reference."), false, 40);?>
					<?php $options = array("auto" => "Auto", "PIO0" => "PIO0", "PIO1" => "PIO1", "PIO2" => "PIO2", "PIO3" => "PIO3", "PIO4" => "PIO4", "WDMA2" => "WDMA2", "UDMA2" => "UDMA-33", "UDMA4" => "UDMA-66", "UDMA5" => "UDMA-100", "UDMA6" => "UDMA-133");?>
					<?php html_combobox("transfermode", gettext("Transfer mode"), $pconfig['transfermode'], $options, gettext("This allows you to set the transfer mode for ATA/IDE hard drives."), false);?>
					<?php $options = array(0 => gettext("Always on")); foreach(array(5, 10, 20, 30, 60, 120, 180, 240, 300, 360) as $vsbtime) { $options[$vsbtime] = sprintf("%d %s", $vsbtime, gettext("minutes")); }?>
					<?php html_combobox("harddiskstandby", gettext("Hard disk standby time"), $pconfig['harddiskstandby'], $options, gettext("Puts the hard disk into standby mode when the selected amount of time after the last hard disk access has been elapsed."), false);?>
					<?php $options = array(0 => gettext("Disabled"), 1 => gettext("Level 1 - Minimum power usage with Standby (spindown)"), 64 => gettext("Level 64 - Intermediate power usage with Standby"), 127 => gettext("Level 127 - Intermediate power usage with Standby"), 128 => gettext("Level 128 - Minimum power usage without Standby (no spindown)"), 192 => gettext("Level 192 - Intermediate power usage without Standby"), 254 => gettext("Level 254 - Maximum performance, maximum power usage"));?>
					<?php html_combobox("apm", gettext("Advanced Power Management"), $pconfig['apm'], $options, gettext("This allows you to lower the power consumption of the drive, at the expense of performance."), false);?>
					<?php $options = array(0 => gettext("Disabled"), 1 => gettext("Minimum performance, Minimum acoustic output"), 64 => gettext("Medium acoustic output"), 127 => gettext("Maximum performance, maximum acoustic output"));?>
					<?php html_combobox("acoustic", gettext("Acoustic level"), $pconfig['acoustic'], $options, gettext("This allows you to set how loud the drive is while it's operating."), false);?>
					<?php html_checkbox("smart", gettext("S.M.A.R.T."), $pconfig['smart'] ? true : false, gettext("Activate S.M.A.R.T. monitoring for this device."), "", false);?>
					<?php $options = get_fstype_list();?>
					<?php html_combobox("fstype", gettext("Preformatted file system"), $pconfig['fstype'], $options, gettext("This allows you to set the file system for preformatted hard disks containing data.") . " " . sprintf(gettext("Leave '%s' for unformated disks and format them using <a href=%s>format</a> menu."), "Unformated", "disks_init.php"), false);?>
				</table>
				<div id="submit">
					<input name="Submit" type="submit" class="formbtn" value="<?=((isset($id) && $a_disk[$id]))?gettext("Save"):gettext("Add")?>" onClick="enable_change(true)">
					<input name="uuid" type="hidden" value="<?=$pconfig['uuid'];?>">
					<?php if (isset($id) && $a_disk[$id]): ?>
					<input name="id" type="hidden" value="<?=$id;?>">
					<?php endif; ?>
				</div>
			</form>
		</td>
	</tr>
</table>
<?php if (isset($id) && $a_disk[$id]):?>
<script language="JavaScript">
<!-- Disable controls that should not be modified anymore in edit mode. -->
enable_change(false);
</script>
<?php endif;?>
<?php include("fend.inc");?>
