#!/usr/local/bin/php
<?php
/*
	services_iscsitarget_extent_edit.php
	Copyright © 2007-2008 Volker Theile (votdev@gmx.de)
  All rights reserved.

	part of FreeNAS (http://www.freenas.org)
	Copyright (C) 2005-2008 Olivier Cochard-Labbe <olivier@freenas.org>.
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

$pgtitle = array(gettext("Services"),gettext("iSCSI Target"),gettext("Device"),isset($id)?gettext("Edit"):gettext("Add"));

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

if (!sizeof($a_iscsitarget_extent)) {
	$errormsg = gettext("You have to define some 'Extent' objects first.");
}

if (isset($id) && $a_iscsitarget_device[$id]) {
	$pconfig['name'] = $a_iscsitarget_device[$id]['name'];
	$pconfig['type'] = $a_iscsitarget_device[$id]['type'];
	$pconfig['storage'] = $a_iscsitarget_device[$id]['storage'];
} else {
	// Find next unused ID.
	$deviceid = 0;
	$a_id = array();
	foreach($a_iscsitarget_device as $extent)
		$a_id[] = (int)str_replace("device", "", $extent['name']); // Extract ID.
	while (true === in_array($deviceid, $a_id))
		$deviceid += 1;

	$pconfig['name'] = "device{$deviceid}";
	$pconfig['type'] = "RAID0";
	$pconfig['storage'] = "";
}

if ($_POST) {
	unset($input_errors);
	unset($errormsg);

	$pconfig = $_POST;

	if (!$input_errors) {
		$iscsitarget_device = array();
		$iscsitarget_device['name'] = $_POST['name'];
		$iscsitarget_device['type'] = $_POST['type'];
		$iscsitarget_device['storage'] = $_POST['storage'];

		if (isset($id) && $a_iscsitarget_device[$id])
			$a_iscsitarget_device[$id] = $iscsitarget_device;
		else
			$a_iscsitarget_device[] = $iscsitarget_device;

		touch($d_iscsitargetdirty_path);

		write_config();

		header("Location: services_iscsitarget.php");
		exit;
	}
}
?>
<?php include("fbegin.inc");?>
<form action="services_iscsitarget_device_edit.php" method="post" name="iform" id="iform">
	<table width="100%" border="0" cellpadding="0" cellspacing="0">
	  <tr>
	    <td class="tabcont">
				<?php if ($errormsg) print_error_box($errormsg);?>
				<?php if ($input_errors) print_input_errors($input_errors);?>
			  <table width="100%" border="0" cellpadding="6" cellspacing="0">
					<?php html_inputbox("name", gettext("Device name"), $pconfig['name'], "", true, 10, isset($id));?>
					<?php html_combobox("type", gettext("Type"), $pconfig['type'], array("RAID0" => gettext("RAID 0 (stripping)"), "RAID1" => gettext("RAID 1 (mirroring)")), "", true);?>
					<?php
					$a_storage = array();
					// Check extents
					foreach ($a_iscsitarget_extent as $extentv) {
						// Add mode: Only display extents that are not already used in a target or device.
						if (!isset($id) && (false !== array_search_ex($extentv['name'], array_merge($a_iscsitarget_target, $a_iscsitarget_device), "storage"))) { continue; }
						// Edit mode:
						if (isset($id)) {
							// Check if extent is already used in another target.
							if (false !== array_search_ex($extentv['name'], $a_iscsitarget_target, "storage")) { continue; }
							// Check if extent is already used in another device. Verify that it isn't the current processed device.
							$index = array_search_ex($extentv['name'], $a_iscsitarget_device, "storage");
							if ((false !== $index) && ($a_iscsitarget_device[$index]['name'] !== $pconfig['name'])) { continue; }
						}
						$a_storage[$extentv['name']] = htmlspecialchars($extentv['name']);
					}
					// Check devices
					foreach ($a_iscsitarget_device as $devicev) {
						// Add mode: Only display devices that are not already used in a target or device.
						if (!isset($id) && false !== array_search_ex($devicev['name'], array_merge($a_iscsitarget_target, $a_iscsitarget_device), "storage")) { continue; }
						// Edit mode:
						if (isset($id)) {
							// Do not display the current device itself.
							if ($devicev['name'] === $pconfig['name']) { continue; }
							// Check if device is already used in another target.
							if (false !== array_search_ex($devicev['name'], $a_iscsitarget_target, "storage")) { continue; }
							// Check if device is already used in another device. Verify that it isn't the current processed device.
							$index = array_search_ex($devicev['name'], $a_iscsitarget_device, "storage");
							if ((false !== $index) && ($a_iscsitarget_device[$index]['name'] !== $pconfig['name'])) { continue; }
						}
						$a_storage[$devicev['name']] = htmlspecialchars($devicev['name']);
					}
					?>
					<?php html_listbox("storage", gettext("Storage"), $pconfig['storage'], $a_storage, "", true);?>
					<tr>
						<td width="22%" valign="top">&nbsp;</td>
						<td width="78%"><input name="Submit" type="submit" class="formbtn" value="<?=((isset($id) && $a_iscsitarget_device[$id]))?gettext("Save"):gettext("Add")?>">
							<?php if (isset($id) && $a_iscsitarget_device[$id]): ?>
							<input name="id" type="hidden" value="<?=$id;?>">
							<?php endif; ?>
						</td>
					</tr>
				</table>
			</td>
		</tr>
	</table>
</form>
<?php include("fend.inc");?>
