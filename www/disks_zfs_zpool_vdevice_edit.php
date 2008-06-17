#!/usr/local/bin/php
<?php
/*
	disks_zfs_zpool_vdevice_edit.php
	Copyright (c) 2008 Volker Theile (votdev@gmx.de)
	Copyright (c) 2008 Nelson Silva
	All rights reserved.

	part of FreeNAS (http://www.freenas.org)
	Copyright (C) 2005-2008 Olivier Cochard-Labbe <olivier@freenas.org>.
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

$pgtitle = array(gettext("Disks"), gettext("ZFS"), gettext("Pools"), gettext("Virtual device"), isset($id) ? gettext("Edit") : gettext("Add"));

if (!isset($config['zfs']['vdevices']) || !is_array($config['zfs']['vdevices']['vdevice']))
	$config['zfs']['vdevices']['vdevice'] = array();

array_sort_key($config['zfs']['vdevices']['vdevice'], "name");

$a_vdevice = &$config['zfs']['vdevices']['vdevice'];
$a_disk = get_conf_disks_filtered_ex("fstype", "zfs");

if (!isset($id) && (!sizeof($a_disk))) {
	$errormsg = sprintf(gettext("No disks available. Please add new <a href=%s>disk</a> first."), "disks_manage.php");
}

if (isset($id) && $a_vdevice[$id]) {
	$pconfig['name'] = $a_vdevice[$id]['name'];
	$pconfig['type'] = $a_vdevice[$id]['type'];
	$pconfig['device'] = $a_vdevice[$id]['device'];
	$pconfig['desc'] = $a_vdevice[$id]['desc'];
} else {
	$pconfig['name'] = "";
	$pconfig['type'] = "stripe";
	$pconfig['desc'] = "";
}

if ($_POST) {
	unset($input_errors);
	$pconfig = $_POST;

	// Input validation
	$reqdfields = explode(" ", "name type");
	$reqdfieldsn = array(gettext("Name"), gettext("Type"));
	$reqdfieldst = explode(" ", "alias string");

	do_input_validation($_POST, $reqdfields, $reqdfieldsn, &$input_errors);
	do_input_validation_type($_POST, $reqdfields, $reqdfieldsn, $reqdfieldst, &$input_errors);

	// Check for duplicate name.
	if(!(isset($id) && $_POST['name'] === $a_vdevice[$id]['name'])) {
		if (false !== array_search_ex($_POST['name'], $a_vdevice, "name")) {
			$input_errors[] = gettext("This virtual device name already exists.");
		}
	}

	switch($_POST['type']) {
		case "mirror": {
				if (count($_POST['device']) <  2) {
					$input_errors[] = gettext("There must be at least 2 disks in a mirror.");
				}
			}
			break;

		case "raidz":
		case "raidz1": {
				if (count($_POST['device']) <  2) {
					$input_errors[] = gettext("There must be at least 2 disks in a raidz.");
				}
			}
			break;

		case "raidz2":{
				if (count($_POST['device']) <  3) {
					$input_errors[] = gettext("There must be at least 3 disks in a raidz2.");
				}
			}
			break;

		default: {
				if (count($_POST['device']) <  1) {
					$input_errors[] = gettext("There must be at least 1 disks selected.");
				}
			}
			break;
	}

	if (!$input_errors) {
		$vdevice = array();
		$vdevice['name'] = $_POST['name'];
		$vdevice['type'] = $_POST['type'];
		$vdevice['device'] = $_POST['device'];
		$vdevice['desc'] = $_POST['desc'];

		if (isset($id) && $a_vdevice[$id])
			$a_vdevice[$id] = $vdevice;
		else
			$a_vdevice[] = $vdevice;

   	write_config();

		header("Location: disks_zfs_zpool_vdevice.php");
		exit;
	}
}
?>
<?php include("fbegin.inc");?>
<script language="JavaScript">
<!--
function enable_change(enable_change) {
	document.iform.name.disabled = !enable_change;
	document.iform.type.disabled = !enable_change;
	document.iform.device.disabled = !enable_change;
}
// -->
</script>
<table width="100%" border="0" cellpadding="0" cellspacing="0">
	<tr>
		<td class="tabnavtbl">
			<ul id="tabnav">
				<li class="tabact"><a href="disks_zfs_zpool.php" title="<?=gettext("Reload page");?>"><?=gettext("Pools");?></a></li>
				<li class="tabinact"><a href="disks_zfs_dataset.php"><?=gettext("Datasets");?></a></li>
			</ul>
		</td>
	</tr>
	<tr>
		<td class="tabnavtbl">
  		<ul id="tabnav">
				<li class="tabact"><a href="disks_zfs_zpool_vdevice.php" title="<?=gettext("Reload page");?>"><?=gettext("Virtual device");?></a></li>
				<li class="tabinact"><a href="disks_zfs_zpool.php"><?=gettext("Pool");?></a></li>
				<li class="tabinact"><a href="disks_zfs_zpool_tools.php"><?=gettext("Tools");?></a></li>
				<li class="tabinact"><a href="disks_zfs_zpool_info.php"><?=gettext("Information");?></a></li>
				<li class="tabinact"><a href="disks_zfs_zpool_io.php"><?=gettext("IO statistics");?></a></li>
  		</ul>
  	</td>
	</tr>
  <tr>
    <td class="tabcont">
			<form action="disks_zfs_zpool_vdevice_edit.php" method="post" name="iform" id="iform">
				<?php if ($errormsg) print_error_box($errormsg);?>
				<?php if ($input_errors) print_input_errors($input_errors);?>
				<?php if (file_exists($d_sysrebootreqd_path)) print_info_box(get_std_save_message(0));?>
			  <table width="100%" border="0" cellpadding="6" cellspacing="0">
			  	<?php html_inputbox("name", gettext("Name"), $pconfig['name'], gettext(""), true, 20, isset($id));?>
			  	<?php html_combobox("type", gettext("Type"), $pconfig['type'], array("stripe" => gettext("Stripe"), "mirror" => gettext("Mirror"), "raidz1" => gettext("Single-parity RAID-5"), "zraidz2" => gettext("Double-parity RAID-5"), "spare" => gettext("Hot Spare")), gettext(""), true, isset($id));?>
					<?php $a_device = array(); foreach ($a_disk as $diskv) { if (isset($id) && !(is_array($pconfig['device']) && in_array($diskv['devicespecialfile'], $pconfig['device']))) { continue; } if (!isset($id) && false !== array_search_ex($diskv['devicespecialfile'], $a_vdevice, "device")) { continue; } $a_device[$diskv['devicespecialfile']] = htmlspecialchars("{$diskv['name']} ({$diskv['size']}, {$diskv['desc']})"); }?>
			    <?php html_listbox("device", gettext("Devices"), $pconfig['device'], $a_device, gettext(""), true, isset($id));?>
			  	<?php html_inputbox("desc", gettext("Description"), $pconfig['desc'], gettext("You may enter a description here for your reference."), false, 40);?>
					<tr>
						<td width="22%" valign="top">&nbsp;</td>
						<td width="78%">
							<input name="Submit" type="submit" class="formbtn" value="<?=((isset($id) && $a_vdevice[$id])) ? gettext("Save") : gettext("Add");?>" onClick="enable_change(true)">
							<?php if (isset($id) && $a_vdevice[$id]):?>
							<input name="id" type="hidden" value="<?=$id;?>">
							<?php endif;?>
						</td>
					</tr>
			  </table>
			</form>
		</td>
	</tr>
</table>
<script language="JavaScript">
<!--
<?php if (isset($id) && $a_vdevice[$id]):?>
<!-- Disable controls that should not be modified anymore in edit mode. -->
enable_change(false);
<?php endif;?>
//-->
</script>
<?php include("fend.inc");?>
