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

if (!is_array($config['mounts']['mount']))
	$config['mounts']['mount'] = array();

if (!is_array($config['mounts']['iso']))
	$config['mounts']['iso'] = array();

mount_sort();
mount_iso_sort();

$a_mount = &$config['mounts']['mount'];
$a_mount_iso = &$config['mounts']['iso'];

if (isset($id) && $a_mount_iso[$id]) {
	$pconfig['filename'] = $a_mount_iso[$id]['filename'];
	$pconfig['desc'] = $a_mount_iso[$id]['desc'];
	$pconfig['sharename'] = $a_mount_iso[$id]['sharename'];
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

	if (($_POST['desc'] && !is_validdesc($_POST['desc']))) {
		$input_errors[] = gettext("The description name contain invalid characters.");
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
			$input_errors[] = gettext("Duplicate Share Name.");
			break;
		}
	}

	if (!$input_errors) {
		$mount = array();
		$mount['filename'] = $_POST['filename'];
		$mount['desc'] = $_POST['desc'];
		$mount['sharename'] = $_POST['sharename'];

		if (isset($id) && $a_mount_iso[$id]) {
			$a_mount_iso[$id] = $mount;
		} else {
			$a_mount_iso[] = $mount;
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
				<input name="browse" type="button" class="formbtn" id="Browse" onClick='ifield = form.filename; filechooser = window.open("filechooser.php?p="+escape(ifield.value), "filechooser", "scrollbars=yes,toolbar=no,menubar=no,statusbar=no,width=500,height=300"); filechooser.ifield = ifield; window.ifield = ifield;' value="..." \> 
      </td>
    </tr>
	 <tr> 
     <td width="22%" valign="top" class="vncellreq"><?=gettext("Share Name") ;?></td>
      <td width="78%" class="vtable"> 
        <?=$mandfldhtml;?><input name="sharename" type="text" class="formfld" id="sharename" size="20" value="<?=htmlspecialchars($pconfig['sharename']);?>"> 
      </td>
    </tr>
    <tr> 
     <td width="22%" valign="top" class="vncell"><?=gettext("Description") ;?></td>
      <td width="78%" class="vtable"> 
        <input name="desc" type="text" class="formfld" id="desc" size="20" value="<?=htmlspecialchars($pconfig['desc']);?>"> 
      </td>
    </tr>
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
