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

$pgtitle = array(gettext("Disks"),gettext("Initialize"));

if (!is_array($config['disks']['disk']))
	$config['disks']['disk'] = array();

disks_sort();

if (!is_array($config['gconcat']['vdisk']))
	$config['gconcat']['vdisk'] = array();

gconcat_sort();

if (!is_array($config['gmirror']['vdisk']))
	$config['gmirror']['vdisk'] = array();

gmirror_sort();

if (!is_array($config['graid5']['vdisk']))
	$config['graid5']['vdisk'] = array();

graid5_sort();

if (!is_array($config['gstripe']['vdisk']))
	$config['gstripe']['vdisk'] = array();

gstripe_sort();

if (!is_array($config['gvinum']['vdisk']))
	$config['gvinum']['vdisk'] = array();

gvinum_sort();

if (!is_array($config['geli']['vdisk']))
	$config['geli']['vdisk'] = array();

geli_sort();

/* Get all fstype supported by FreeNAS. */
$a_fst = get_fstype_list();
// Remove NTFS: can't format on NTFS under FreeNAS
unset($a_fst['ntfs']);
// Remove geli
unset($a_fst['geli']);
// Remove cd9660: can't format a CD/DVD !
unset($a_fst['cd9660']);
// Remove the first blank line 'unknown'
$a_fst = array_slice($a_fst, 1);

/* Load the cfdevice file for found the disk where FreeNAS is installed*/
$filename=$g['varetc_path']."/cfdevice";
$cfdevice = trim(file_get_contents("$filename"));
$cfdevice = "/dev/" . $cfdevice;

/* Get disk configurations. */
$a_disk = &$config['disks']['disk'];
$a_gconcat = &$config['gconcat']['vdisk'];
$a_gmirror = &$config['gmirror']['vdisk'];
$a_gstripe = &$config['gstripe']['vdisk'];
$a_graid5 = &$config['graid5']['vdisk'];
$a_gvinum = &$config['gvinum']['vdisk'];
$a_geli = &$config['geli']['vdisk'];
$a_alldisk = array_merge($a_disk,$a_gconcat,$a_gmirror,$a_gstripe,$a_graid5,$a_gvinum,$a_geli);

