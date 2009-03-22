#!/usr/local/bin/php
<?php
/*
	disks_zfs_zpool_edit.php
	Copyright (c) 2008-2009 Volker Theile (votdev@gmx.de)
	Copyright (c) 2008 Nelson Silva
	All rights reserved.

	part of FreeNAS (http://www.freenas.org)
	Copyright (C) 2005-2009 Olivier Cochard-Labbe <olivier@freenas.org>.
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
require("zfs.inc");

$pool = $_GET['pool'];
if (isset($_POST['pool']))
	$pool = $_POST['pool'];

$pgtitle = array(gettext("Disks"), gettext("ZFS"), gettext("Pools"), gettext("Pool"), isset($pool) ? gettext("Edit") : gettext("Add"));

if (!isset($config['zfs']['pools']) || !is_array($config['zfs']['pools']['pool']))
	$config['zfs']['pools']['pool'] = array();

if (!isset($config['zfs']['vdevices']) || !is_array($config['zfs']['vdevices']['vdevice']))
	$config['zfs']['vdevices']['vdevice'] = array();

array_sort_key($config['zfs']['pools']['pool'], "name");
array_sort_key($config['zfs']['vdevices']['vdevice'], "name");

$a_pool = &$config['zfs']['pools']['pool'];
$a_vdevice = &$config['zfs']['vdevices']['vdevice'];

if (!isset($pool) && (!sizeof($a_vdevice))) {
	$errormsg = sprintf(gettext("No configured virtual devices. Please add new <a href=%s>virtual device</a> first."), "disks_zfs_zpool_vdevice.php");
}

if (isset($pool) && (false !== ($index = array_search_ex($pool, $a_pool, "name")))) {
	$pconfig['uuid'] = $a_pool[$index]['uuid'];
	$pconfig['name'] = $a_pool[$index]['name'];
	$pconfig['vdevice'] = $a_pool[$index]['vdevice'];
	$pconfig['root'] = $a_pool[$index]['root'];
	$pconfig['mountpoint'] = $a_pool[$index]['mountpoint'];
	$pconfig['desc'] = $a_pool[$index]['desc'];	
} else {
	$pconfig['uuid'] = uuid();
	$pconfig['name'] = "";
	$pconfig['root'] = "";
	$pconfig['mountpoint'] = "";
	$pconfig['desc'] = "";	
}

if ($_POST) {
	unset($input_errors);
	$pconfig = $_POST;

	// Input validation
	$reqdfields = explode(" ", "name");
	$reqdfieldsn = array(gettext("Name"));
	$reqdfieldst = explode(" ", "string");

	do_input_validation($_POST, $reqdfields, $reqdfieldsn, &$input_errors);
	do_input_validation_type($_POST, $reqdfields, $reqdfieldsn, $reqdfieldst, &$input_errors);

	// Validate pool name
	if (!zfs_is_valid_poolname($_POST['name'])) {
		$input_errors[] = gettext("The pool name does not match the naming rules.");
	}

	// Check for duplicate name
	if (!(isset($pool) && $_POST['name'] === $a_pool[$index]['name'])) {
		if (false !== array_search_ex($_POST['name'], $a_pool, "name")) {
			$input_errors[] = gettext("This pool name already exists.");
		}
	}

	if (!$input_errors) {
		$pooldata = array();
		$pooldata['uuid'] = $_POST['uuid'];
		$pooldata['name'] = $_POST['name'];
		$pooldata['vdevice'] = $_POST['vdevice'];
		$pooldata['root'] = $_POST['root'];
		$pooldata['mountpoint'] = $_POST['mountpoint'];
		$pooldata['desc'] = $_POST['desc'];

		if (isset($pool) && $a_pool[$index]) {
			$mode = UPDATENOTIFY_MODE_MODIFIED;
			$a_pool[$index] = $pooldata;
		} else {
			$mode = UPDATENOTIFY_MODE_NEW;
			$a_pool[] = $pooldata;
		}

		updatenotify_set("zfszpool", $mode, $pooldata['name']);
		write_config();

		header("Location: disks_zfs_zpool.php");
		exit;
	}
}
?>
<?php include("fbegin.inc");?>
<script language="JavaScript">
<!--
function enable_change(enable_change) {
	document.iform.name.disabled = !enable_change;
	document.iform.vdevice.disabled = !enable_change;
	document.iform.root.disabled = !enable_change;
	document.iform.mountpoint.disabled = !enable_change;
}
// -->
</script>
<table width="100%" border="0" cellpadding="0" cellspacing="0">
	<tr>
		<td class="tabnavtbl">
			<ul id="tabnav">
				<li class="tabact"><a href="disks_zfs_zpool.php" title="<?=gettext("Reload page");?>"><span><?=gettext("Pools");?></span></a></li>
				<li class="tabinact"><a href="disks_zfs_dataset.php"><span><?=gettext("Datasets");?></span></a></li>
			</ul>
		</td>
	</tr>
	<tr>
		<td class="tabnavtbl">
			<ul id="tabnav">
				<li class="tabinact"><a href="disks_zfs_zpool_vdevice.php"><span><?=gettext("Virtual device");?></span></a></li>
				<li class="tabact"><a href="disks_zfs_zpool.php" title="<?=gettext("Reload page");?>"><span><?=gettext("Pool");?></span></a></li>
				<li class="tabinact"><a href="disks_zfs_zpool_tools.php"><span><?=gettext("Tools");?></span></a></li>
				<li class="tabinact"><a href="disks_zfs_zpool_info.php"><span><?=gettext("Information");?></span></a></li>
				<li class="tabinact"><a href="disks_zfs_zpool_io.php"><span><?=gettext("I/O statistics");?></span></a></li>
			</ul>
		</td>
	</tr>
	<tr>
		<td class="tabcont">
			<form action="disks_zfs_zpool_edit.php" method="post" name="iform" id="iform">
				<?php if ($errormsg) print_error_box($errormsg);?>
				<?php if ($input_errors) print_input_errors($input_errors);?>
				<?php if (file_exists($d_sysrebootreqd_path)) print_info_box(get_std_save_message(0));?>
				<table width="100%" border="0" cellpadding="6" cellspacing="0">
					<?php html_inputbox("name", gettext("Name"), $pconfig['name'], "", true, 20);?>
					<?php $a_device = array(); foreach ($a_vdevice as $vdevicev) { if (isset($pool) && !(is_array($pconfig['vdevice']) && in_array($vdevicev['name'], $pconfig['vdevice']))) { continue; } if (!isset($pool) && false !== array_search_ex($vdevicev['name'], $a_vdevice, "vdevice")) { continue; } $a_device[$vdevicev['name']] = htmlspecialchars("{$vdevicev['name']} ({$vdevicev['type']}" . (!empty($vdevicev['desc']) ? ", {$vdevicev['desc']})" : ")")); }?>
					<?php html_listbox("vdevice", gettext("Virtual devices"), $pconfig['vdevice'], $a_device, "", true);?>
					<?php html_inputbox("root", gettext("Root"), $pconfig['root'], gettext("Creates the pool with an alternate root."), false, 40);?>
					<?php html_inputbox("mountpoint", gettext("Mount point"), $pconfig['mountpoint'], gettext("Sets an alternate mount point for the root dataset. Default is /mnt."), false, 40);?>
					<?php html_inputbox("desc", gettext("Description"), $pconfig['desc'], gettext("You may enter a description here for your reference."), false, 40);?>
				</table>
				<div id="submit">
					<input name="Submit" type="submit" class="formbtn" value="<?=((isset($pool) && $a_pool[$index])) ? gettext("Save") : gettext("Add");?>" onClick="enable_change(true)">
					<input name="uuid" type="hidden" value="<?=$pconfig['uuid'];?>">
					<?php if (isset($pool) && $a_pool[$index]):?>
					<input name="pool" type="hidden" value="<?=$pool;?>">
					<?php endif;?>
				</div>
			</form>
		</td>
	</tr>
</table>
<script language="JavaScript">
<!--
<?php if (isset($pool)):?>
<!-- Disable controls that should not be modified anymore in edit mode. -->
enable_change(false);
<?php endif;?>
//-->
</script>
<?php include("fend.inc");?>
