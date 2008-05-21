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

$pgtitle = array(gettext("Disks"), gettext("ZFS"), gettext("zpool"), gettext("Manage Pool") ,isset($id)?gettext("Edit"):gettext("Add"));

if (!isset($config['zfs']['pools']) || !is_array($config['zfs']['pools']['pool']))
	$config['zfs']['pools']['pool'] = array();

array_sort_key($config['zfs']['pools']['pool'], "name");
$a_pool = &$config['zfs']['pools']['pool'];

$a_group = &$config['zfs']['groups']['group'];
$a_group_avail = get_conf_group_avail();

if(!isset($id)) {
	if (!sizeof($a_group_avail)) {
		$errormsg = sprintf(gettext("No available groups. Please add new <a href=%s>groups</a> first."), 'disks_raid_zfs_groups.php');
	}
} else {
	$a_group_avail = $a_group;
}

if (isset($id) && $a_pool[$id]) {
	$pconfig['name'] = $a_pool[$id]['name'];
	$pconfig['groups']['group'] = $a_pool[$id]['groups']['group'];
	array_sort_key($pconfig['groups']['group'],"group");
	$pconfig['root'] = $a_pool[$id]['root'];
	$pconfig['mountpoint'] = $a_pool[$id]['mountpoint'];
}

if ($_POST) {
	unset($input_errors);
	$pconfig = $_POST;
	
	/* input validation */
	$reqdfields = explode(" ", "name");
	$reqdfieldsn = array(gettext("Raid name"));
	
	do_input_validation($_POST, $reqdfields, $reqdfieldsn, &$input_errors);
	// validate pool name
	if (($_POST['name'] && !is_validaliasname($_POST['name']))) {
		$input_errors[] = gettext("The pool name may only consist of the characters a-z, A-Z, 0-9.");
	}
	if (($_POST['name'] && preg_match("/^\d/",$_POST['name']))) {
		$input_errors[] = gettext("The pool name can't start with 0-9.");
	}
	// check if pool already exist 
	if(false !== array_search_ex($_POST['name'], $a_pool, "name")) {
		$input_errors[] = gettext("This pool name already exists.");
	}
	$group = array();
	foreach($_POST as $post => $val) {
		if(preg_match("/(.*)\d+/", $post, $matches)) {
			if($val != "none") {
				if(in_array($val, $group)) {
					$input_errors[] = gettext("You can't have the same group on a pool.");
				} else {
					$group[] = $val;
				}
			}
		}
	}
	if(count($group) == 0) {
		$input_errors[] = gettext("Select at least a group member.");
	}
	/*
	$i = 0;
	foreach($a_group as $groups) {
		foreach($group as $g) {
			if($g == $groups['name']) {
				if($groups['type'] == "spares") {
					$i++;
				}
			}
		}
	}

	if(count($group) == $i) {
		$input_errors[] = gettext("Select a group member besides a spare.");
	}
	
	$i = 0;
	foreach($a_group as $groups) {
		foreach($group as $g) {
			if($g == $groups['name']) {
				if($groups['type'] == "strippedz") {
					$i++;
				}
			}
		}
	}
	
	if($i > 1 ) {
		$input_errors[] = gettext("There can be only one stripped group member.");
	}*/
	
	/*$i=0;
	foreach($group as $g) {
		foreach($a_group as $groups) {
			if($g == $groups['name']) {
				if($groups['type'] == "strippedz") {
					$i++;
					break;
				}
			}
		}
	}
	if($i == 1) {
		$input_errors[] = gettext("The stripped group member have to be the first member of the group.");
	}*/
	
	if (!$input_errors) {
		$pool = array();
		$pool['name'] = substr($_POST['name'], 0, 15); // Make sure name is only 15 chars long.
		$pool['groups']['group'] = $group;
		$pool['root'] = $_POST['root'];
		$pool['mountpoint'] = $_POST['mountpoint'];
		
		if (isset($id) && $a_pool[$id]) {
			$a_pool[$id] = $pool;
		} else {
			$a_pool[] = $pool;
		}
		write_config();
		// Mark new added pool.
		file_put_contents($d_raid_zpool_confdirty_path, "{$pool[name]}\n", FILE_APPEND | FILE_TEXT);
		header("Location: disks_zfs_zpool.php");
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
					<li class="tabinact"><a href="disks_zfs_zpool_groups.php"><?=gettext("Groups");?></a></li>
					<li class="tabact"><a href="disks_zfs_zpool.php" title="<?=gettext("Reload page");?>" ><?=gettext("Manage Pool");?></a></li>
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
				<?php if(count($a_group_avail) != 0):?>
				<form action="disks_zfs_zpool_edit.php" method="post" name="iform" id="iform">
				<table width="100%" border="0" cellpadding="6" cellspacing="0">
				<tr>
					<td valign="top" class="vncellreq"><?=gettext("Pool name");?></td>
					<td width="78%" class="vtable">
						<input name="name" type="text" class="formfld" id="name" size="15" value="<?=htmlspecialchars($pconfig['name']);?>" <?php if (isset($id)) echo "disabled";?>/>
					</td>
				</tr>
				<tr>
					<td width="22%" valign="top" class="vncellreq"><?=gettext("Members Disks");?></td>
					<td width="78%" class="vtable">
					<?php 
					if (isset($id)) {
						$groups = $pconfig['groups']['group'];
						foreach ($groups as $g1) {
							echo "<select id='groups${i}' name='groups${i}' disabled>";
							echo "<option value='none'>".gettext("None...")."</option>";
							foreach ($groups as $g2) {
								$group = get_conf_group($g2);
								echo "<option value=".$group['name'];
								if($g1 == $g2)
									echo " selected";
								echo ">".$group['name']." - ".$group['type'];"</option>";
							}
							echo "</select><br/>";
							$i++;
						}
					} else {
						$i=0;
						foreach ($a_group_avail as $g1) {
							?>
							<select id="groups<?=$i?>" name="groups<?=$i?>">
								<option value="none"><?=gettext("None...");?></option>
							<?
								foreach ($a_group_avail as $g2) {
									?>
								<option value="<?=$g2['name']?>">
								<?=$g2['name'];?> - <?=$g2['type'];?>
								</option>
							<?
								}
							?>
							</select>
							<br/>
							<?
							$i++;
						}
						if($i == 0) echo "&nbsp;";
					}
					?>
					</td>
				</tr>
			
				<tr>
					<td width="22%" valign="top" class="vncell"><?=gettext("Root");?></td>
					<td width="78%" class="vtable">&nbsp;
					</td>
				</tr>
				<tr>
					<td width="22%" valign="top" class="vncell"><?=gettext("Mountpoint");?></td>
					<td width="78%" class="vtable">&nbsp;
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
				<?php endif;?>
			</td>
		</tr>
		</table>
	</td>
</tr>
</table>
<?php include("fend.inc");?>
