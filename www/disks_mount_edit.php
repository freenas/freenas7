#!/usr/local/bin/php
<?php 
/*
	disks_mount_edit.php
	part of FreeNAS (http://www.freenas.org)
	Copyright (C) 2005-2006 Olivier Cochard-Labbé <olivier@freenas.org>.
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

$pgtitle = array(_DISKS,_DISKSMOUNTPHP_NAME,isset($id)?_EDIT:_ADD);

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

$a_mount = &$config['mounts']['mount'];

$a_disk = array_merge($config['disks']['disk'],$config['gvinum']['vdisk'],$config['gmirror']['vdisk']);

/* Load the cfdevice file*/
$filename=$g['varetc_path']."/cfdevice";
$cfdevice = trim(file_get_contents("$filename"));

if (isset($id) && $a_mount[$id]) {
	$pconfig['mdisk'] = $a_mount[$id]['mdisk'];
	$pconfig['partition'] = $a_mount[$id]['partition'];
	$pconfig['fstype'] = $a_mount[$id]['fstype'];
	$pconfig['sharename'] = $a_mount[$id]['sharename'];
	$pconfig['desc'] = $a_mount[$id]['desc'];
}

if ($_POST) {

	unset($input_errors);
	$pconfig = $_POST;

	/* input validation */
	
	if (($_POST['sharename'] && !is_validsharename($_POST['sharename'])))
	{
		$input_errors[] = _DISKSMOUNTEDITPHP_MSGVALIDNAME;
	}
	
	
	if (($_POST['desc'] && !is_validdesc($_POST['desc'])))
	{
		$input_errors[] = _DISKSMOUNTEDITPHP_MSGVALIDDESC;
	}
	$device=$_POST['mdisk'].$_POST['partition'];
	
	if ($device == $cfdevice )
	{
		$input_errors[] = _DISKSMOUNTEDITPHP_MSGVALIDMOUNTSYS;
	}
	
	
		
	/* check for name conflicts */
	foreach ($a_mount as $mount)
	{
		if (isset($id) && ($a_mount[$id]) && ($a_mount[$id] === $mount))
			continue;

		/* Remove the duplicate disk use
		if ($mount['mdisk'] == $_POST['mdisk'])
		{
			$input_errors[] = "This device already exists in the mount point list.";
			break;
		}
		*/
		
		if (($_POST['sharename']) && ($mount['sharename'] == $_POST['sharename']))
		{
			$input_errors[] = _DISKSMOUNTEDITPHP_MSGVALIDDUP;
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
                  <td valign="top" class="vncellreq"><?=_DISK; ?></td>
                  <td class="vtable">            
                	 <select name="mdisk" class="formfld" id="mdisk">
                		  <?php foreach ($a_disk as $disk): ?>
                			<?php if ((strcmp($disk['fstype'],"gvinum")!=0) | (strcmp($disk['fstype'],"gmirror")!=0)): ?> 	  
                				<option value="<?=$disk['name'];?>" <?php if ($pconfig['mdisk'] == $disk['name']) echo "selected";?>> 
                				<?php echo htmlspecialchars($disk['name'] . ": " .$disk['size'] . " (" . $disk['desc'] . ")");	?>
                				</option>
                			<?php endif; ?>
                		  <?php endforeach; ?>
                		</select>
                  </td>
	              </tr>   
                 <tr> 
                  <td valign="top" class="vncellreq"><?=_PARTITION ; ?></td>
                  <td class="vtable"> 
                    <select name="partition" class="formfld" id="partition">
                      <option value="s1" <?php if ($pconfig['partition'] == "s1") echo "selected"; ?>>1</option>
                      <option value="s2" <?php if ($pconfig['partition'] == "s2") echo "selected"; ?>>2</option>
                      <option value="s3" <?php if ($pconfig['partition'] == "s3") echo "selected"; ?>>3</option>
                      <option value="s4" <?php if ($pconfig['partition'] == "s4") echo "selected"; ?>>4</option>
                      <option value="gmirror" <?php if ($pconfig['partition'] == "gmirror") echo "selected"; ?>><?=_SOFTRAID ;?> - gmirror</option>
                      <option value="gvinum" <?php if ($pconfig['partition'] == "gvinum") echo "selected"; ?>><?=_SOFTRAID ;?> - gvinum</option>
                      <option value="p1" <?php if ($pconfig['partition'] == "gpt") echo "selected"; ?>>GPT</option>
                    </select>
                  </td>
                </tr>
                <tr> 
                  <td valign="top" class="vncellreq"><?=_FILESYSTEM ;?></td>
                  <td class="vtable"> 
                    <select name="fstype" class="formfld" id="fstype">
                      <option value="ufs" <?php if ($pconfig['fstype'] == "ufs") echo "selected"; ?>>UFS</option>
                      <option value="msdosfs" <?php if ($pconfig['fstype'] == "msdosfs") echo "selected"; ?>>FAT</option>
                      <option value="ntfs" <?php if ($pconfig['fstype'] == "ntfs") echo "selected"; ?>>NTFS (read-only)</option> 
                      <option value="ext2fs" <?php if ($pconfig['fstype'] == "ext2fs") echo "selected"; ?>>EXT2 FS</option> 
                    </select>
                  </td>
                </tr>
                 <tr> 
                 <td width="22%" valign="top" class="vncell"><?=_DISKSMOUNTPHP_SHARENAME ;?></td>
                  <td width="78%" class="vtable"> 
                    <?=$mandfldhtml;?><input name="sharename" type="text" class="formfld" id="sharename" size="20" value="<?=htmlspecialchars($pconfig['sharename']);?>"> 
                  </td>
				</tr>
				 <tr> 
                 <td width="22%" valign="top" class="vncell"><?=_DESC ;?></td>
                  <td width="78%" class="vtable"> 
                    <?=$mandfldhtml;?><input name="desc" type="text" class="formfld" id="desc" size="20" value="<?=htmlspecialchars($pconfig['desc']);?>"> 
                  </td>
				</tr>
                <tr> 
                  <td width="22%" valign="top">&nbsp;</td>
                  <td width="78%"> <input name="Submit" type="submit" class="formbtn" value="<?=((isset($id) && $a_disk[$id]))?_SAVE:_ADD?>"> 
                    <?php if (isset($id) && $a_mount[$id]): ?>
                    <input name="id" type="hidden" value="<?=$id;?>"> 
                    <?php endif; ?>
                  </td>
                </tr>
                
                  <tr> 
                  <td width="22%" valign="top">&nbsp;</td>
                  <td width="78%"><span class="vexpl"><span class="red"><strong><?=_WARNING; ?>:<br>
                    </strong></span><?=_DISKSMOUNTEDITPHP_TEXT;?>
                    </span></td>
                </tr>
                      
                
              </table>
</form>
<?php include("fend.inc"); ?>
