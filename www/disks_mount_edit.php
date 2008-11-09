#!/usr/local/bin/php
<?php
/*
	disks_mount_edit.php
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

$pgtitle = array(gettext("Disks"),gettext("Mount Point"),isset($id)?gettext("Edit"):gettext("Add"));

if (!is_array($config['mounts']['mount']))
	$config['mounts']['mount'] = array();

array_sort_key($config['mounts']['mount'], "devicespecialfile");

$a_mount = &$config['mounts']['mount'];

// Get list of all configured disks (physical and virtual).
$a_disk = get_conf_all_disks_list_filtered();

/* Load the cfdevice file*/
$filename=$g['varetc_path']."/cfdevice";
$cfdevice = trim(file_get_contents("$filename"));
$cfdevice = "/dev/" . $cfdevice;

if (isset($id) && $a_mount[$id]) {
	$pconfig['uuid'] = $a_mount[$id]['uuid'];
	$pconfig['type'] = $a_mount[$id]['type'];
	$pconfig['mdisk'] = $a_mount[$id]['mdisk'];
	$pconfig['partition'] = $a_mount[$id]['partition'];
	$pconfig['devicespecialfile'] = $a_mount[$id]['devicespecialfile'];
	$pconfig['fstype'] = $a_mount[$id]['fstype'];
	$pconfig['sharename'] = $a_mount[$id]['sharename'];
	$pconfig['desc'] = $a_mount[$id]['desc'];
	$pconfig['readonly'] = isset($a_mount[$id]['readonly']);
	$pconfig['fsck'] = isset($a_mount[$id]['fsck']);
	$pconfig['owner'] = $a_mount[$id]['accessrestrictions']['owner'];
	$pconfig['group'] = $a_mount[$id]['accessrestrictions']['group'][0];
	$pconfig['mode'] = $a_mount[$id]['accessrestrictions']['mode'];
	$pconfig['filename'] = $a_mount[$id]['filename'];
} else {
	$pconfig['uuid'] = uuid();
	$pconfig['type'] = "disk";
	$pconfig['partition'] = "p1";
	$pconfig['readonly'] = false;
	$pconfig['fsck'] = false;
	$pconfig['owner'] = "root";
	$pconfig['group'] = "wheel";
	$pconfig['mode'] = "0777";
}

initmodectrl($pconfig, $pconfig['mode']);

