#!/usr/local/bin/php
<?php
/*
	disks_manage_init.php
	
	part of FreeNAS (http://www.freenas.org)
	Copyright (C) 2005-2006 Olivier Cochard-Labb� <olivier@freenas.org>.
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

$pgtitle = array(_DISKSPHP_NAME,_DISKSMANAGEINITPHP_NAMEDESC);

if (!is_array($config['disks']['disk']))
	$config['disks']['disk'] = array();
	
disks_sort();

$a_disk = &$config['disks']['disk'];

if ($_POST) {
	unset($input_errors);
	unset($do_format);

	/* input validation */
	$reqdfields = explode(" ", "disk type");
	$reqdfieldsn = explode(",", "Disk,Type");
	do_input_validation($_POST, $reqdfields, $reqdfieldsn, &$input_errors);

	
	if (!$input_errors)
	{
		$do_format = true;
		$disk = $_POST['disk'];
		$type = $_POST['type'];
		$diskid = $_POST['id'];
		$notinitmbr= $_POST['notinitmbr'];
		
		/* found the name in the config: Must be a better way for did that */

		$id=0;
		$i=0;
		foreach ($a_disk as $disks)
		{
			$diskname=$disks['name'];
			if (strcmp($diskname,$disk)==0)
				$id=$i;
			$i++;
		}
		
		if ($type == "ufs" || $type == "ufsgpt" || $type == "ufs_no_su" || $type == "ufsgpt_no_su")
			$a_disk[$id]['fstype'] = "ufs";
		else
			$a_disk[$id]['fstype'] = $type;
		write_config();
	}
}
if (!isset($do_format))
{
	$do_format = false;
	$disk = '';
	$type = '';
}

?>
<?php include("fbegin.inc"); ?>
<table width="100%" border="0" cellpadding="0" cellspacing="0">
  <tr><td class="tabnavtbl">
  <ul id="tabnav">
	<li class="tabinact"><a href="disks_manage.php"><?=_DISKSPHP_MANAGE; ?></a></li>
	<li class="tabact"><?=_DISKSPHP_FORMAT; ?></li>
	<li class="tabinact"><a href="disks_manage_iscsi.php"><?=_DISKSPHP_ISCSIINIT; ?></a></li>
  </ul>
  </td></tr>
  <tr> 
    <td class="tabcont">
