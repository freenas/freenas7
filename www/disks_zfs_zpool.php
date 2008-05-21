#!/usr/local/bin/php
<?php
/*
	disks_raid_gmirror.php
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

$pgtitle = array(gettext("Disks"), gettext("ZFS"), gettext("zpool"), gettext("Manage Pool"));

if (!isset($config['zfs']['pools']) || !is_array($config['zfs']['pools']['pool']))
	$config['zfs']['pools']['pool'] = array();

array_sort_key($config['zfs']['pools']['pool'], "name");
$a_pool = &$config['zfs']['pools']['pool'];

$a_group_avail = get_conf_group_avail();

if ($_POST) {

	$pconfig = $_POST;

	if ($_POST['apply']) {
		$retval = 0;
		if (!file_exists($d_sysrebootreqd_path)) {
			foreach ($a_pool as $pool) {
				if (is_modified($pool['name'])) {
					$retval |= disks_raid_zpool_configure($pool['name']);
				}
			}
		}
		$savemsg = get_std_save_message($retval);
		if ($retval == 0) {
			unlink_if_exists($d_raid_zpool_confdirty_path);
		}
	}
}

if ($_GET['act'] == "del") {
// 	unset($errormsg);
	if ($a_pool[$_GET['id']]) {
		// Check if disk is mounted.
// 		if(0 == disks_ismounted_ex($a_raid[$_GET['id']]['devicespecialfile'], "devicespecialfile")) {
			disks_raid_zpool_destroy($a_pool[$_GET['id']]['name']);
			unset($a_pool[$_GET['id']]);
			write_config();
			header("Location: disks_zfs_zpool.php");
			exit;
// 		} else {
// 			$errormsg = sprintf( gettext("The RAID volume is currently mounted! Remove the <a href=%s>mount point</a> first before proceeding."), "disks_mount.php");
// 		}
	}
}

function is_modified($name) {
	global $d_raid_zpool_confdirty_path;
	return (file_exists($d_raid_zpool_confdirty_path) && in_array("{$name}\n", file($d_raid_zpool_confdirty_path)));
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
				<form action="disks_zfs_zpool.php" method="post" name="iform" id="iform">
					<?php if ($errormsg) print_error_box($errormsg); ?>
					<?php if ($savemsg) print_info_box($savemsg); ?>
					<?php if (file_exists($d_raid_zpool_confdirty_path)): ?><p>
					<?php print_info_box_np(gettext("The Raid configuration has been changed.<br>You must apply the changes in order for them to take effect."));?><br>
						<input name="apply" type="submit" class="formbtn" id="apply" value="<?=gettext("Apply changes"); ?>"></p>
					<?php endif; ?>
					<table width="100%" border="0" cellpadding="6" cellspacing="0">
					<tr>
						<td width="20%" class="listhdrr"><?=gettext("Name");?></td>
						<td width="10%" class="listhdrr"><?=gettext("Size");?></td>
						<td width="10%" class="listhdrr"><?=gettext("Used");?></td>
						<td width="10%" class="listhdrr"><?=gettext("Avail");?></td>
						<td width="10%" class="listhdrr"><?=gettext("Cap");?></td>
						<td width="10%" class="listhdrr"><?=gettext("Health");?></td>
						<td width="20%" class="listhdrr"><?=gettext("AltRoot");?></td>
						<td width="10%" class="list"></td>
					</tr>
					<?php $zpoolstatus = get_zfs_pool_list();?>
					<?php $i = 0; foreach ($a_pool as $pool):?>
					<?
						$altroot = $cap = $avail = $used = $size = gettext("Unknown");
						$health = gettext("STOPPED");
					?>
					<?php if (is_array($zpoolstatus) && array_key_exists($pool['name'], $zpoolstatus)):?>
					<?php
						$size = $zpoolstatus[$pool['name']]['size'];
						$used = $zpoolstatus[$pool['name']]['used'];
						$avail = $zpoolstatus[$pool['name']]['avail'];
						$cap = $zpoolstatus[$pool['name']]['cap'];
						$health = $zpoolstatus[$pool['name']]['health'];
						$altroot = $zpoolstatus[$pool['name']]['altroot'];
					?>
					<?php endif;?>
					<tr>
						<td class="listlr"><?=htmlspecialchars($pool['name']);?></td>
						<td class="listr"><?=$size;?>&nbsp;</td>
						<td class="listr"><?=$used;?>&nbsp;</td>
						<td class="listr"><?=$avail;?>&nbsp;</td>
						<td class="listr"><?=$cap;?>&nbsp;</td>
						<td class="listbg"><a href="disks_raid_zfs_info.php?id=<?=$i?>"><?=$health;?></a>&nbsp;</td>
						<td class="listr"><?=$altroot;?>&nbsp;</td>	
						<td valign="middle" nowrap class="list">
							<a href="disks_zfs_zpool_edit.php?id=<?=$i;?>"><img src="e.gif" title="<?=gettext("Edit RAID"); ?>" width="17" height="17" border="0"></a>&nbsp;
							<a href="disks_zfs_zpool.php?act=del&id=<?=$i;?>" onclick="return confirm('<?=gettext("Do you really want to delete this pool? All elements that still use it will become invalid (e.g. share)!") ;?>')"><img src="x.gif" title="<?=gettext("Delete pool") ;?>" width="17" height="17" border="0"></a>
						</td>
					</tr>
				
					<?php $i++;endforeach;?>
					<tr>
						<td class="list" colspan="7">&nbsp;</td>
						<td class="list">
					<?php if(count($a_group_avail) == 0) {?>
						<img src="plus_d.gif" title="<?=gettext("No groups available");?>" width="17" height="17" border="0">
					<? } else {  ?>
							<a href="disks_zfs_zpool_edit.php"><img src="plus.gif" title="<?=gettext("Add Pool");?>" width="17" height="17" border="0"></a>
					<?php }?>
						</td>
					</tr>
					</table>
				</form>
			</td>
		</tr>
		</table>
	</td>
</tr>
</table>
<?php include("fend.inc");?>
