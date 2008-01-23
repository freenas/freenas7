#!/usr/local/bin/php
<?php
/*
	disks_raid_gvinum_edit.php
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

$pgtitle = array(gettext("Disks"),gettext("RAID"),isset($id)?gettext("Edit"):gettext("Add"));

if (!is_array($config['gvinum']['vdisk']))
	$config['gvinum']['vdisk'] = array();

array_sort_key($config['gvinum']['vdisk'], "name");

$a_raid = &$config['gvinum']['vdisk'];
$all_raid = get_conf_sraid_disks_list();
$a_disk = get_conf_disks_filtered_ex("fstype", "softraid");

if (!sizeof($a_disk)) {
	$nodisk_errors[] = gettext("You must add disks first.");
}

if (isset($id) && $a_raid[$id]) {
	$pconfig['name'] = $a_raid[$id]['name'];
	$pconfig['devicespecialfile'] = $a_raid[$id]['devicespecialfile'];
	$pconfig['type'] = $a_raid[$id]['type'];
	$pconfig['device'] = $a_raid[$id]['device'];
}

if ($_POST) {
	unset($input_errors);
	$pconfig = $_POST;

	/* input validation */
	$reqdfields = explode(" ", "name type");
	$reqdfieldsn = array(gettext("Raid name"),gettext("Type"));

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
	switch ($_POST['type'])
	{
		case 0:
			if (count($_POST['device']) < 2)
				$input_errors[] = gettext("There must be a minimum of 2 disks in a RAID 0 volume.");
			break;
		case 1:
			if (count($_POST['device']) != 2)
				$input_errors[] = gettext("There must be 2 disks in a RAID 1 volume.");
			break;
		case 5:
			if (count($_POST['device']) < 3)
				$input_errors[] = gettext("There must be a minimum of 3 disks in a RAID 5 volume.");
			break;
	}

	if (!$input_errors) {
		$raid = array();
		$raid['name'] = substr($_POST['name'], 0, 15); // Make sure name is only 15 chars long (GEOM limitation).
		$raid['type'] = $_POST['type'];
		$raid['device'] = $_POST['device'];
		$raid['desc'] = "Software gvinum RAID {$_POST['type']}";
		$raid['devicespecialfile'] = "/dev/gvinum/{$raid['name']}";

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

		header("Location: disks_raid_gvinum.php");
		exit;
	}
}
?>
<?php include("fbegin.inc"); ?>
<table width="100%" border="0" cellpadding="0" cellspacing="0">
	<tr>
		<td class="tabnavtbl">
			<ul id="tabnav">
				<li class="tabinact"><a href="disks_raid_gconcat.php"><?=gettext("JBOD"); ?> </a></li>
				<li class="tabinact"><a href="disks_raid_gstripe.php"><?=gettext("RAID 0"); ?></a></li>
				<li class="tabinact"><a href="disks_raid_gmirror.php"><?=gettext("RAID 1"); ?></a></li>
				<li class="tabinact"><a href="disks_raid_graid5.php"><?=gettext("RAID 5"); ?> </a></li>
				<li class="tabact"><a href="disks_raid_gvinum.php" title="<?=gettext("Reload page");?>" style="color:black"><?=gettext("Geom Vinum"); ?> <?=gettext("(unstable)") ;?></a></li>
			</ul>
		</td>
	</tr>
	<tr>
		<td class="tabnavtbl">
			<ul id="tabnav">
				<li class="tabact"><a href="disks_raid_gvinum.php" title="<?=gettext("Reload page");?>" style="color:black"><?=gettext("Manage RAID");?></a></li>
				<li class="tabinact"><a href="disks_raid_gvinum_tools.php"><?=gettext("Tools"); ?></a></li>
				<li class="tabinact"><a href="disks_raid_gvinum_info.php"><?=gettext("Information"); ?></a></li>
			</ul>
		</td>
	</tr>
  <tr>
    <td class="tabcont">
			<form action="disks_raid_gvinum_edit.php" method="post" name="iform" id="iform">
				<?php if ($nodisk_errors) print_input_errors($nodisk_errors); ?>
				<?php if ($input_errors) print_input_errors($input_errors); ?>
			  <table width="100%" border="0" cellpadding="6" cellspacing="0">
			    <tr>
			      <td valign="top" class="vncellreq"><?=gettext("Raid name");?></td>
			      <td width="78%" class="vtable">
			        <input name="name" type="text" class="formfld" id="name" size="15" value="<?=htmlspecialchars($pconfig['name']);?>" <?php if (isset($id)) echo "readonly";?>>
			      </td>
			    </tr>
			    <tr>
			      <td valign="top" class="vncellreq"><?=gettext("Type"); ?></td>
			      <td width="78%" class="vtable">
			        <select name="type" class="formfld" id="type" <?php if(isset($id)) echo "disabled";?>>
			          <option value="0" <?php if ($pconfig['type'] == 0) echo "selected"; ?>>RAID 0 (<?=gettext("striping");?>)</option>
			          <option value="1" <?php if ($pconfig['type'] == 1) echo "selected"; ?>>RAID 1 (<?=gettext("mirroring"); ?>)</option>
			          <option value="5" <?php if ($pconfig['type'] == 5) echo "selected"; ?>>RAID 5 (<?=gettext("rotated block-interleaved parity"); ?>)</option>
			        </select>
			      </td>
			    </tr>
			    <tr>
			      <td width="22%" valign="top" class="vncellreq"><?=gettext("Members of this volume");?></td>
			      <td width="78%" class="vtable">
			      <?php if (isset($id)) {
			      	foreach ($pconfig['device'] as $devicek => $devicev) {
			      		foreach ($a_disk as $diskv) {
			      			if ($diskv['devicespecialfile'] === $devicev) {
			      				echo "<input name='device[]' id='{$devicek}' type='checkbox' value='{$diskv[devicespecialfile]}' checked disabled>{$diskv[name]} ({$diskv[size]}, {$diskv[desc]})<br>\n";
			      				break;
									}
			      		}
							}
						} else {
			        $i = 0;
			        foreach ($a_disk as $diskv) {
			        	$display = true;
			        	foreach($all_raid as $raid) {
			            if (in_array($diskv['devicespecialfile'], (array)$raid['device'])) {
			              $display = false;
			              break;
			            }
			          }
			          if (true !== $display)
			          	continue;
			          echo "<input name='device[]' id='$i' type='checkbox' value='$diskv[devicespecialfile]'" . ((is_array($pconfig['device']) && in_array($diskv['devicespecialfile'], $pconfig['device']))?" checked":"") . ">$diskv[name] ($diskv[size], $diskv[desc])<br>\n";
			          $i++;
			        }
			        if (0 == $i) echo "&nbsp;";
			      }?>
						</td>
			    </tr>
			    <?php if (!isset($id)):?>
					<tr>
						<td width="22%" valign="top">&nbsp;</td>
						<td width="78%">
							<input name="Submit" type="submit" class="formbtn" value="<?=gettext("Add");?>">
						</td>
					</tr>
					<?php endif;?>
			  </table>
			</form>
		</td>
	</tr>
</table>
<?php include("fend.inc");?>
