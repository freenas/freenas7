#!/usr/local/bin/php
<?php
/*
	services_iscsitarget_extent_edit.php
	Copyright © 2007 Volker Theile (votdev@gmx.de)
  All rights reserved.

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

$id = $_GET['id'];
if (isset($_POST['id']))
	$id = $_POST['id'];

$pgtitle = array(gettext("Services"),gettext("iSCSI Target"),gettext("Device"),isset($id)?gettext("Edit"):gettext("Add"));

if (!is_array($config['iscsitarget']['extent']))
	$config['iscsitarget']['extent'] = array();

if (!is_array($config['iscsitarget']['device']))
	$config['iscsitarget']['device'] = array();

array_sort_key($config['iscsitarget']['extent'], "name");
array_sort_key($config['iscsitarget']['device'], "name");

$a_iscsitarget_extent = &$config['iscsitarget']['extent'];
$a_iscsitarget_device = &$config['iscsitarget']['device'];

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

// Check if the extent/device storage object is already used.
// Return true if it is used anywhere, otherwise false.
function iscsitarget_checkusage($name,$skipdevice = "") {
	global $config;

	$result = false;

	if (is_array($config['iscsitarget']['device'])) {
		foreach($config['iscsitarget']['device'] as $device) {
			if (!empty($skipdevice) && ($device['name'] === $skipdevice)) continue;
			if (is_array($device['storage'])) {
				foreach($device['storage'] as $storage) {
					if ($storage === $name) {
						$result = true;
						break;
					}
				}
			}
		}
	}

	return $result;
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
			  	<tr>
						<td width="22%" valign="top" class="vncellreq"><?=gettext("Device name");?></td>
						<td width="78%" class="vtable">
							<input name="name" type="text" class="formfld" id="name" size="10" value="<?=htmlspecialchars($pconfig['name']);?>" <?php if (isset($id)) echo "readonly";?>>
					  </td>
					</tr>
					<tr>
			    	<td width="22%" valign="top" class="vncellreq"><?=gettext("Type"); ?></td>
			      <td width="78%" class="vtable">
			  			<select name="type" class="formfld" id="type">
			          <?php $opts = array(gettext("RAID 0 (stripping)"), gettext("RAID 1 (mirroring)")); $vals = explode(" ", "RAID0 RAID1"); $i = 0;
								foreach ($opts as $opt): ?>
			          <option <?php if ($vals[$i] === $pconfig['type']) echo "selected";?> value="<?=$vals[$i++];?>"><?=htmlspecialchars($opt);?></option>
			          <?php endforeach; ?>
			        </select>
			      </td>
			    </tr>
					<tr>
			      <td width="22%" valign="top" class="vncellreq"><?=gettext("Storage");?></td>
			      <td width="78%" class="vtable">
				      <?php $i = 0; foreach ($a_iscsitarget_extent as $extent):?>
				      <?php if (true === iscsitarget_checkusage($extent['name'], $pconfig['name'])) continue;?>
							<input name="storage[]" id="<?=$i;?>" type="checkbox" value="<?=$extent['name'];?>" <?php if (is_array($pconfig['storage']) && in_array($extent['name'],$pconfig['storage'])) echo "checked";?>><?=htmlspecialchars($extent['name']);?><br>
				      <?php $i++; endforeach;?>
				      <?php $k = 0; foreach ($a_iscsitarget_device as $device):?>
				      <?php if ($device['name'] === $pconfig['name']) continue;?>
				      <?php if (true === iscsitarget_checkusage($device['name'])) continue;?>
							<input name="storage[]" id="<?=$k;?>" type="checkbox" value="<?=$device['name'];?>" <?php if (is_array($pconfig['storage']) && in_array($device['name'],$pconfig['storage'])) echo "checked";?>><?=htmlspecialchars($device['name']);?><br>
				      <?php $k++; endforeach;?>
				      <?php if ((0 == $i) && (0 == $k)):?>&nbsp;<?php endif;?>
				    </td>
			    </tr>
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
