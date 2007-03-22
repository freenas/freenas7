#!/usr/local/bin/php
<?php 
/*
	disks_mount_edit.php
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

$pgtitle = array(gettext("Disks"),gettext("Mount Point"),isset($id)?gettext("Edit"):gettext("Add"));

if (!is_array($config['mounts']['mount']))
	$config['mounts']['mount'] = array();

mount_sort();

if (!is_array($config['disks']['disk']))
	$config['disks']['disk'] = array();
	
disks_sort();

if (!is_array($config['gvinum']['vdisk']))
	$config['gvinum']['vdisk'] = array();

gvinum_sort();

if (!is_array($config['gmirror']['vdisk']))
	$config['gmirror']['vdisk'] = array();

gmirror_sort();

if (!is_array($config['gconcat']['vdisk']))
	$config['gconcat']['vdisk'] = array();

gconcat_sort();

if (!is_array($config['gstripe']['vdisk']))
	$config['gstripe']['vdisk'] = array();

gstripe_sort();

if (!is_array($config['graid5']['vdisk']))
	$config['graid5']['vdisk'] = array();

graid5_sort();

if (!is_array($config['geli']['vdisk']))
	$config['geli']['vdisk'] = array();

geli_sort();

$a_mount = &$config['mounts']['mount'];

$a_disk = array_merge($config['disks']['disk'],$config['gvinum']['vdisk'],$config['gmirror']['vdisk'],$config['gconcat']['vdisk'],$config['gstripe']['vdisk'],$config['graid5']['vdisk'],$config['geli']['vdisk']);

/* Load the cfdevice file*/
$filename=$g['varetc_path']."/cfdevice";
$cfdevice = trim(file_get_contents("$filename"));

if (isset($id) && $a_mount[$id]) {
	$pconfig['mdisk'] = $a_mount[$id]['mdisk'];
	$pconfig['partition'] = $a_mount[$id]['partition'];
	$pconfig['fullname'] = $a_mount[$id]['fullname'];
	$pconfig['fstype'] = $a_mount[$id]['fstype'];
	$pconfig['sharename'] = $a_mount[$id]['sharename'];
	$pconfig['desc'] = $a_mount[$id]['desc'];
} else {
$pconfig['partition'] = "gpt";
}