if ($_POST) {
	unset($input_errors);
	$pconfig = $_POST;

	/* input validation */
	switch($_POST['type']) {
		case "disk":
			$reqdfields = explode(" ", "mdisk partition fstype sharename");
			$reqdfieldsn = array(gettext("Disk"), gettext("Partition"), gettext("File system"), gettext("Name"));
			break;

		case "iso":
			$reqdfields = explode(" ", "filename sharename");
			$reqdfieldsn = array(gettext("Filename"), gettext("Name"));
			break;
	}

	do_input_validation($_POST, $reqdfields, $reqdfieldsn, &$input_errors);

	if (($_POST['sharename'] && !is_validsharename($_POST['sharename']))) {
		$input_errors[] = sprintf(gettext("The attribute '%s' may only consist of the characters a-z, A-Z, 0-9, _ , -."), gettext("Name"));
	}

	if (($_POST['desc'] && !is_validdesc($_POST['desc']))) {
		$input_errors[] = sprintf(gettext("The attribute '%s' contains invalid characters."), gettext("Description"));
	}

	// Do some 'disk' specific checks.
	if ("disk" === $_POST['type']) {
		if (($_POST['partition'] == "p1") && (($_POST['fstype'] == "msdosfs") || ($_POST['fstype'] == "cd9660") || ($_POST['fstype'] == "ntfs") || ($_POST['fstype'] == "ext2fs")))  {
			$input_errors[] = gettext("EFI/GPT partition can be use with UFS only.");
		}

		$device = "{$_POST['mdisk']}{$_POST['partition']}";
		if ($device === $cfdevice) {
			$input_errors[] = gettext("Can't mount the system partition 1, the DATA partition is the 2.");
		}
	}

	// Do some 'iso' specific checks.
	if ("iso" === $_POST['type']) {
		// Check if it is a valid ISO file.
		// 32769    string    CD001     ISO 9660 CD-ROM filesystem data
		// 37633    string    CD001     ISO 9660 CD-ROM filesystem data (raw 2352 byte sectors)
		// 32776    string    CDROM     High Sierra CD-ROM filesystem data
		$fp = fopen($_POST['filename'], 'r');
		fseek($fp, 32769, SEEK_SET);
		$identifier[] = fgets($fp, 6);
		fseek($fp, 37633, SEEK_SET);
		$identifier[] = fgets($fp, 6);
		fseek($fp, 32776, SEEK_SET);
		$identifier[] = fgets($fp, 6);
		fclose($fp);

		if (false === array_search('CD001', $identifier) && false === array_search('CDROM', $identifier)) {
			$input_errors[] = gettext("Selected file isn't an valid ISO file.");
		}
	}

	/* Check for name conflicts. */
	foreach ($a_mount as $mount) {
		if (isset($id) && ($a_mount[$id]) && ($a_mount[$id] === $mount))
			continue;

		if ("disk" === $_POST['type']) {
			// Check for duplicate mount point
			if (($mount['mdisk'] === $_POST['mdisk']) && ($mount['partition'] === $_POST['partition'])) {
				$input_errors[] = gettext("This disk/partition is already configured.");
				break;
			}
		}

		if (($_POST['sharename']) && ($mount['sharename'] === $_POST['sharename'])) {
			$input_errors[] = gettext("Duplicate share name.");
			break;
		}
	}

	if (!$input_errors) {
		$mount = array();
		$mount['uuid'] = $_POST['uuid'];
		$mount['type'] = $_POST['type'];

		switch($_POST['type']) {
			case "disk":
				$mount['mdisk'] = $_POST['mdisk'];
				$mount['partition'] = $_POST['partition'];
				$mount['fstype'] = $_POST['fstype'];
				$mount['devicespecialfile'] = trim("{$mount['mdisk']}{$mount['partition']}");
				$mount['readonly'] = $_POST['readonly'] ? true : false;
				$mount['fsck'] = $_POST['fsck'] ? true : false;
				break;

			case "iso":
				$mount['filename'] = $_POST['filename'];
				$mount['fstype'] = "cd9660";
				break;
		}

		$mount['sharename'] = $_POST['sharename'];
		$mount['desc'] = $_POST['desc'];
		$mount['accessrestrictions']['owner'] = $_POST['owner'];
		$mount['accessrestrictions']['group'] = $_POST['group'];
		$mount['accessrestrictions']['mode'] = getmodectrl($pconfig['mode_owner'], $pconfig['mode_group'], $pconfig['mode_others']);

		if (isset($id) && $a_mount[$id]) {
			$mode = UPDATENOTIFICATION_MODE_MODIFIED;
			$a_mount[$id] = $mount;
		} else {
			$mode = UPDATENOTIFICATION_MODE_NEW;
			$a_mount[] = $mount;
		}

		ui_set_updatenotification("mountpoint", $mode, $mount['uuid']);
		write_config();

		header("Location: disks_mount.php");
		exit;
	}
}

function initmodectrl(&$pconfig, $mode) {
	$pconfig['mode_owner'] = array();
	$pconfig['mode_group'] = array();
	$pconfig['mode_others'] = array();

	// Convert octal to decimal 
	$mode = octdec($mode);

	// Owner
	if ($mode & 0x0100) $pconfig['mode_owner'][] = "r"; //Read
	if ($mode & 0x0080) $pconfig['mode_owner'][] = "w"; //Write
	if ($mode & 0x0040) $pconfig['mode_owner'][] = "x"; //Execute

	// Group
	if ($mode & 0x0020) $pconfig['mode_group'][] = "r"; //Read
	if ($mode & 0x0010) $pconfig['mode_group'][] = "w"; //Write
	if ($mode & 0x0008) $pconfig['mode_group'][] = "x"; //Execute

	// Others
	if ($mode & 0x0004) $pconfig['mode_others'][] = "r"; //Read
	if ($mode & 0x0002) $pconfig['mode_others'][] = "w"; //Write
	if ($mode & 0x0001) $pconfig['mode_others'][] = "x"; //Execute
}