<?php if ($input_errors) print_input_errors($input_errors); ?>
			<form action="disks_manage_init.php" method="post" name="iform" id="iform">
			  <table width="100%" border="0" cellpadding="6" cellspacing="0">
                <tr>			  		
			
			<td valign="top" class="vncellreq"><?=_DISKSPHP_DISK; ?></td>
                   
	<td class="vtable"> 
		<select name="disk" class="formfld" id="disk">
		  <?php foreach ($a_disk as $diskn): ?>
		  <option value="<?=$diskn['name'];?>"<?php if ($diskn['name'] == $disk) echo "selected";?>> 
		  <?php echo htmlspecialchars($diskn['name'] . ": " .$diskn['size'] . " (" . $diskn['desc'] . ")");				  
		  ?>
		  </option>
		  <?php endforeach; ?>
			</tr> 		
				
                  <td valign="top" class="vncellreq"><?=_DISKSPHP_FILESYSTEM; ?></td>
                  <td class="vtable"> 
                    <select name="type" class="formfld" id="type">
                      <option value="ufs" <?php if ($type == "ufs") echo "selected"; ?>>UFS with Soft Updates (use 8% space disk)</option>
                      <option value="ufs_no_su" <?php if ($type == "ufs_no_su") echo "selected"; ?>>UFS</option>
                      <option value="ufsgpt" <?php if ($type == "ufsgpt") echo "selected"; ?>>UFS (EFI/GPT) with Soft Updates (use 8% space disk)</option>
                      <option value="ufsgpt_no_su" <?php if ($type == "ufsgpt_no_su") echo "selected"; ?>>UFS (EFI/GPT)</option>
                      <option value="msdos" <?php if ($type == "msdos") echo "selected"; ?>>FAT32</option>
                      <option value="gmirror" <?php if ($type == "gmirror") echo "selected"; ?>>Software RAID: Geom mirror</option>
                      <option value="raid" <?php if ($type == "raid") echo "selected"; ?>>Software RAID: Geom Vinum</option>
                     </select>
                  </td>
                  
                  <tr>
                <td width="22%" valign="top" class="vncell"><strong><?=_DISKSPHP_NOMBR; ?><strong></td>
                		<td width="78%" class="vtable">
                		<input name="notinitmbr" id="notinitmbr" type="checkbox" value="yes" >
                		<?=_DISKSPHP_NOMBRTEXT; ?><br>
						</td>
				</tr>
                </tr>
				<tr>
				  <td width="22%" valign="top">&nbsp;</td>
				  <td width="78%">
                    <input name="Submit" type="submit" class="formbtn" value="<?=_DISKSMANAGEINITPHP_FORMATDISC;?>">
				</td>
				</tr>
				<tr>
				<td valign="top" colspan="2">
				<? if ($do_format)
				{
					echo(_DISKSMANAGEINITPHP_INITTEXT);
					echo('<pre>');
					ob_end_flush();
					
					/* Erase MBR if not checked*/
					
					if (!$notinitmbr) {
						echo "Erasing MBR\n";
						system("dd if=/dev/zero of=/dev/" . escapeshellarg($disk) . " bs=32k count=640");
						
					}
					else
						echo "Keeping the MBR\n";
					
					switch ($type)
					{
					case "ufs":
						/* Initialize disk */
						system("/sbin/fdisk -I -b /boot/mbr " . escapeshellarg($disk));
						// echo "\"fdisk: Geom not found\"is not an error message!\n";
						/* Initialise the partition (optional) */
						system("/bin/dd if=/dev/zero of=/dev/" . escapeshellarg($disk) . "s1 bs=32k count=16");
						/* Create s1 label */
						system("/sbin/bsdlabel -w " . escapeshellarg($disk) . "s1 auto");			
						// Create filesystem	
						system("/sbin/newfs -U /dev/" . escapeshellarg($disk) . "s1");
						break;
					case "ufs_no_su":
						/* Initialize disk */
						system("/sbin/fdisk -I -b /boot/mbr " . escapeshellarg($disk));
						/* Initialise the partition (optional) */
						system("/bin/dd if=/dev/zero of=/dev/" . escapeshellarg($disk) . "s1 bs=32k count=16");
						/* Create s1 label */
						system("/sbin/bsdlabel -w " . escapeshellarg($disk) . "s1 auto");			
						// Create filesystem	
						system("/sbin/newfs -m 0 /dev/" . escapeshellarg($disk) . "s1");
						break;
					case "ufsgpt":
						/* Create GPT partition table */
						system("/sbin/gpt destroy " . escapeshellarg($disk));
						system("/sbin/gpt create -f " . escapeshellarg($disk));
						system("/sbin/gpt add -t ufs " . escapeshellarg($disk));
						// Create filesystem
						system("/sbin/newfs -U /dev/" . escapeshellarg($disk) . "p1");
						break;
					case "ufsgpt_no_su":
						/* Create GPT partition table */
						system("/sbin/gpt destroy " . escapeshellarg($disk));
						system("/sbin/gpt create -f " . escapeshellarg($disk));
						system("/sbin/gpt add -t ufs " . escapeshellarg($disk));
						// Create filesystem
						system("/sbin/newfs -m 0 /dev/" . escapeshellarg($disk) . "p1");
						break;
					case "gmirror":
						/* Initialize disk */
						system("/sbin/fdisk -I -b /boot/mbr " . escapeshellarg($disk));
						/* Initialise the partition (optional) */
						system("/bin/dd if=/dev/zero of=/dev/" . escapeshellarg($disk) . "s1 bs=32k count=16");
						/* Create s1 label */
						//system("/sbin/bsdlabel -w " . escapeshellarg($disk) . "s1 auto");
						/* Delete old gmirror information */
						system("/sbin/gmirror clear /dev/" . escapeshellarg($disk));
						break;
					case "raid":
						/* Initialize disk */
						system("/sbin/fdisk -I -b /boot/mbr " . escapeshellarg($disk));
						// echo "\"fdisk: Geom not found\"is not an error message!\n";
						/* Initialise the partition (optional) */
						system("/bin/dd if=/dev/zero of=/dev/" . escapeshellarg($disk) . "s1 bs=32k count=16");
						/* Create s1 label */
						system("/sbin/bsdlabel -w " . escapeshellarg($disk) . "s1 auto");
						break;
					case "msdos":
						/* Initialize disk */
						system("/sbin/fdisk -I -b /boot/mbr " . escapeshellarg($disk));
						// echo "\"fdisk: Geom not found\"is not an error message!\n";
						/* Initialise the partition (optional) */
						system("/bin/dd if=/dev/zero of=/dev/" . escapeshellarg($disk) . "s1 bs=32k count=16");
						/* Create s1 label */
						system("/sbin/bsdlabel -w " . escapeshellarg($disk) . "s1 auto");
						// Create filesystem
						system("/sbin/newfs_msdos -F 32 /dev/" . escapeshellarg($disk) . "s1");
						break;		
					}
					
					echo('</pre>');
				}
				?>
				</td>
				</tr>
			</table>
</form>
<p><span class="vexpl"><span class="red"><strong>WARNING:<br>
                </strong></span><?=_DISKSMANAGEINITPHP_TEXT; ?>
                </span></p>
</td></tr></table>
<?php include("fend.inc"); ?>
