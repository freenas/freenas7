#!/usr/local/bin/php
<?php
/*
	disks_raid_gmirror_edit.php
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

$pgtitle = array(gettext("Disks"),gettext("RAID"),isset($id)?gettext("Edit"):gettext("Add"));

if (!is_array($config['gmirror']['vdisk']))
	$config['gmirror']['vdisk'] = array();

array_sort_key($config['gmirror']['vdisk'], "name");

$a_raid = &$config['gmirror']['vdisk'];
$all_raid = array_merge((array)$config['graid5']['vdisk'],(array)$config['gmirror']['vdisk'],(array)$config['gvinum']['vdisk'],(array)$config['gstripe']['vdisk'],(array)$config['gconcat']['vdisk']);
$a_disk = get_fstype_disks_list("softraid");

if (!sizeof($a_disk)) {
	$nodisk_errors[] = gettext("You must add disks first.");
}

if (isset($id) && $a_raid[$id]) {
	$pconfig['name'] = $a_raid[$id]['name'];
	$pconfig['fullname'] = $a_raid[$id]['fullname'];
	$pconfig['type'] = $a_raid[$id]['type'];
	$pconfig['balance'] = $a_raid[$id]['balance'];
	$pconfig['diskr'] = $a_raid[$id]['diskr'];
}

if ($_POST) {
	unset($input_errors);
	$pconfig = $_POST;

	/* input validation */
	$reqdfields = explode(" ", "name");
	$reqdfieldsn = array(gettext("Raid name"));

	do_input_validation($_POST, $reqdfields, $reqdfieldsn, &$input_errors);

	if (($_POST['name'] && !is_validaliasname($_POST['name']))) {
		$input_errors[] = gettext("The device name may only consist of the characters a-z, A-Z, 0-9.");
	}

	/* check for name conflicts */
	foreach ($a_raid as $raid) {
		if (isset($id) && ($a_raid[$id]) && ($a_raid[$id] === $raid))
			continue;

		if ($raid['name'] == $_POST['name']) {
			$input_errors[] = gettext("This device already exists in the raid volume list.");
			break;
		}
	}

	/* check the number of RAID disk for volume */
	if (count($_POST['diskr']) != 2)
		$input_errors[] = gettext("There must be 2 disks in a RAID 1 volume.");

	if (!$input_errors) {
		$raid = array();
		$raid['name'] = $_POST['name'];
		$raid['balance'] = $_POST['balance'];
		$raid['type'] = 1;
		$raid['diskr'] = $_POST['diskr'];
		$raid['desc'] = "Software gmirror RAID 1";
		$raid['fullname'] = "/dev/mirror/{$raid['name']}";

		if (isset($id) && $a_raid[$id])
			$a_raid[$id] = $raid;
		else
			$a_raid[] = $raid;

   	$fd = @fopen("$d_raidconfdirty_path", "a");
   	if (!$fd) {
   		echo gettext("ERR Could not save RAID configuration.\n");
   		exit(0);
   	}
   	fwrite($fd, "$raid[name]\n");
   	fclose($fd);

		write_config();

		header("Location: disks_raid_gmirror.php");
		exit;
	}
}
?>
<?php include("fbegin.inc"); ?>
<table width="100%" border="0" cellpadding="0" cellspacing="0">
  <tr>
		<td class="tabnavtbl">
		  <ul id="tabnav">
				<li class="tabinact"><a href="disks_raid_gconcat.php"><?=gettext("JBOD"); ?></a></li>
				<li class="tabinact"><a href="disks_raid_gstripe.php"><?=gettext("RAID 0"); ?> </a></li>
				<li class="tabact"><a href="disks_raid_gmirror.php" title="<?=gettext("Reload page");?>" style="color:black"><?=gettext("RAID 1");?></a></li>
				<li class="tabinact"><a href="disks_raid_graid5.php"><?=gettext("RAID 5"); ?></a></li>
				<li class="tabinact"><a href="disks_raid_gvinum.php"><?=gettext("Geom Vinum"); ?> <?=gettext("(unstable)") ;?> </a></li>
		  </ul>
	  </td>
	</tr>
  <tr>
		<td class="tabnavtbl">
		  <ul id="tabnav">
				<li class="tabact"><a href="disks_raid_gmirror.php" title="<?=gettext("Reload page");?>" style="color:black"><?=gettext("Manage RAID");?></a></li>
				<li class="tabinact"><a href="disks_raid_gmirror_tools.php"><?=gettext("Tools"); ?></a></li>
				<li class="tabinact"><a href="disks_raid_gmirror_info.php"><?=gettext("Information"); ?></a></li>
		  </ul>
	  </td>
	</tr>
  <tr>
    <td class="tabcont">
			<form action="disks_raid_gmirror_edit.php" method="post" name="iform" id="iform">
				<?php if ($nodisk_errors) print_input_errors($nodisk_errors); ?>
				<?php if ($input_errors) print_input_errors($input_errors); ?>
			  <table width="100%" border="0" cellpadding="6" cellspacing="0">
			    <tr>
			      <td valign="top" class="vncellreq"><?=gettext("Raid name");?></td>
			      <td width="78%" class="vtable">
			        <input name="name" type="text" class="formfld" id="name" size="20" value="<?=htmlspecialchars($pconfig['name']);?>" <?php if (isset($id)) echo "readonly";?>>
			      </td>
			    </tr>
			    <tr>
			      <td width="22%" valign="top" class="vncellreq"><?=gettext("Type"); ?></td>
			      <td width="78%" class="vtable">
			      RAID 1 (<?=gettext("mirroring"); ?>)
			      </td>
			    </tr>
			    <tr>
			      <td width="22%" valign="top" class="vncellreq"><?=gettext("Balance algorithm"); ?></td>
			      <td width="78%" class="vtable">
			        <select name="balance" class="formfld">
			        <?php $balvals = array("round-robin"=>"Round-robin read","split"=>"Split request", "load"=>"Read from lowest load"); ?>
			        <?php foreach ($balvals as $balval => $balname): ?>
			          <option value="<?=$balval;?>" <?php if($pconfig['balance'] == $balval) echo 'selected';?>><?=htmlspecialchars($balname);?></option>
			        <?php endforeach; ?>
			        </select><br>
			        <?=gettext("Select your read balance algorithm.");?></td>
			    </tr>
			    <tr>
			      <td width="22%" valign="top" class="vncellreq"><?=gettext("Members of this volume");?></td>
			      <td width="78%" class="vtable">
			      <?
			        $i=0;
			        $disable_script="";
			        foreach ($a_disk as $diskv) {
			          $r_name="";
			          foreach($all_raid as $raid) {
			            if (in_array($diskv['fullname'],(array)$raid['diskr'])) {
			              $r_name=$raid['name'];
			              if ($r_name!=$pconfig['name']) $disable_script.="document.getElementById($i).disabled=1;\n";
			              break;
			            }
			          }
			          echo "<input name='diskr[]' id='$i' type='checkbox' value='$diskv[fullname]'".
			               ((is_array($pconfig['diskr']) && in_array($diskv['fullname'],$pconfig['diskr']))?" checked":"").
			               ">$diskv[name] ($diskv[size], $diskv[desc])".(($r_name)?" - assigned to $r_name":"")."</option><br>\n";
			          $i++;
			        }
			        if ($disable_script) echo "<script language='javascript'><!--\n$disable_script--></script>\n";
			      ?>
			      <?php if (0 == $i):?>&nbsp;<?php endif;?>
						</td>
			    </tr>
			    <tr>
			      <td width="22%" valign="top">&nbsp;</td>
			      <td width="78%"> <input name="Submit" type="submit" class="formbtn" value="<?=(isset($id))?gettext("Save"):gettext("Add");?>">
			        <?php if (isset($id) && $a_raid[$id]): ?>
			        <input name="id" type="hidden" value="<?=$id;?>">
			        <?php endif; ?>
			      </td>
			    </tr>
			  </table>
			</form>
		</td>
	</tr>
</table>
<?php include("fend.inc");?>