function getmodectrl($owner, $group, $others) {
		$mode = "";
		$legal = array("r", "w", "x");

		foreach ($legal as $value) {
			$mode .= (is_array($owner) && in_array($value, $owner)) ? $value : "-";
		}
		foreach ($legal as $value) {
			$mode .= (is_array($group) && in_array($value, $group)) ? $value : "-";
		}
		foreach ($legal as $value) {
			$mode .= (is_array($others) && in_array($value, $others)) ? $value : "-";
		}

    $realmode = "";
    $legal = array("", "w", "r", "x", "-");
    $attarray = preg_split("//",$mode);

    for ($i=0; $i<count($attarray); $i++) {
        if ($key = array_search($attarray[$i], $legal)) {
            $realmode .= $legal[$key];
        }
    }

    $mode = str_pad($realmode, 9, '-');
    $trans = array('-'=>'0', 'r'=>'4', 'w'=>'2', 'x'=>'1');
    $mode = strtr($mode, $trans);
    $newmode = "0";
    $newmode .= $mode[0]+$mode[1]+$mode[2];
    $newmode .= $mode[3]+$mode[4]+$mode[5];
    $newmode .= $mode[6]+$mode[7]+$mode[8];

    return $newmode;
}
?>
<?php include("fbegin.inc");?>
<script language="JavaScript">
<!--
function type_change() {
  switch(document.iform.type.selectedIndex) {
    case 0: /* Disk */
      showElementById('mdisk_tr','show');
      showElementById('partition_tr','show');
      showElementById('fstype_tr','show');
      showElementById('filename_tr','hide');
      showElementById('readonly_tr','show');
      showElementById('fsck_tr','show');
      break;

    case 1: /* ISO */
      showElementById('mdisk_tr','hide');
      showElementById('partition_tr','hide');
      showElementById('fstype_tr','hide');
      showElementById('filename_tr','show');
      showElementById('readonly_tr','hide');
      showElementById('fsck_tr','hide');
      break;
  }
}

function fstype_change() {
	switch(document.iform.fstype.selectedIndex) {
		case 0: /* UFS */
			document.iform.partition.value = "p1";
			break;

		case 2: /* CD/DVD */
			document.iform.partition.value = " ";
			break;
  }
}

