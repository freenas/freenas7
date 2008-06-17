#!/usr/local/bin/php
<?php
/*
	disks_zfs_zpool_edit.php
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

$pgtitle = array(gettext("Disks"), gettext("ZFS"), gettext("Pools"), gettext("Pool"), isset($id) ? gettext("Edit") : gettext("Add"));

if (!isset($config['zfs']['pools']) || !is_array($config['zfs']['pools']['pool']))
	$config['zfs']['pools']['pool'] = array();

if (!isset($config['zfs']['vdevices']) || !is_array($config['zfs']['vdevices']['vdevice']))
	$config['zfs']['vdevices']['vdevice'] = array();

array_sort_key($config['zfs']['pools']['pool'], "name");
array_sort_key($config['zfs']['vdevices']['vdevice'], "name");

$a_pool = &$config['zfs']['pools']['pool'];
$a_vdevice = &$config['zfs']['vdevices']['vdevice'];

if (!isset($id) && (!sizeof($a_vdevice))) {
	$errormsg = sprintf(gettext("No configured virtual devices. Please add new <a href=%s>virtual device</a> first."), "disks_zfs_zpool_vdevice.php");
}

if (isset($id) && $a_pool[$id]) {
	$pconfig['name'] = $a_pool[$id]['name'];
	$pconfig['vdevice'] = $a_pool[$id]['vdevice'];
	$pconfig['root'] = $a_pool[$id]['root'];
	$pconfig['mountpoint'] = $a_pool[$id]['mountpoint'];
	$pconfig['desc'] = $a_pool[$id]['desc'];	
} else {
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
	$reqdfieldst = explode(" ", "alias");

	do_input_validation($_POST, $reqdfields, $reqdfieldsn, &$input_errors);
	do_input_validation_type($_POST, $reqdfields, $reqdfieldsn, $reqdfieldst, &$input_errors);

	// Validate pool name
	if (($_POST['name'] && preg_match("/^\d/", $_POST['name']))) {
		$input_errors[] = gettext("The pool name can't start with 0-9.");
	}

	if (in_array($_POST['name'], array("disk", "file", "mirror", "raidz", "raidz1", "raidz2", "spare"))) {
		$input_errors[] = gettext("The pool name is prohibited.");
	}

	// Check for duplicate name
	if (!(isset($id) && $_POST['name'] === $a_pool[$id]['name'])) {
		if (false !== array_search_ex($_POST['name'], $a_pool, "name")) {
			$input_errors[] = gettext("This pool name already exists.");
		}
	}

	if (!$input_errors) {
		$pool = array();
		$pool['name'] = $_POST['name'];
		$pool['vdevice'] = $_POST['vdevice'];
		$pool['root'] = $_POST['root'];
		$pool['mountpoint'] = $_POST['mountpoint'];
		$pool['desc'] = $_POST['desc'];

		if (isset($id) && $a_pool[$id]) {
			$a_pool[$id] = $pool;
		} else {
			$a_pool[] = $pool;

			// Mark new added pool to be configured.
			file_put_contents($d_zpoolconfdirty_path, "{$pool[name]}\n", FILE_APPEND | FILE_TEXT);
		}

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
				<li class="tabact"><a href="disks_zfs_zpool.php" title="<?=gettext("Reload page");?>"><?=gettext("Pools");?></a></li>
				<li class="tabinact"><a href="disks_zfs_dataset.php"><?=gettext("Datasets");?></a></li>
			</ul>
		</td>
	</tr>
	<tr>
		<td class="tabnavtbl">
			<ul id="tabnav">
				<li class="tabinact"><a href="disks_zfs_zpool_vdevice.php"><?=gettext("Virtual device");?></a></li>
				<li class="tabact"><a href="disks_zfs_zpool.php" title="<?=gettext("Reload page");?>"><?=gettext("Pool");?></a></li>
				<li class="tabinact"><a href="disks_zfs_zpool_tools.php"><?=gettext("Tools");?></a></li>
				<li class="tabinact"><a href="disks_zfs_zpool_info.php"><?=gettext("Information");?></a></li>
				<li class="tabinact"><a href="disks_zfs_zpool_io.php"><?=gettext("IO statistics");?></a></li>
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
					<?php html_inputbox("name", gettext("Name"), $pconfig['name'], gettext(""), true, 20);?>
					<?php $a_device = array(); foreach ($a_vdevice as $vdevicev) { if (isset($id) && !(is_array($pconfig['vdevice']) && in_array($vdevicev['name'], $pconfig['vdevice']))) { continue; } if (!isset($id) && false !== array_search_ex($vdevicev['name'], $a_vdevice, "vdevice")) { continue; } $a_device[$vdevicev['name']] = htmlspecialchars("{$vdevicev['name']} ({$vdevicev['type']}, {$vdevicev['desc']})"); }?>
					<?php html_listbox("vdevice", gettext("Virtual devices"), $pconfig['vdevice'], $a_device, gettext(""), true);?>
					<?php html_inputbox("root", gettext("Root"), $pconfig['root'], gettext("Creates the pool with an alternate root."), false, 40);?>
					<?php html_inputbox("mountpoint", gettext("Mount point"), $pconfig['mountpoint'], gettext("Sets an alternate mount point for the root dataset. Default is /mnt."), false, 40);?>
					<?php html_inputbox("desc", gettext("Description"), $pconfig['desc'], gettext("You may enter a description here for your reference."), false, 40);?>
					<tr>
						<td width="22%" valign="top">&nbsp;</td>
						<td width="78%">
							<input name="Submit" type="submit" class="formbtn" value="<?=((isset($id) && $a_pool[$id])) ? gettext("Save") : gettext("Add");?>" onClick="enable_change(true)">
							<?php if (isset($id) && $a_pool[$id]):?>
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
