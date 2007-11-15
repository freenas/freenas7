#!/usr/local/bin/php
<?php
/*
	disks_init.php

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

$pgtitle = array(gettext("Disks"), gettext("Format"));

// Get list of all supported file systems.
$a_fst = get_fstype_list();
unset($a_fst['ntfs']); // Remove NTFS: can't format on NTFS under FreeNAS
unset($a_fst['geli']); // Remove geli
unset($a_fst['cd9660']); // Remove cd9660: can't format a CD/DVD !
$a_fst = array_slice($a_fst, 1); // Remove the first blank line 'unknown'
unset($a_fst['ufs']); // Remove old UFS type: Now FreeNAS will impose only one UFS type: GPT/EFI with softupdate
unset($a_fst['ufs_no_su']);
unset($a_fst['ufsgpt_no_su']);

// Load the /var/etc/cfdevice file to find out on which disk the OS is installed.
$filename=$g['varetc_path']."/cfdevice";
$cfdevice = trim(file_get_contents($filename));
$cfdevice = "/dev/" . $cfdevice;

// Get list of all configured disks (physical and virtual).
$a_alldisk = get_conf_all_disks_list_filtered();

if ($_POST) {
	unset($input_errors);
	unset($errormsg);
	unset($do_format);

	// Input validation.
	$reqdfields = explode(" ", "disk type");
	$reqdfieldsn = array(gettext("Disk"),gettext("Type"));
	do_input_validation($_POST, $reqdfields, $reqdfieldsn, &$input_errors);

	if (!$input_errors) {
		$do_format = true;
		$disk = $_POST['disk'];
		$type = $_POST['type'];
		$minspace = $_POST['minspace'];
		$notinitmbr= $_POST['notinitmbr'];
		$volumelabel = $_POST['volumelabel'];

		// Check whether disk is mounted.
		if (disks_ismounted_ex($disk, "devicespecialfile")) {
			$errormsg = sprintf(gettext("The disk is currently mounted! <a href=%s>Unmount</a> this disk first before proceeding."), "disks_mount_tools.php?disk={$disk}&action=umount");
			$do_format = false;
		}

		if (strstr($cfdevice, $disk)) {
			$input_errors[] = gettext("Can't format the OS origin disk!");
		}

		if ($do_format) {
			// Set new file system type attribute ('fstype') in configuration.
			set_conf_disk_fstype($disk, $type);

			write_config();

			// Update list of configured disks.
			$a_alldisk = get_conf_all_disks_list_filtered();
		}
	}
}

if (!isset($do_format)) {
	$do_format = false;
	$disk = '';
	$type = '';
	$minspace = '';
	$volumelabel = '';
}
?>
<?php include("fbegin.inc");?>
<script language="JavaScript">
<!--
function disk_change() {
  switch(document.iform.disk.value) {
    <?php foreach ($a_alldisk as $diskv): ?>
		case "<?=$diskv['devicespecialfile'];?>":
		  <?php $i = 0;?>
      <?php foreach ($a_fst as $fstval => $fstname): ?>
        document.iform.type.options[<?=$i++;?>].selected = <?php if($diskv['fstype'] == $fstval){echo "true";}else{echo "false";};?>;
      <?php endforeach; ?>
      break;
    <?php endforeach; ?>
  }
  fstype_change();
}

function fstype_change() {
	switch(document.iform.type.value) {
		case "ufsgpt":
			showElementById('minspace_tr','show');
			showElementById('volumelabel_tr','show');
			break;
		case "ext2":
		case "msdos":
			showElementById('minspace_tr','hide');
			showElementById('volumelabel_tr','show');
			break;
		default:
			showElementById('minspace_tr','hide');
			showElementById('volumelabel_tr','hide');
			break;
	}
}
//-->
</script>
<form action="disks_init.php" method="post" name="iform" id="iform">
<?php if($input_errors) print_input_errors($input_errors);?>
<?php if($errormsg) print_error_box($errormsg);?>
  <table width="100%" border="0" cellpadding="6" cellspacing="0">
    <tr>
      <td valign="top" class="vncellreq"><?=gettext("Disk"); ?></td>
      <td class="vtable">
        <select name="disk" class="formfld" id="disk" onchange="disk_change()">
					<option value=""><?=gettext("Must choose one");?></option>
					<?php foreach ($a_alldisk as $diskv):?>
					<?php if (0 == strcmp($diskv['size'], "NA")) continue;?>
					<?php if (1 == disks_exists($diskv['devicespecialfile'])) continue;?>
					<option value="<?=$diskv['devicespecialfile'];?>" <?php if ($diskv['devicespecialfile'] === $disk) echo "selected";?>><?php echo htmlspecialchars($diskv['name'] . ": " .$diskv['size'] . " (" . $diskv['desc'] . ")");?></option>
					<?php endforeach;?>
        </select>
      </td>
		</tr>
		<tr>
	    <td valign="top" class="vncellreq"><?=gettext("File system"); ?></td>
	    <td class="vtable">
	      <select name="type" class="formfld" id="type" onchange="fstype_change()">
	        <?php foreach ($a_fst as $fstval => $fstname): ?>
	        <option value="<?=$fstval;?>" <?php if($type == $fstval) echo 'selected';?>><?=htmlspecialchars($fstname);?></option>
	        <?php endforeach; ?>
	       </select>
	    </td>
		</tr>
		<tr id="volumelabel_tr">
			<td width="22%" valign="top" class="vncell"><?=gettext("Volume label");?></td>
			<td width="78%" class="vtable">
				<input name="volumelabel" type="text" class="formfld" id="volumelabel" size="20" value="<?=htmlspecialchars($volumelabel);?>"><br/>
				<?=gettext("Volume label of the new file system.");?>
			</td>
		</tr>
		<tr id="minspace_tr">
			<td width="22%" valign="top" class="vncell"><?=gettext("Minimum free space") ; ?></td>
			<td width="78%" class="vtable">
				<select name="minspace" class="formfld" id="minspace">
				<?php $types = explode(",", "8,7,6,5,4,3,2,1"); $vals = explode(" ", "8 7 6 5 4 3 2 1");?>
				<?php $j = 0; for ($j = 0; $j < count($vals); $j++): ?>
					<option value="<?=$vals[$j];?>"><?=htmlspecialchars($types[$j]);?></option>
				<?php endfor; ?>
				</select>
				<br><?=gettext("Specify the percentage of space held back from normal users. Note that lowering the threshold can adversely affect performance and auto-defragmentation.") ;?>
			</td>
		</tr>
    <tr>
      <td width="22%" valign="top" class="vncell"><?=gettext("Don't Erase MBR"); ?></td>
      <td width="78%" class="vtable">
        <input name="notinitmbr" id="notinitmbr" type="checkbox" value="yes">
        <?=gettext("Don't erase the MBR (useful for some RAID controller cards)");?>
			</td>
	  </tr>
		<tr>
		  <td width="22%" valign="top">&nbsp;</td>
		  <td width="78%">
        <input name="Submit" type="submit" class="formbtn" value="<?=gettext("Format disk");?>" onclick="return confirm('<?=gettext("Do you really want to format this disk? All data will be lost!");?>')">
		  </td>
		</tr>
		<tr>
			<td valign="top" colspan="2">
			<? if ($do_format) {
				echo("<strong>" . gettext("Command output:") . "</strong>");
				echo('<pre>');
				ob_end_flush();
				disks_format($disk,$type,$notinitmbr,$minspace,$volumelabel);
				echo('</pre>');
			}
			?>
			</td>
		</tr>
		<tr>
      <td width="22%" valign="top">&nbsp;</td>
      <td width="78%">
				<span class="vexpl"><span class="red"><strong><?=gettext("Warning");?>:<br></strong></span><?=gettext("This step will erase all your partition, create one GPT/EFI (for UFS) or MBR (for others) partition and format the hard drive with the file system specified.");?><br><br>
				<?php echo sprintf(gettext("UFS is the NATIVE file format for FreeBSD (the underlying OS of %s). Attempting to use other file formats such as FAT, FAT32, EXT2, EXT3, or NTFS can result in unpredictable results, file corruption, and loss of data!"), get_product_name());?></span>
			</td>
		</tr>
	</table>
</form>
<script language="JavaScript">
<!--
disk_change();
//-->
</script>
<?php include("fend.inc");?>