function enable_change(enable_change) {
	document.iform.type.disabled = !enable_change;
	document.iform.mdisk.disabled = !enable_change;
	document.iform.filename.disabled = !enable_change;
}
// -->
</script>
<table width="100%" border="0" cellpadding="0" cellspacing="0">
  <tr>
    <td class="tabnavtbl">
      <ul id="tabnav">
        <li class="tabact"><a href="disks_mount.php" title="<?=gettext("Reload page");?>"><span><?=gettext("Management");?></span></a></li>
        <li class="tabinact"><a href="disks_mount_tools.php"><span><?=gettext("Tools");?></span></a></li>
        <li class="tabinact"><a href="disks_mount_fsck.php"><span><?=gettext("Fsck");?></span></a></li>
      </ul>
    </td>
  </tr>
  <tr>
    <td class="tabcont">
			<form action="disks_mount_edit.php" method="post" name="iform" id="iform">
				<?php if ($input_errors) print_input_errors($input_errors);?>
			  <table width="100%" border="0" cellpadding="6" cellspacing="0">
					<?php html_titleline(gettext("Settings"));?>
					<?php html_combobox("type", gettext("Type"), $pconfig['type'], array("disk" => "Disk", "iso" => "ISO"), "", true, false, "type_change()");?>
					<tr id="mdisk_tr">
			      <td width="22%" valign="top" class="vncellreq"><?=gettext("Disk");?></td>
			      <td class="vtable">
							<select name="mdisk" class="formfld" id="mdisk">
								<option value=""><?=gettext("Must choose one");?></option>
								<?php foreach ($a_disk as $diskv):?>
								<option value="<?=$diskv['devicespecialfile'];?>" <?php if ($pconfig['mdisk'] === $diskv['devicespecialfile']) echo "selected";?>>
								<?php $diskinfo = disks_get_diskinfo($diskv['devicespecialfile']); echo htmlspecialchars("{$diskv['name']}: {$diskinfo['mediasize_mbytes']}MB ({$diskv['desc']})");?>
								</option>
								<?php endforeach;?>
							</select>
			      </td>
			    </tr>
					<tr id="partition_tr">
			      <td width="22%" valign="top" class="vncellreq"><?=gettext("Partition");?></td>
			      <td class="vtable">
							<select name="partition" class="formfld" id="partition">
								<option value="p1" <?php if ($pconfig['partition'] === "p1") echo "selected";?>>EFI GPT</option>
								<option value="s1" <?php if ($pconfig['partition'] === "s1") echo "selected";?>>1</option>
								<option value="s2" <?php if ($pconfig['partition'] === "s2") echo "selected";?>>2</option>
								<option value="s3" <?php if ($pconfig['partition'] === "s3") echo "selected";?>>3</option>
								<option value="s4" <?php if ($pconfig['partition'] === "s4") echo "selected";?>>4</option>
								<option value="s5" <?php if ($pconfig['partition'] === "s5") echo "selected";?>>5</option>
								<option value="s6" <?php if ($pconfig['partition'] === "s6") echo "selected";?>>6</option>
								<option value=" " <?php if (empty($pconfig['partition'])) echo "selected";?>><?=gettext("CD/DVD or Old Software RAID");?></option>
							</select><br/>
							<span class="vexpl"><?=gettext("<b>EFI GPT</b> if you want to mount a GPT formatted drive (<b>default partition since 0.684b</b>).<br><b>1*</b> first MBR partition, for UFS formatted drive or Software RAID volume (<b>created before 0.684b</b>).<br><b>2*</b> second MBR partition (<b>DATA partition</b>) if you select option 2 during installation on hard drive (<b>all versions</b>).<br><b>3*</b>,<b>4*</b> third or fourth primary MRB partition.<br><b>5*</b>,<b>6*</b> first or second logical MBR partition on extended partition. <br><b>CD/DVD or Old software RAID</b> for old SoftwareRAID volumes (<b>created before version 0.68</b>) or CD/DVD.<br><br><b>*</b> for disks imported/formatted under a different OS (Windows, Linux, MAC, etc.) that use MBR partition table.");?></span>
			      </td>
			    </tr>
			    <tr id="fstype_tr">
			      <td width="22%" valign="top" class="vncellreq"><?=gettext("File system");?></td>
			      <td class="vtable">
							<select name="fstype" class="formfld" id="fstype" onchange="fstype_change()">
								<option value="ufs" <?php if ($pconfig['fstype'] === "ufs") echo "selected";?>>UFS</option>
								<option value="msdosfs" <?php if ($pconfig['fstype'] === "msdosfs") echo "selected";?>>FAT</option>
								<option value="cd9660" <?php if ($pconfig['fstype'] === "cd9660") echo "selected";?>>CD/DVD</option>
								<option value="ntfs" <?php if ($pconfig['fstype'] === "ntfs") echo "selected";?>>NTFS</option>
								<option value="ext2fs" <?php if ($pconfig['fstype'] === "ext2fs") echo "selected";?>>EXT2</option>
							</select>
			      </td>
					</tr>
					<?php html_filechooser("filename", "Filename", $pconfig['filename'], gettext("ISO file to be mounted."), "/mnt", true);?>
					<?php html_inputbox("sharename", gettext("Share name"), $pconfig['sharename'], "", true, 20);?>
					<?php html_inputbox("desc", gettext("Description"), $pconfig['desc'], gettext("You may enter a description here for your reference."), false, 40);?>
					<?php html_checkbox("readonly", gettext("Read only"), $pconfig['readonly'] ? true : false, gettext("Mount the file system read-only (even the super-user may not write it)."), "", false);?>
					<?php html_checkbox("fsck", gettext("File system check"), $pconfig['fsck'] ? true : false, gettext("Enable foreground/background file system consistency check during boot process."), "", false);?>
					<?php html_separator();?>
					<?php html_titleline(gettext("Access Restrictions"));?>
					<?php $a_owner = array(); foreach (system_get_user_list() as $userk => $userv) { $a_owner[$userk] = htmlspecialchars($userk); }?>
					<?php html_combobox("owner", gettext("Owner"), $pconfig['owner'], $a_owner, "", false);?>
					<?php $a_group = array(); foreach (system_get_group_list() as $groupk => $groupv) { $a_group[$groupk] = htmlspecialchars($groupk); }?>
					<?php html_combobox("group", gettext("Group"), $pconfig['group'], $a_group, "", false);?>
					<tr>
						<td width="22%" valign="top" class="vncell"><?=gettext("Mode");?></td>
			      <td width="78%" class="vtable">
			      	<table width="100%" border="0" cellpadding="0" cellspacing="0">
				        <tr>
				        	<td width="20%" class="listhdrr">&nbsp;</td>
									<td width="20%" class="listhdrc"><?=gettext("Read");?></td>
									<td width="50%" class="listhdrc"><?=gettext("Write");?></td>
									<td width="20%" class="listhdrc"><?=gettext("Execute");?></td>
									<td width="10%" class="list"></td>
				        </tr>
				        <tr>
									<td class="listlr"><?=gettext("Owner");?>&nbsp;</td>
									<td class="listrc" align="center"><input type="checkbox" name="mode_owner[]" id="owner_read" value="r" <?php if (in_array("r", $pconfig['mode_owner'])) echo "checked";?>>&nbsp;</td>
									<td class="listrc" align="center"><input type="checkbox" name="mode_owner[]" id="owner_write" value="w" <?php if (in_array("w", $pconfig['mode_owner'])) echo "checked";?>>&nbsp;</td>
									<td class="listrc" align="center"><input type="checkbox" name="mode_owner[]" id="owner_execute" value="x" <?php if (in_array("x", $pconfig['mode_owner'])) echo "checked";?>>&nbsp;</td>
				        </tr>
				        <tr>
				          <td class="listlr"><?=gettext("Group");?>&nbsp;</td>
									<td class="listrc" align="center"><input type="checkbox" name="mode_group[]" id="group_read" value="r" <?php if (in_array("r", $pconfig['mode_group'])) echo "checked";?>>&nbsp;</td>
									<td class="listrc" align="center"><input type="checkbox" name="mode_group[]" id="group_write" value="w" <?php if (in_array("w", $pconfig['mode_group'])) echo "checked";?>>&nbsp;</td>
									<td class="listrc" align="center"><input type="checkbox" name="mode_group[]" id="group_execute" value="x" <?php if (in_array("x", $pconfig['mode_group'])) echo "checked";?>>&nbsp;</td>
				        </tr>
				        <tr>
				          <td class="listlr"><?=gettext("Others");?>&nbsp;</td>
									<td class="listrc" align="center"><input type="checkbox" name="mode_others[]" id="others_read" value="r" <?php if (in_array("r", $pconfig['mode_others'])) echo "checked";?>>&nbsp;</td>
									<td class="listrc" align="center"><input type="checkbox" name="mode_others[]" id="others_write" value="w" <?php if (in_array("w", $pconfig['mode_others'])) echo "checked";?>>&nbsp;</td>
									<td class="listrc" align="center"><input type="checkbox" name="mode_others[]" id="others_execute" value="x" <?php if (in_array("x", $pconfig['mode_others'])) echo "checked";?>>&nbsp;</td>
				        </tr>
							</table>
			      </td>
			    </tr>
			  </table>
				<div id="submit">
					<input name="Submit" type="submit" class="formbtn" value="<?=((isset($id) && $a_disk[$id]))?gettext("Save"):gettext("Add")?>" onClick="enable_change(true)">
					<input name="uuid" type="hidden" value="<?=$pconfig['uuid'];?>">
					<?php if (isset($id) && $a_mount[$id]): ?>
					<input name="id" type="hidden" value="<?=$id;?>">
					<?php endif; ?>
				</div>
				<div id="remarks">
					<?php html_remark("warning", gettext("Warning"), sprintf(gettext("You can't mount the partition '%s' where the config file is stored.<br>"),htmlspecialchars($cfdevice)) . sprintf(gettext("UFS and variants are the NATIVE file format for FreeBSD (the underlying OS of %s). Attempting to use other file formats such as FAT, FAT32, EXT2, EXT3, or NTFS can result in unpredictable results, file corruption, and loss of data!"), get_product_name()));?>
				</div>
			</form>
		</td>
	</tr>
</table>
<script language="JavaScript">
<!--
type_change();
<?php if (isset($id) && $a_disk[$id]):?>
<!-- Disable controls that should not be modified anymore in edit mode. -->
enable_change(false);
<?php endif;?>
//-->
</script>
<?php include("fend.inc");?>
