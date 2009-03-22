#!/usr/local/bin/php
<?php
/*
	disks_zfs_zpool.php
	Copyright (c) 2008-2009 Volker Theile (votdev@gmx.de)
	Copyright (c) 2008 Nelson Silva
	All rights reserved.

	part of FreeNAS (http://www.freenas.org)
	Copyright (C) 2005-2009 Olivier Cochard-Labbe <olivier@freenas.org>.
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
require("zfs.inc");

$pgtitle = array(gettext("Disks"), gettext("ZFS"), gettext("Pools"), gettext("Pool"));

if (!isset($config['zfs']['pools']) || !is_array($config['zfs']['pools']['pool']))
	$config['zfs']['pools']['pool'] = array();

array_sort_key($config['zfs']['pools']['pool'], "name");

$a_pool = &$config['zfs']['pools']['pool'];

if ($_POST) {
	$pconfig = $_POST;

	if ($_POST['apply']) {
		$retval = 0;

		if (!file_exists($d_sysrebootreqd_path)) {
			foreach ($a_pool as $poolv) {
				if (is_zfs_zpool_modified($poolv['name'])) {
					$retval |= zfs_zpool_configure($poolv['name']);
				}
			}
		}

		$savemsg = get_std_save_message($retval);
		if ($retval == 0) {
			unlink_if_exists($d_zpoolconfdirty_path);
		}
	}
}

if ($_GET['act'] === "del") {
	if ($a_pool[$_GET['id']]) {
		zfs_zpool_destroy($a_pool[$_GET['id']]['name']);
		unset($a_pool[$_GET['id']]);

		write_config();

		header("Location: disks_zfs_zpool.php");
		exit;
	}
}

function is_zfs_zpool_modified($name) {
	global $d_zpoolconfdirty_path;
	return (file_exists($d_zpoolconfdirty_path) && in_array("{$name}\n", file($d_zpoolconfdirty_path)));
}

$a_poolstatus = zfs_get_pool_list();
?>
<?php include("fbegin.inc");?>
<table width="100%" border="0" cellpadding="0" cellspacing="0">
	<tr>
		<td class="tabnavtbl">
			<ul id="tabnav">
				<li class="tabact"><a href="disks_zfs_zpool.php" title="<?=gettext("Reload page");?>"><span><?=gettext("Pools");?></span></a></li>
				<li class="tabinact"><a href="disks_zfs_dataset.php"><span><?=gettext("Datasets");?></span></a></li>
			</ul>
		</td>
	</tr>
  <tr>
		<td class="tabnavtbl">
  		<ul id="tabnav">
  			<li class="tabinact"><a href="disks_zfs_zpool_vdevice.php"><span><?=gettext("Virtual device");?></span></a></li>
				<li class="tabact"><a href="disks_zfs_zpool.php" title="<?=gettext("Reload page");?>"><span><?=gettext("Pool");?></span></a></li>
				<li class="tabinact"><a href="disks_zfs_zpool_tools.php"><span><?=gettext("Tools");?></span></a></li>
				<li class="tabinact"><a href="disks_zfs_zpool_info.php"><span><?=gettext("Information");?></span></a></li>
				<li class="tabinact"><a href="disks_zfs_zpool_io.php"><span><?=gettext("I/O statistics");?></span></a></li>
  		</ul>
  	</td>
	</tr>
  <tr>
    <td class="tabcont">
			<form action="disks_zfs_zpool.php" method="post">
				<?php if ($savemsg) print_info_box($savemsg);?>
				<?php if (file_exists($d_zpoolconfdirty_path)) print_config_change_box();?>
				<?php if (file_exists($d_sysrebootreqd_path)) print_info_box(get_std_save_message(0));?>
				<table width="100%" border="0" cellpadding="0" cellspacing="0">
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
					<?php $i = 0; foreach ($a_pool as $poolv):?>
					<?php
						$altroot = $cap = $avail = $used = $size = gettext("Unknown");
						$health = gettext("STOPPED");
						if (is_array($a_poolstatus) && array_key_exists($poolv['name'], $a_poolstatus)) {
							$size = $a_poolstatus[$poolv['name']]['size'];
							$used = $a_poolstatus[$poolv['name']]['used'];
							$avail = $a_poolstatus[$poolv['name']]['avail'];
							$cap = $a_poolstatus[$poolv['name']]['cap'];
							$health = $a_poolstatus[$poolv['name']]['health'];
							$altroot = $a_poolstatus[$poolv['name']]['altroot'];
						}
					?>
					<tr>
						<td class="listlr"><?=htmlspecialchars($poolv['name']);?>&nbsp;</td>
						<td class="listr"><?=$size;?>&nbsp;</td>
						<td class="listr"><?=$used;?>&nbsp;</td>
						<td class="listr"><?=$avail;?>&nbsp;</td>
						<td class="listr"><?=$cap;?>&nbsp;</td>
						<td class="listbg"><a href="disks_zfs_zpool_info.php?pool=<?=$poolv['name']?>"><?=$health;?></a>&nbsp;</td>
						<td class="listr"><?=$altroot;?>&nbsp;</td>	
						<td valign="middle" nowrap class="list">
							<a href="disks_zfs_zpool_edit.php?id=<?=$i;?>"><img src="e.gif" title="<?=gettext("Edit pool");?>" border="0"></a>&nbsp;
							<a href="disks_zfs_zpool.php?act=del&id=<?=$i;?>" onclick="return confirm('<?=gettext("Do you really want to delete this pool?");?>')"><img src="x.gif" title="<?=gettext("Delete pool");?>" border="0"></a>
						</td>
					</tr>
					<?php $i++; endforeach;?>
					<tr>
						<td class="list" colspan="7"></td>
						<td class="list">
							<a href="disks_zfs_zpool_edit.php"><img src="plus.gif" title="<?=gettext("Add pool");?>" border="0"></a>
						</td>
					</tr>
				</table>
			</form>
		</td>
	</tr>
</table>
<?php include("fend.inc");?>
