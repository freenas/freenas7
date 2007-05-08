#!/usr/local/bin/php
<?php 
/*
	disks_mount_iso_edit.php
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

$pgtitle = array(gettext("Disks"),gettext("Mount Point"),gettext("ISO"),isset($id)?gettext("Edit"):gettext("Add"));

/* Fix old config file */
if (!isset($config['mounts']['iso_md_id'])) {
	$config['mounts']['iso_md_id']=2;
}

if (!is_array($config['mounts']['iso']))
	$config['mounts']['iso'] = array();

mount_iso_sort();

if (is_array($config['mounts']['mount'])) {
	$a_mount = &$config['mounts']['mount'];
}
else {
	$input_errors[] = gettext("You must add a mount point before.");
}

$a_mount_iso = &$config['mounts']['iso'];

if (isset($id) && $a_mount_iso[$id]) {
	$pconfig['filename'] = $a_mount_iso[$id]['filename'];
	$pconfig['sharename'] = $a_mount_iso[$id]['sharename'];
	$pconfig['md_id'] = $a_mount_iso[$id]['md_id'];
}

if ($_POST) {
	unset($input_errors);
	$pconfig = $_POST;

	/* input validation */
  $reqdfields = explode(" ", "filename sharename");
  $reqdfieldsn = array(gettext("Filename"), gettext("Share Name"));
  do_input_validation($_POST, $reqdfields, $reqdfieldsn, &$input_errors);
	
	if (($_POST['sharename'] && !is_validsharename($_POST['sharename']))) {
		$input_errors[] = gettext("The share name may only consist of the characters a-z, A-Z, 0-9, _ , -.");
	}

	/* check for name conflicts */
	foreach ($a_mount_iso as $mount_iso) {
		if (isset($id) && ($a_mount_iso[$id]) && ($a_mount_iso[$id] === $mount_iso))
			continue;

		// Check for duplicate mount point
		if (($mount_iso['filename'] == $_POST['filename']))       {
			$input_errors[] = gettext("This filename is already configured.");
			break;
		}
	}
	
	/* check for sharename conflicts */
	foreach ($a_mount as $mount) {
		if (($_POST['sharename']) && ($mount['sharename'] == $_POST['sharename'])) {
			$input_errors[] = gettext("Duplicate Share Name with a mount point.");
			break;
		}
	}

	if (!$input_errors) {
		$mount_iso = array();
		$mount_iso['filename'] = $_POST['filename'];
		$mount_iso['sharename'] = $_POST['sharename'];
			
		if (isset($id) && $a_mount_iso[$id]) {
			$mount_iso['md_id'] = $_POST['md_id'];
			$a_mount_iso[$id] = $mount_iso;
		}
		else 	{
			$mount_iso['md_id'] = $config['mounts']['iso_md_id'];
			$config['mounts']['iso_md_id'] ++;
			$a_mount_iso[] = $mount_iso;
		}
		
		touch($d_mount_iso_dirty_path);
		
		write_config();
		
		header("Location: disks_mount_iso.php");
		exit;
	}
}
?>
<?php include("fbegin.inc"); ?>
<?php if ($input_errors) print_input_errors($input_errors); ?>
<form action="disks_mount_iso_edit.php" method="post" name="iform" id="iform">
  <table width="100%" border="0" cellpadding="6" cellspacing="0">
     <tr> 
     <td width="22%" valign="top" class="vncellreq"><?=gettext("Filename") ;?></td>
      <td width="78%" class="vtable"> 
        <?=$mandfldhtml;?><input name="filename" type="text" class="formfld" id="filename" size="20" value="<?=htmlspecialchars($pconfig['filename']);?>"> 
      </td>
    </tr>
	 <tr> 
     <td width="22%" valign="top" class="vncellreq"><?=gettext("Share Name") ;?></td>
      <td width="78%" class="vtable"> 
        <?=$mandfldhtml;?><input name="sharename" type="text" class="formfld" id="sharename" size="20" value="<?=htmlspecialchars($pconfig['sharename']);?>"> 
      </td>
    </tr>
    <tr> 
	 <?php if (isset($id) && $a_mount_iso[$id]): ?>
               <input name="md_id" type="hidden" class="formfld" id="md_id" value="<?=$pconfig['md_id'];?>">
               <?php endif; ?>
    <tr> 
      <td width="22%" valign="top">&nbsp;</td>
      <td width="78%"> <input name="Submit" type="submit" class="formbtn" value="<?=(isset($id))?gettext("Save"):gettext("Add")?>"> 
        <?php if (isset($id) && $a_mount_iso[$id]): ?>
        <input name="id" type="hidden" value="<?=$id;?>"> 
        <?php endif; ?>
      </td>
    </tr>
  </table>
</form>
<?php include("fend.inc"); ?>
