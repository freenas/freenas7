#!/usr/local/bin/php
<?php
/*
	disks_raid_gmirror_edit.php
	part of FreeNAS (http://www.freenas.org)
	Copyright (C) 2005-2008 Olivier Cochard-Labbe <olivier@freenas.org>.
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

$pgtitle = array(gettext("Disks"), gettext("ZFS"), gettext("zpool"),gettext("Groups"), isset($id)?gettext("Edit"):gettext("Add"));

if (!isset($config['zfs']['groups']) || !is_array($config['zfs']['groups']['group']))
	$config['zfs']['groups']['group'] = array();

array_sort_key($config['zfs']['groups']['group'], "name");

$a_group = &$config['zfs']['groups']['group'];

$a_disk_avail = get_conf_disks_avail_groups();

if(!isset($id)) {
	if (!sizeof($a_disk_avail)) {
		$errormsg = sprintf(gettext("No more available disks. Please add new <a href=%s>disk</a> first."), 'disks_manage.php');
	}
} else {
	if(isset($id) && (!$a_group[$id])) {
		$errormsg = sprintf(gettext("Group '%d' not found."), $id);
		$a_disk_avail = array();
	}
}

$isInPool = false;
$hasId = false;

if (isset($id) && $a_group[$id]) {

	$pconfig['name'] = $a_group[$id]['name'];
	$pconfig['type'] = $a_group[$id]['type'];
	$pconfig['comment'] = $a_group[$id]['comment'];
	$pconfig['devices']['device'] = $a_group[$id]['devices']['device'];
	
	$isInPool = get_group_pool($pconfig['name']);
	
	foreach($pconfig['devices']['device'] as $device) {
		 $a_disk_avail[] = get_conf_disk($device);
	}
	array_sort_key($a_disk_avail, "name");
	
	$hasId = true;
}

if ($_POST) {
	unset($input_errors);
	
	if(!isset($id) || (isset($id) && false === $isInPool)) {
		$pconfig = $_POST;
	
		/* input validation */
		$reqdfields = explode(" ", "name");
		$reqdfieldsn = array(gettext("Group name"));
	
		do_input_validation($_POST, $reqdfields, $reqdfieldsn, &$input_errors);
	
		if (($_POST['name'] && !is_validaliasname($_POST['name']))) {
			$input_errors[] = gettext("The disk name may only consist of the characters a-z, A-Z, 0-9.");
		}
		if(!(isset($id) && $_POST['name'] == $a_group[$id]['name'])) {
			/* check if pool already exist */
			if(false !== array_search_ex($_POST['name'], $a_group, "name")) {
				$input_errors[] = gettext("This group name already exists.");
			}
		}
		switch($_POST['type'])
		{
			case "mirrorz":
			{
				if(count($_POST['device']) <  2) {
					$input_errors[] = gettext("There must be at least 2 disks in a mirror.");
				}
			}
			break;
			case "raidz":
			{
				if(count($_POST['device']) <  2) {
					$input_errors[] = gettext("There must be at least 2 disks in a raidz.");
				}
			}
			break;
			case "raidz2":
			{
				if(count($_POST['device']) <  3) {
					$input_errors[] = gettext("There must be at least 3 disks in a raidz2.");
				}
			}
			break;
			case "stripped":
			case "spares":
			default:
			{
				if(count($_POST['device']) <  1) {
					$input_errors[] = gettext("There must be at least 1 disks selected.");
				}
			}
			break;
		}
	}
	if (!$input_errors) {
		if ($hasId) {
			if(false === $isInPool) {
				$a_group[$id]['name'] = substr($_POST['name'], 0, 15); // Make sure name is only 15 chars long.
				$a_group[$id]['type'] = $_POST['type'];
				$a_group[$id]['devices']['device'] = $_POST['device'];
			}
			$a_group[$id]['comment'] = $_POST['comment'];
		} else {
			$group = array();
			$group['name'] = substr($_POST['name'], 0, 15); // Make sure name is only 15 chars long.
			$group['type'] = $_POST['type'];
			$group['devices']['device'] = $_POST['device'];
			$group['comment'] = $_POST['comment'];
			$a_group[] = $group;
		}
		write_config();
		header("Location: disks_zfs_zpool_groups.php");
		exit;
	}
}
?>
<?php include("fbegin.inc"); ?>
<table width="100%" border="0" cellpadding="0" cellspacing="0">
<tr>
	<td class="tabnavtbl">
		<ul id="tabnav">
			<li class="tabact"><a href="disks_zfs_zpool.php" title="<?=gettext("Reload page");?>" ><?=gettext("ZPool");?></a></li>
			<li class="tabinact"><a href="disks_zfs.php"><?=gettext("ZFS"); ?></a></li>
		</ul>
	</td>