if ($_POST) {
	unset($input_errors);
	unset($errormsg);
	unset($do_format);

	/* input validation */
	$reqdfields = explode(" ", "disk type");
	$reqdfieldsn = array(gettext("Disk"),gettext("Type"));
	do_input_validation($_POST, $reqdfields, $reqdfieldsn, &$input_errors);

	if (!$input_errors) {
		$do_format = true;
		$disk = $_POST['disk'];
		$type = $_POST['type'];
		$notinitmbr= $_POST['notinitmbr'];

		/* Check if disk is mounted. */ 
		if(disks_check_mount_fullname($disk)) {
			$errormsg = sprintf( gettext("The disk is currently mounted! <a href=%s>Unmount</a> this disk first before proceeding."), "disks_mount_tools.php?disk={$disk}&action=umount");
			$do_format = false;
		}
		
		if (strstr ($cfdevice,$disk) ) {
		$input_errors[] = gettext("Can't Format the drive where FreeNAS configuration file is installed!");
		}

		if ($do_format) {
			/* Get the id of the disk array entry. */
			$NotFound = 1;
			$id = array_search_ex($disk, $a_disk, "fullname");

			/* disk */
			if ($id !== false) {
				/* Set new filesystem type. */
 				$a_disk[$id]['fstype'] = $type;
				$NotFound = 0;
			} else {
				$id = array_search_ex($disk, $a_gmirror, "fullname");
			}

			/* gmirror */
			if (($id !== false) && $NotFound) {
				/* Set new filesystem type. */
 				$a_gmirror[$id]['fstype'] = $type;
				$NotFound = 0;
			} else {
				$id = array_search_ex($disk, $a_gstripe, "fullname");
			}

			/* gstripe */
			if (($id !== false) && $NotFound) {
				/* Set new filesystem type. */
 				$a_gstripe[$id]['fstype'] = $type;
				$NotFound = 0;
			} else {
				$id = array_search_ex($disk, $a_gconcat, "fullname");
			}

			/* gconcat */
			if (($id !== false) && $NotFound) {
				/* Set new filesystem type. */
 				$a_gconcat[$id]['fstype'] = $type;
				$NotFound = 0;
			} else {
				$id = array_search_ex($disk, $a_graid5, "fullname");
			}

			/* graid5 */
			if (($id !== false) && $NotFound) {
				/* Set new filesystem type. */
 				$a_graid5[$id]['fstype'] = $type;
				$NotFound = 0;
			} else {
				$id = array_search_ex($disk, $a_gvinum, "fullname");
			}

			/* gvinum */
			if (($id !== false) && $NotFound) {
				/* Set new filesystem type. */
 				$a_gvinum[$id]['fstype'] = $type;
				$NotFound = 0;
			}
			
			/* geli */
			if (($id !== false) && $NotFound) {
				/* Set new filesystem type. */
 				$a_geli[$id]['fstype'] = $type;
				$NotFound = 0;
			}

			write_config();
		}
	}
}
if (!isset($do_format)) {
	$do_format = false;
	$disk = '';
	$type = '';
}
?>
<?php include("fbegin.inc"); ?>
<script language="JavaScript">
<!--
function disk_change() {
  switch(document.iform.disk.value)
  {
    <?php foreach ($a_alldisk as $diskv): ?>
		case "<?=$diskv['fullname'];?>":
		  <?php $i = 0;?>
      <?php foreach ($a_fst as $fstval => $fstname): ?>
        document.iform.type.options[<?=$i++;?>].selected = <?php if($diskv['fstype'] == $fstval){echo "true";}else{echo "false";};?>;
      <?php endforeach; ?>
      break;
    <?php endforeach; ?>
  }
}
// -->
</script>
<form action="disks_init.php" method="post" name="iform" id="iform">
<?php if($input_errors) print_input_errors($input_errors);?>
<?php if($errormsg) print_error_box($errormsg);?>
  <table width="100%" border="0" cellpadding="6" cellspacing="0">
    <tr>
      <td valign="top" class="vncellreq"><?=gettext("Disk"); ?></td>
      <td class="vtable">
        <select name="disk" class="formfld" id="disk" onchange="disk_change()">
          <?php foreach ($a_alldisk as $diskv): ?>
					<?php if (strcmp($diskv['size'],"NA") == 0) continue; ?>
					<?php if (strcmp($diskv['fstype'],"geli") == 0) continue; ?>
					<?php if (disks_geli_check($diskv['fullname'])) continue; ?>
          <option value="<?=$diskv['fullname'];?>" <?php if ($diskv['name'] == $disk) echo "selected";?>><?php echo htmlspecialchars($diskv['name'] . ": " .$diskv['size'] . " (" . $diskv['desc'] . ")");?></option>
          <?php endforeach; ?>
        </select>
      </td>
		</tr>
    <td valign="top" class="vncellreq"><?=gettext("File system"); ?></td>
    <td class="vtable">
      <select name="type" class="formfld" id="type">
        <?php foreach ($a_fst as $fstval => $fstname): ?>
        <option value="<?=$fstval;?>" <?php if($type == $fstval) echo 'selected';?>><?=htmlspecialchars($fstname);?></option>
        <?php endforeach; ?>
       </select>
    </td>
    <tr>
      <td width="22%" valign="top" class="vncell"><strong><?=gettext("Don't Erase MBR"); ?><strong></td>
      <td width="78%" class="vtable">
        <input name="notinitmbr" id="notinitmbr" type="checkbox" value="yes" >
        <?=gettext("Don't erase the MBR (useful for some RAID controller cards)"); ?><br>
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
			<? if ($do_format)
			{
				echo("<strong>".gettext("Disk initialization details").":</strong>");
				echo('<pre>');
				ob_end_flush();

				// Erase MBR if not checked
				if (!$notinitmbr) {
					echo gettext("Erasing MBR and all partitions").".\n";
					system("dd if=/dev/zero of=" . escapeshellarg($disk) . " bs=32k count=640");
				}
				else
					echo gettext("Keeping the MBR and all partitions")."\n";

				switch ($type)
				{
					case "ufs":
						// Initialize disk
						echo gettext("Creating one partition").":\n";
						system("/sbin/fdisk -I -b /boot/mbr " . escapeshellarg($disk));
						// Initialize the partition
						echo gettext("Initializing partition").".\n";
						system("/bin/dd if=/dev/zero of=" . escapeshellarg($disk) . "s1 bs=32k count=16");
						// Create s1 label
						echo gettext("Creating BSD label").".\n";
						system("/sbin/bsdlabel -w " . escapeshellarg($disk) . "s1 auto");
						// Create filesystem
						echo gettext("Creating filesystem").":\n";
						system("/sbin/newfs -U " . escapeshellarg($disk) . "s1");
						echo gettext("Done")."!\n";
						break;
					case "ufs_no_su":
						// Initialize disk
						echo gettext("Creating one partition").":\n";
						system("/sbin/fdisk -I -b /boot/mbr " . escapeshellarg($disk));
						// Initialize the partition
						echo gettext("Initializing partition").".\n";
						system("/bin/dd if=/dev/zero of=" . escapeshellarg($disk) . "s1 bs=32k count=16");
						// Create s1 label
						echo gettext("Creating BSD label").".\n";
						system("/sbin/bsdlabel -w " . escapeshellarg($disk) . "s1 auto");
						// Create filesystem
						echo gettext("Creating filesystem").":\n";
						system("/sbin/newfs -m 0 " . escapeshellarg($disk) . "s1");
						echo gettext("Done")."!\n";
						break;
					case "ufsgpt":
						// Create GPT partition table
						echo sprintf(gettext("Destroying old %s information"), "GPT").":\n";
						system("/sbin/gpt destroy " . escapeshellarg($disk));
						echo sprintf(gettext("Creating %s partition"), "GPT").":\n";
						system("/sbin/gpt create -f " . escapeshellarg($disk));
						system("/sbin/gpt add -t ufs " . escapeshellarg($disk));
						// Create filesystem
						echo gettext("Creating filesystem with 'Soft Updates'").":\n";
						system("/sbin/newfs -U " . escapeshellarg($disk) . "p1");
						echo gettext("Done")."!\n";
						break;
					case "ufsgpt_no_su":
						// Create GPT partition table
						echo sprintf(gettext("Destroying old %s information"), "GPT").":\n";
						system("/sbin/gpt destroy " . escapeshellarg($disk));
						echo sprintf(gettext("Creating %s partition"), "GPT").":\n"; 
						system("/sbin/gpt create -f " . escapeshellarg($disk));
						system("/sbin/gpt add -t ufs " . escapeshellarg($disk));
						// Create filesystem
						echo gettext("Creating filesystem without 'Soft Updates'").":\n";
						system("/sbin/newfs -m 0 " . escapeshellarg($disk) . "p1");
						echo gettext("Done")."!\n";
						break;
					case "softraid":
						// Initialise the disk
						echo gettext("Initializing disk").".\n";
						system("/bin/dd if=/dev/zero of=" . escapeshellarg($disk) . " bs=1m count=16");
						echo gettext("Done")."!\n";
						break;
					case "msdos":
						// Initialize disk
						echo gettext("Creating one partition").":\n";
						system("/sbin/fdisk -I -b /boot/mbr " . escapeshellarg($disk));
						// Initialize the partition
						echo gettext("Initializing partition")."\n";
						system("/bin/dd if=/dev/zero of=" . escapeshellarg($disk) . "s1 bs=32k count=16");
						// Create filesystem
						echo gettext("Creating filesystem").":\n";
						system("/sbin/newfs_msdos -F 32 " . escapeshellarg($disk) . "s1");
						echo "Done!\n";
						break;
					case "ext2":
						// Initialize disk
						echo gettext("Creating one partition").":\n";
						system("/sbin/fdisk -I -b /boot/mbr " . escapeshellarg($disk));
						// Initialize the partition
						echo gettext("Initializing partition")."\n";
						system("/bin/dd if=/dev/zero of=" . escapeshellarg($disk) . "s1 bs=32k count=16");
						// Create filesystem
						echo gettext("Creating filesystem").":\n";
						system("/usr/local/sbin/mke2fs " . escapeshellarg($disk) . "s1");
						echo "Done!\n";
						break;
				}
				echo('</pre>');
			}
			?>
			</td>
		</tr>
		<tr>
      <td width="22%" valign="top">&nbsp;</td>
      <td width="78%">
				<span class="vexpl"><span class="red"><strong><?=gettext("Warning");?>:<br></strong></span><?=gettext("This step will erase all your partition, create partition number 1 and format the hard drive with the file system specified.");?><br><br>
				<?php echo sprintf(gettext("UFS and variants are the NATIVE file format for FreeBSD (the underlying OS of %s). Attempting to use other file formats such as FAT, FAT32, EXT2, EXT3, or NTFS can result in unpredictable results, file corruption, and loss of data!"), get_product_name());?></span>
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
