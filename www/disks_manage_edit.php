#!/usr/local/bin/php
<?php
/*
	disks_manage_edit.php
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
	$pconfig['name'] = $a_disk[$id]['name'];
	$pconfig['harddiskstandby'] = $a_disk[$id]['harddiskstandby'];
	$pconfig['acoustic'] = $a_disk[$id]['acoustic'];
	$pconfig['fstype'] = $a_disk[$id]['fstype'];
	$pconfig['apm'] = $a_disk[$id]['apm'];
	$pconfig['transfermode'] = $a_disk[$id]['transfermode'];
	$pconfig['devicespecialfile'] = $a_disk[$id]['devicespecialfile'];
} else {
	$pconfig['name'] = "";
	$pconfig['transfermode'] = "auto";
	$pconfig['harddiskstandby'] = "0";
	$pconfig['apm'] = "0";
	$pconfig['acoustic'] = "0";
	$pconfig['fstype'] = "";
}

if ($_POST) {
	unset($input_errors);
	$pconfig = $_POST;

	/* check for name conflicts */
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
		$disks['name'] = $devname;
		$disks['devicespecialfile'] = "/dev/{$devname}";
		$disks['harddiskstandby'] = $_POST['harddiskstandby'];
		$disks['acoustic'] = $_POST['acoustic'];
		if ($_POST['fstype']) $disks['fstype'] = $_POST['fstype'];
		$disks['apm'] = $_POST['apm'];
		$disks['transfermode'] = $_POST['transfermode'];
		$disks['type'] = $a_phy_disk[$devname]['type'];
		$disks['desc'] = $a_phy_disk[$devname]['desc'];
		$disks['size'] = $a_phy_disk[$devname]['size'];

		if (isset($id) && $a_disk[$id])
			$a_disk[$id] = $disks;
		else
			$a_disk[] = $disks;

		touch($d_diskdirty_path);

		write_config();
		rc_exec_service("ataidle");

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
				<li class="tabact"><a href="disks_manage.php" style="color:black" title="<?=gettext("Reload page");?>"><?=gettext("Management");?></a></li>
				<li class="tabinact"><a href="disks_manage_iscsi.php"><?=gettext("iSCSI Initiator");?></a></li>
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
								<?php foreach ($a_phy_disk as $diskk => $diskv): ?>
								<?php // Do not display disks that are already configured. (Create mode);?>
								<?php if (!isset($id) && (false !== array_search_ex($diskk,$a_disk,"name"))) continue;?>
								<option value="<?=$diskk;?>" <?php if ($diskk == $pconfig['name']) echo "selected";?>><?php echo htmlspecialchars($diskk . ": " .$diskv['size'] . " (" . $diskv['desc'] . ")");?></option>
								<?php endforeach; ?>
							</select>
					  </td>
					</tr>
					<tr>
						<td width="22%" valign="top" class="vncell"><?=gettext("Transfer mode"); ?></td>
						<td width="78%" class="vtable">
							<select name="transfermode" class="formfld" id="transfermode">
							<?php $types = explode(",", "Auto,PIO0,PIO1,PIO2,PIO3,PIO4,WDMA2,UDMA-33,UDMA-66,UDMA-100,UDMA-133"); $vals = explode(" ", "auto PIO0 PIO1 PIO2 PIO3 PIO4 WDMA2 UDMA2 UDMA4 UDMA5 UDMA6");
							$j = 0; for ($j = 0; $j < count($vals); $j++): ?>
								<option value="<?=$vals[$j];?>" <?php if ($vals[$j] == $pconfig['transfermode']) echo "selected";?>><?=htmlspecialchars($types[$j]);?></option>
							<?php endfor; ?>
							</select>
							<br>
							<?=gettext("You can force PIO/UDMA mode if you have 'UDMA_ERROR.... LBA' message with your IDE/ATA hard drive."); ?>
						</td>
					</tr>
					<tr>
						<td width="22%" valign="top" class="vncell"><?=gettext("Hard disk standby time"); ?></td>
						<td width="78%" class="vtable">
							<select name="harddiskstandby" class="formfld">
							<?php $sbvals = array(0=>gettext("Always on"), 5=>"5 ".gettext("minutes"), 10=>"10 ".gettext("minutes"), 20=>"20 ".gettext("minutes"), 30=>"30 ".gettext("minutes"), 60=>"60 ".gettext("minutes"));?>
							<?php foreach ($sbvals as $sbval => $sbname): ?>
								<option value="<?=$sbval;?>" <?php if($pconfig['harddiskstandby'] == $sbval) echo 'selected';?>><?=htmlspecialchars($sbname);?></option>
							<?php endforeach; ?>
							</select>
							<br>
							<?=gettext("Puts the hard disk into standby mode when the selected amount of time after the last access has elapsed. <em>Do not set this for CF cards.</em>") ;?>
						</td>
					</tr>
					<tr>
						<td width="22%" valign="top" class="vncell"><?=gettext("Advanced Power Management"); ?></td>
						<td width="78%" class="vtable">
							<select name="apm" class="formfld">
							<?php $apmvals = array(0=>gettext("Disabled"),1=>gettext("Minimum power usage with Standby"),64=>gettext("Medium power usage with Standby"),128=>gettext("Minimum power usage without Standby"),192=>gettext("Medium power usage without Standby"),254=>gettext("Maximum performance, maximum power usage"));?>
							<?php foreach ($apmvals as $apmval => $apmname): ?>
								<option value="<?=$apmval;?>" <?php if($pconfig['apm'] == $apmval) echo 'selected';?>><?=htmlspecialchars($apmname);?></option>
							<?php endforeach; ?>
							</select>
							<br>
							<?=gettext("This allows  you  to lower the power consumption of the drive, at the expense of performance.<br><em>Do not set this for CF cards.</em>"); ?>
						</td>
					</tr>
					<tr>
						<td width="22%" valign="top" class="vncell"><?=gettext("Acoustic level"); ?></td>
						<td width="78%" class="vtable">
							<select name="acoustic" class="formfld">
							<?php $acvals = array(0=>gettext("Disabled"),1=>gettext("Minimum performance, Minimum acoustic output"),64=>gettext("Medium acoustic output"),127=>gettext("Maximum performance, maximum acoustic output"));?>
							<?php foreach ($acvals as $acval => $acname): ?>
								<option value="<?=$acval;?>" <?php if($pconfig['acoustic'] == $acval) echo 'selected';?>><?=htmlspecialchars($acname);?></option>
							<?php endforeach; ?>
							</select>
							<br>
							<?=gettext("This allows you to set how loud the drive is while it's operating.<br><em>Do not set this for CF cards.</em>"); ?>
						</td>
					</tr>
					<tr>
						<td width="22%" valign="top" class="vncell"><?=gettext("Preformatted FS"); ?></td>
						<td width="78%" class="vtable">
							<select name="fstype" class="formfld">
							<?php $fstlist = get_fstype_list(); ?>
							<?php foreach ($fstlist as $fstval => $fstname): ?>
								<option value="<?=$fstval;?>" <?php if($pconfig['fstype'] == $fstval) echo 'selected';?>><?=gettext($fstname);?></option>
							<?php endforeach; ?>
							</select>
							<br>
							<?php echo sprintf( gettext("This allows you to set FS type for preformated disk with data.<br><em>Leave 'unformated' for unformated disk and then use <a href=%s>format menu</a>.</em>"), "disks_init.php"); ?>
						</td>
					</tr>
					<tr>
						<td width="22%" valign="top">&nbsp;</td>
						<td width="78%"> <input name="Submit" type="submit" class="formbtn" value="<?=((isset($id) && $a_disk[$id]))?gettext("Save"):gettext("Add")?>" onClick="enable_change(true)">
						<?php if (isset($id) && $a_disk[$id]): ?>
							<input name="id" type="hidden" value="<?=$id;?>">
						<?php endif; ?>
						</td>
					</tr>
				</table>
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