</tr>
<tr>
	<td class="tabcont">
		<table width="100%" border="0" cellpadding="0" cellspacing="0">
		<tr>
			<td class="tabnavtbl">
				<ul id="tabnav">
					<li class="tabact"><a href="disks_zfs_zpool_groups.php"><?=gettext("Groups");?></a></li>
					<li class="tabinact"><a href="disks_zfs_zpool.php" title="<?=gettext("Reload page");?>" ><?=gettext("Manage Pool");?></a></li>
					<li class="tabinact"><a href="disks_zfs_zpool_tools.php"><?=gettext("Tools"); ?></a></li>
					<li class="tabinact"><a href="disks_zfs_zpool_info.php"><?=gettext("Information"); ?></a></li>
					<li class="tabinact"><a href="disks_zfs_zpool_io.php"><?=gettext("IO Status"); ?></a></li>
				</ul>
			</td>
		</tr>
		<tr>
			<td class="tabcont">
				<?php if ($errormsg) print_error_box($errormsg); ?>
				<?php if ($input_errors) print_input_errors($input_errors); ?>
				<?php if(count($a_disk_avail) != 0 || $hasId):?>
				<form action="disks_zfs_zpool_groups_edit.php" method="post" name="iform" id="iform">
				<table width="100%" border="0" cellpadding="6" cellspacing="0">
				<tr>
					<td valign="top" class="vncellreq"><?=gettext("Group name");?></td>
					<td width="78%" class="vtable">
						<input name="name" type="text" class="formfld" id="name" size="15" value="<?=htmlspecialchars($pconfig['name']);?>" <?if($hasId && (false !== $isInPool)) echo "disabled"?>>
					</td>
				</tr>
				<tr>
					<td valign="top" class="vncellreq"><?=gettext("Type");?></td>
					<td width="78%" class="vtable">
					<?
						$types = explode(" ", "strippedz mirrorz raidz raidz2 spares");
						?>
						<select name="type" <? if(false !== $isInPool) echo "disabled"; ?>>
						<?
						foreach($types as $type) {
							echo "<option value=\"".$type."\"";
							if($type == $pconfig['type']) {
								echo " selected";
							}
							echo ">".$type."</option>";
						}?>
						</select>
					</td>
				</tr>
				<tr>
					<td width="22%" valign="top" class="vncellreq"><?=gettext("Members of this volume");?></td>
					<td width="78%" class="vtable">
					<?php
					$i = 0;
					foreach ($a_disk_avail as $diskv) {
						echo "<input name='device[]' id='$i' type='checkbox' value='$diskv[devicespecialfile]'";
						if(is_array($pconfig['devices']['device']) && in_array($diskv['devicespecialfile'], $pconfig['devices']['device'])) {
							echo "checked ";
						}
						if($hasId && false !== $isInPool) {
							echo "disabled";
						}
						echo ">";
						$href = "disks_manage";
						if(isset($diskv['class']) && ($diskv['class'] == 'gmirror') || $diskv['class'] == 'gvinum' || $diskv['class'] == 'gconcat' || $diskv['class'] == 'gstripe' || $diskv['class'] == 'graid5') {
								$href="disks_raid_".$diskv['class'];
						}
						$k = get_conf_disk_index($diskv['devicespecialfile']);
						echo "<a href='${href}_edit.php?id=${k}'>";
						echo "$diskv[name]</a>";
 						echo "($diskv[size], $diskv[desc])<br>\n";
						$i++;
					}
					if (0 == $i) echo "&nbsp;";
					?>
					</td>
				</tr>
				<tr>
					<td valign="top" class="vncell"><?=gettext("Comment");?></td>
					<td width="78%" class="vtable">
						<input name="comment" type="text" class="formfld" id="comment" size="20" value="<?=htmlspecialchars($pconfig['comment']);?>">
					</td>
				</tr>
				<tr>
					<td width="22%" valign="top">&nbsp;</td>
					<td width="78%">
						<input name="Submit" type="submit" class="formbtn" value="<?=!isset($id)?gettext("Add"):gettext("Edit");?>">
						<? if(isset($id)) {?>
						<input name="id" type="hidden" value="<?=$id?>">
						<?}?>
					</td>
				</tr>
				</table>
				</form>
				<? endif;?>
			</td>
		</tr>
		</table>
	</td>
</tr>
</table>
<?php include("fend.inc");?>