if ($_POST) {
	unset($input_errors);
	$pconfig = $_POST;

	/* input validation */
  $reqdfields = explode(" ", "sharename");
  $reqdfieldsn = array(gettext("Share Name"));
  do_input_validation($_POST, $reqdfields, $reqdfieldsn, &$input_errors);

	if (($_POST['sharename'] && !is_validsharename($_POST['sharename']))) {
		$input_errors[] = gettext("The share name may only consist of the characters a-z, A-Z, 0-9, _ , -.");
	}

	if (($_POST['desc'] && !is_validdesc($_POST['desc']))) {
		$input_errors[] = gettext("The description name contain invalid characters.");
	}

	$device=$_POST['mdisk'].$_POST['partition'];
	if ($device == $cfdevice ) {
		$input_errors[] = gettext("Can't mount the system partition 1, the DATA partition is the 2.");
	}

	/* check for name conflicts */
	foreach ($a_mount as $mount) {
		if (isset($id) && ($a_mount[$id]) && ($a_mount[$id] === $mount))
			continue;

		// Check for duplicate mount point
		if (($mount['mdisk'] == $_POST['mdisk']) && ($mount['partition'] == $_POST['partition']))       {
			$input_errors[] = gettext("This disk/partition is already configured.");
			break;
		}
		
		if (($_POST['sharename']) && ($mount['sharename'] == $_POST['sharename'])) {
			$input_errors[] = gettext("Duplicate Share Name.");
			break;
		}
	}

	if (!$input_errors) {
		$mount = array();
		$mount['mdisk'] = $_POST['mdisk'];
		$mount['partition'] = $_POST['partition'];
		$mount['fstype'] = $_POST['fstype'];
		$mount['desc'] = $_POST['desc'];
		/* if not sharename given, create one */
		if (!$_POST['sharename'])
			$mount['sharename'] = "disk_{$_POST['mdisk']}_part_{$_POST['partition']}";
		else
			$mount['sharename'] = $_POST['sharename'];
		
		// Generate fullname
		$mount['fullname'] = "{$mount['mdisk']}{$mount['partition']}";
		
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
<?php include("fbegin.inc"); ?>
<?php if ($input_errors) print_input_errors($input_errors); ?>
<form action="disks_mount_edit.php" method="post" name="iform" id="iform">
  <table width="100%" border="0" cellpadding="6" cellspacing="0">
    <tr> 
      <td valign="top" class="vncellreq"><?=gettext("Disk"); ?></td>
      <td class="vtable">            
    	 <select name="mdisk" class="formfld" id="mdisk">
    	  <?php foreach ($a_disk as $disk): ?>
				<?php if ((strcmp($disk['fstype'],"softraid")==0) || (strcmp($disk['fstype'],"geli")==0)): ?> 	 				
    			<?php continue; ?>
    			<?php endif; ?>
    				<option value="<?=$disk['fullname'];?>" <?php if ($pconfig['mdisk'] == $disk['name']) echo "selected";?>> 
    				<?php echo htmlspecialchars($disk['name'] . ": " .$disk['size'] . " (" . $disk['desc'] . ")");	?>
    				</option>
    		  <?php endforeach; ?>
    		</select>
      </td>
    </tr>   
     <tr> 
      <td valign="top" class="vncellreq"><?=gettext("Partition") ; ?></td>
      <td class="vtable"> 
        <select name="partition" class="formfld" id="partition">
		  <option value="p1" <?php if ($pconfig['partition'] == "gpt") echo "selected"; ?>>EFI GPT</option>
          <option value="s1" <?php if ($pconfig['partition'] == "s1") echo "selected"; ?>>1</option>
          <option value="s2" <?php if ($pconfig['partition'] == "s2") echo "selected"; ?>>2</option>
          <option value="s3" <?php if ($pconfig['partition'] == "s3") echo "selected"; ?>>3</option>
          <option value="s4" <?php if ($pconfig['partition'] == "s4") echo "selected"; ?>>4</option>
          <option value="" <?php if ($pconfig['partition'] == "") echo "selected"; ?>>CD/DVD</option>
          <option value="gmirror" <?php if ($pconfig['partition'] == "gmirror") echo "selected"; ?>>old <?=gettext("Software RAID") ;?> - gmirror</option>
          <option value="graid5" <?php if ($pconfig['partition'] == "graid5") echo "selected"; ?>>old <?=gettext("Software RAID") ;?> - graid5</option>
          <option value="gvinum" <?php if ($pconfig['partition'] == "gvinum") echo "selected"; ?>>old <?=gettext("Software RAID") ;?> - gvinum</option>
        </select>
		<br><?=gettext("Select EFI GPT if you want to mount a GPT formatted drive (default method since 0.684b).<br>Select 1 for UFS formatted drive or software RAID volume creating since the 0.683b).<br>Select 2 for mounting the DATA partition if you install FreeNAS on an hard drive.<br>Select old software gmirror/graid5/gvinum for volume created with FreeNAS release older than 0.XX") ;?>
      </td>
    </tr>
    <tr> 
      <td valign="top" class="vncellreq"><?=gettext("File system") ;?></td>
      <td class="vtable"> 
        <select name="fstype" class="formfld" id="fstype">
          <option value="ufs" <?php if ($pconfig['fstype'] == "ufs") echo "selected"; ?>>UFS</option>
          <option value="msdosfs" <?php if ($pconfig['fstype'] == "msdosfs") echo "selected"; ?>>FAT</option>
          <option value="cd9660" <?php if ($pconfig['fstype'] == "cd9669") echo "selected"; ?>>CD/DVD</option>
          <option value="ntfs" <?php if ($pconfig['fstype'] == "ntfs") echo "selected"; ?>>NTFS</option> 
          <option value="ext2fs" <?php if ($pconfig['fstype'] == "ext2fs") echo "selected"; ?>>EXT2</option> 
        </select>
      </td>
    </tr>
     <tr> 
     <td width="22%" valign="top" class="vncell"><?=gettext("Share Name") ;?></td>
      <td width="78%" class="vtable"> 
        <?=$mandfldhtml;?><input name="sharename" type="text" class="formfld" id="sharename" size="20" value="<?=htmlspecialchars($pconfig['sharename']);?>"> 
      </td>
    </tr>
    <tr> 
     <td width="22%" valign="top" class="vncell"><?=gettext("Description") ;?></td>
      <td width="78%" class="vtable"> 
        <?=$mandfldhtml;?><input name="desc" type="text" class="formfld" id="desc" size="20" value="<?=htmlspecialchars($pconfig['desc']);?>"> 
      </td>
    </tr>
    <tr> 
      <td width="22%" valign="top">&nbsp;</td>
      <td width="78%"> <input name="Submit" type="submit" class="formbtn" value="<?=((isset($id) && $a_disk[$id]))?gettext("Save"):gettext("Add")?>"> 
        <?php if (isset($id) && $a_mount[$id]): ?>
        <input name="id" type="hidden" value="<?=$id;?>"> 
        <?php endif; ?>
      </td>
    </tr>
    <tr> 
      <td width="22%" valign="top">&nbsp;</td>
      <td width="78%"><span class="vexpl"><span class="red"><strong><?=gettext("Warning"); ?>:<br>
        </strong></span><?=sprintf(gettext("You can't mount the partition '%s' where the config file is stored.<br>"),htmlspecialchars($cfdevice));?></span>
				<p><span class="vexpl"><?php echo sprintf(gettext("UFS and variants are the NATIVE file format for FreeBSD (the underlying OS of %s). Attempting to use other file formats such as FAT, FAT32, EXT2, EXT3, or NTFS can result in unpredictable results, file corruption, and loss of data!"), get_product_name());?></p>
      </td>
    </tr>
  </table>
</form>
<?php include("fend.inc"); ?>
