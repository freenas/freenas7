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

$pgtitle = array(gettext("Disks"), gettext("ZFS"), gettext("zpool"),gettext("Groups"));

if (!isset($config['zfs']['groups']) || !is_array($config['zfs']['groups']['group']))
	$config['zfs']['groups']['group'] = array();

array_sort_key($config['zfs']['groups']['group'], "name");
$a_group = &$config['zfs']['groups']['group'];

$a_disk_avail = get_conf_disks_avail_groups();

if ($_GET['act'] == "del") {
	unset($errormsg);
	if ($a_group[$_GET['id']]) {
		$pool = get_group_pool($a_group[$_GET['id']]['name']);
		if(false !== $pool) {
			$errormsg = sprintf(gettext("Group '%s' is on pool '%s'."), $a_group[$_GET['id']]['name'], $pool['name']);
		} else {
			unset($a_group[$_GET['id']]);
			write_config();
			header("Location: disks_zfs_zpool_groups.php");
			exit;
		}
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
				<form action="disks_zfs_zpool_edit.php" method="post" name="iform" id="iform">
					<?php if ($errormsg) print_error_box($errormsg); ?>
					<?php if ($input_errors) print_input_errors($input_errors); ?>
					<table width="100%" border="0" cellpadding="6" cellspacing="0">
					<tr>
						<td width="20%" class="listhdrr"><?=gettext("Group Name");?></td>
						<td width="15%" class="listhdrr"><?=gettext("Type");?></td>
						<td width="55%" class="listhdrr"><?=gettext("Comment");?></td>
						<td width="10%" class="list"></td>
					</tr>
					<?php if(isset($a_group) && is_array($a_group)):?>
					<?php $i = 0; foreach ($a_group as $group):?>
					 <tr>
						<td class="listlr"><?=htmlspecialchars($group['name']);?></td>
						<td class="listr"><?=$group['type'];?>&nbsp;</td>
						<td class="listbg"><?=$group['comment'];?>&nbsp;</td>
            					<td valign="middle" nowrap class="list">
            					<a href="disks_zfs_zpool_groups_edit.php?id=<?=$i;?>"><img src="e.gif" title="<?=gettext("Edit Group"); ?>" width="17" height="17" border="0"></a>&nbsp;
            					<? if(false !== get_group_pool($group['name'])) {?>
            						<? $pool = get_group_pool($group['name']); ?>
							<img src="x_d.gif" title="<?=sprintf(gettext("Group is on pool '%s'."), $pool['name']);?>" width="17" height="17" border="0">
						<? } else { ?>
							<a href="disks_zfs_zpool_groups.php?act=del&id=<?=$i;?>" onclick="return confirm('<?=gettext("Do you really want to delete this group?") ;?>')"><img src="x.gif" title="<?=gettext("Delete Group") ;?>" width="17" height="17" border="0"></a>
						<? } ?>
						</td>
					</tr>
					<?php $i++; endforeach;?>
					<?php endif;?>
					<tr>
            					<td class="list" colspan="3">&nbsp;</td>
            					<td class="list">
            					<? if(count($a_disk_avail) == 0) { ?>
							<img src="plus_d.gif" title="<?=gettext("No disks available");?>" width="17" height="17" border="0">
						<?} else {?>
							<a href="disks_zfs_zpool_groups_edit.php"><img src="plus.gif" title="<?=gettext("Add Group");?>" width="17" height="17" border="0"></a>
						<?}?>
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
