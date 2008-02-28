#!/usr/local/bin/php
<?php
/*
	disks_mount_edit.php
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
	$pconfig['type'] = $a_mount[$id]['type'];
	$pconfig['mdisk'] = $a_mount[$id]['mdisk'];
	$pconfig['partition'] = $a_mount[$id]['partition'];
	$pconfig['devicespecialfile'] = $a_mount[$id]['devicespecialfile'];
	$pconfig['fstype'] = $a_mount[$id]['fstype'];
	$pconfig['sharename'] = $a_mount[$id]['sharename'];
	$pconfig['desc'] = $a_mount[$id]['desc'];
	$pconfig['readonly'] = isset($a_mount[$id]['readonly']);
	$pconfig['fsck'] = isset($a_mount[$id]['fsck']);
	
} else {
	$pconfig['type'] = "disk";
	$pconfig['partition'] = "p1";
	$pconfig['readonly'] = false;
	$pconfig['fsck'] = false;
}

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
			$input_errors[] = gettext("Duplicate name.");
			break;
		}
	}

	if (!$input_errors) {
		$mount = array();
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

		if (isset($id) && $a_mount[$id])
			$a_mount[$id] = $mount;
		else
			$a_mount[] = $mount;

		touch($d_mountdirty_path);

		write_config();

		header("Location: disks_mount.php");
		exit;
	}
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
			document.iform.partition.selectedIndex = 0;
			break;

		case 2: /* CD/DVD */
			document.iform.partition.selectedIndex = 5;
			break;
  }
}

function enable_change(enable_change) {
	document.iform.type.disabled = !enable_change;
	document.iform.mdisk.disabled = !enable_change;
}
// -->
</script>
<table width="100%" border="0" cellpadding="0" cellspacing="0">
  <tr>
    <td class="tabnavtbl">
      <ul id="tabnav">
        <li class="tabact"><a href="disks_mount.php" style="color:black" title="<?=gettext("Reload page");?>"><?=gettext("Management");?></a></li>
        <li class="tabinact"><a href="disks_mount_tools.php"><?=gettext("Tools");?></a></li>
        <li class="tabinact"><a href="disks_mount_fsck.php"><?=gettext("Fsck");?></a></li>
      </ul>
    </td>
  </tr>
  <tr>
    <td class="tabcont">
			<form action="disks_mount_edit.php" method="post" name="iform" id="iform">
				<?php if ($input_errors) print_input_errors($input_errors); ?>
			  <table width="100%" border="0" cellpadding="6" cellspacing="0">
			  	<tr>
			    	<td width="22%" valign="top" class="vncellreq"><?=gettext("Type");?></td>
			      <td width="78%" class="vtable">
			  			<select name="type" class="formfld" id="type" onchange="type_change()">
			          <?php $opts = array(gettext("Disk"), gettext("ISO")); $vals = explode(" ", "disk iso"); $i = 0;
								foreach ($opts as $opt):?>
			          <option <?php if ($vals[$i] === $pconfig['type']) echo "selected";?> value="<?=$vals[$i++];?>"><?=htmlspecialchars($opt);?></option>
			          <?php endforeach; ?>
			        </select>
			      </td>
			    </tr>
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
							</select>
							<br>
							<span class="vexpl"><?=gettext("Select 'EFI GPT' if you want to mount a GPT formatted drive (default method since 0.684b).<br>Select 1 for UFS formatted drive or Software RAID volume creating since the 0.683b.<br>Select 2 for mounting the DATA partition if you select option 2 during installation on hard drive.<br>Select 'Old software RAID' for gmirror/graid5/gvinum volumes created with old FreeNAS release.");?></span>
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
			    <tr id="filename_tr">
						<td width="22%" valign="top" class="vncellreq"><?=gettext("Filename");?></td>
			      <td width="78%" class="vtable">
			        <input name="filename" type="text" class="formfld" id="filename" size="60" value="<?=htmlspecialchars($pconfig['filename']);?>">
							<input name="browse" type="button" class="formbtn" id="Browse" onClick='ifield = form.filename; filechooser = window.open("filechooser.php?p="+escape(ifield.value)+"&sd=/mnt", "filechooser", "scrollbars=yes,toolbar=no,menubar=no,statusbar=no,width=550,height=300"); filechooser.ifield = ifield; window.ifield = ifield;' value="..." \>
			      </td>
			    </tr>
					<tr>
						<td width="22%" valign="top" class="vncellreq"><?=gettext("Name");?></td>
			      <td width="78%" class="vtable">
			        <input name="sharename" type="text" class="formfld" id="sharename" size="20" value="<?=htmlspecialchars($pconfig['sharename']);?>">
			      </td>
			    </tr>
			    <tr>
						<td width="22%" valign="top" class="vncell"><?=gettext("Description");?></td>
			      <td width="78%" class="vtable">
							<input name="desc" type="text" class="formfld" id="desc" size="20" value="<?=htmlspecialchars($pconfig['desc']);?>">
			      </td>
			    </tr>
			    <tr id="readonly_tr">
						<td width="22%" valign="top" class="vncell"><?=gettext("Read only");?></td>
			      <td width="78%" class="vtable">
							<input name="readonly" type="checkbox" id="readonly" value="yes" <?php if ($pconfig['readonly']) echo "checked"; ?>>
							<?=gettext("Mount the file system read-only (even the super-user may not write it).");?>
			      </td>
			    </tr>
			    <tr id="fsck_tr">
						<td width="22%" valign="top" class="vncell"><?=gettext("File system check");?></td>
			      <td width="78%" class="vtable">
							<input name="fsck" type="checkbox" id="fsck" value="yes" <?php if ($pconfig['fsck']) echo "checked"; ?>>
							<?=gettext("Enable foreground/background file system consistency check during boot process.");?>
			      </td>
			    </tr>
			    <tr>
			      <td width="22%" valign="top">&nbsp;</td>
			      <td width="78%"> <input name="Submit" type="submit" class="formbtn" value="<?=((isset($id) && $a_disk[$id]))?gettext("Save"):gettext("Add")?>" onClick="enable_change(true)">
			        <?php if (isset($id) && $a_mount[$id]): ?>
			        <input name="id" type="hidden" value="<?=$id;?>">
			        <?php endif; ?>
			      </td>
			    </tr>
			    <tr>
			      <td width="22%" valign="top">&nbsp;</td>
			      <td width="78%"><span class="vexpl"><span class="red"><strong><?=gettext("Warning");?>:<br>
			        </strong></span><?=sprintf(gettext("You can't mount the partition '%s' where the config file is stored.<br>"),htmlspecialchars($cfdevice));?></span>
							<p><span class="vexpl"><?php echo sprintf(gettext("UFS and variants are the NATIVE file format for FreeBSD (the underlying OS of %s). Attempting to use other file formats such as FAT, FAT32, EXT2, EXT3, or NTFS can result in unpredictable results, file corruption, and loss of data!"), get_product_name());?></span></p>
			      </td>
			    </tr>
			  </table>
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
