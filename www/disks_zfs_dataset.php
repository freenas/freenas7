#!/usr/local/bin/php
<?php
/*
	disks_zfs_dataset.php
	Copyright (c) 2008 Volker Theile (votdev@gmx.de)
	All rights reserved.

	part of FreeNAS (http://www.freenas.org)
	Copyright (C) 2005-2008 Olivier Cochard-Labbe <olivier@freenas.org>.
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

$pgtitle = array(gettext("Disks"), gettext("ZFS"), gettext("Datasets"), gettext("Dataset"));

if (!isset($config['zfs']['datasets']) || !is_array($config['zfs']['datasets']['dataset']))
	$config['zfs']['datasets']['dataset'] = array();

array_sort_key($config['zfs']['datasets']['dataset'], "name");

$a_dataset = &$config['zfs']['datasets']['dataset'];

if ($_POST) {
	$pconfig = $_POST;

	if ($_POST['apply']) {
		$retval = 0;

		if (!file_exists($d_sysrebootreqd_path)) {
			foreach ($a_dataset as $datasetv) {
				if (is_zfs_dataset_new($datasetv)) {
					$retval |= zfs_dataset_configure($datasetv['name']);
				} else if (is_zfs_dataset_modified($datasetv)) {
					$retval |= zfs_dataset_properties($datasetv['name']);
				}
			}
		}

		$savemsg = get_std_save_message($retval);
		if ($retval == 0) {
			unlink_if_exists($d_zfsconfdirty_path);
		}
	}
}

if ($_GET['act'] === "del") {
	if ($a_dataset[$_GET['id']]) {
		zfs_dataset_destroy($a_dataset[$_GET['id']]['name']);
		unset($a_dataset[$_GET['id']]);

		write_config();

		header("Location: disks_zfs_dataset.php");
		exit;
	}
}

function is_zfs_dataset_modified($dataset) {
	global $d_zfsconfdirty_path;
	return (file_exists($d_zfsconfdirty_path) && in_array("{$dataset['pool'][0]}/{$dataset['name']}\n", file($d_zfsconfdirty_path)));
}

function is_zfs_dataset_new($dataset) {
	global $d_zfsconfdirty_path;
	return (file_exists($d_zfsconfdirty_path) && in_array("+{$dataset['pool'][0]}/{$dataset['name']}\n", file($d_zfsconfdirty_path)));
}
?>
<?php include("fbegin.inc");?>
<table width="100%" border="0" cellpadding="0" cellspacing="0">
	<tr>
		<td class="tabnavtbl">
			<ul id="tabnav">
				<li class="tabinact"><a href="disks_zfs_zpool.php"><span><?=gettext("Pools");?></span></a></li>
				<li class="tabact"><a href="disks_zfs_dataset.php" title="<?=gettext("Reload page");?>"><span><?=gettext("Datasets");?></span></a></li>
			</ul>
		</td>
	</tr>
  <tr>
		<td class="tabnavtbl">
  		<ul id="tabnav">
  			<li class="tabact"><a href="disks_zfs_dataset.php" title="<?=gettext("Reload page");?>"><span><?=gettext("Dataset");?></span></a></li>
				<li class="tabinact"><a href="disks_zfs_dataset_info.php"><span><?=gettext("Information");?></span></a></li>
  		</ul>
  	</td>
	</tr>
  <tr>
    <td class="tabcont">
			<form action="disks_zfs_dataset.php" method="post">
				<?php if ($savemsg) print_info_box($savemsg);?>
				<?php if (file_exists($d_zfsconfdirty_path)) print_config_change_box();?>
				<?php if (file_exists($d_sysrebootreqd_path)) print_info_box(get_std_save_message(0));?>
				<table width="100%" border="0" cellpadding="0" cellspacing="0">
					<tr>
						<td width="20%" class="listhdrr"><?=gettext("Pool");?></td>
						<td width="25%" class="listhdrr"><?=gettext("Name");?></td>
						<td width="45%" class="listhdrr"><?=gettext("Description");?></td>
						<td width="10%" class="list"></td>
					</tr>
					<?php $i = 0; foreach ($a_dataset as $datasetv):?>
					<tr>
						<td class="listlr"><?=htmlspecialchars($datasetv['pool'][0]);?>&nbsp;</td>
						<td class="listr"><?=htmlspecialchars($datasetv['name']);?>&nbsp;</td>
						<td class="listbg"><?=htmlspecialchars($datasetv['desc']);?>&nbsp;</td>
						<td valign="middle" nowrap class="list">
							<a href="disks_zfs_dataset_edit.php?id=<?=$i;?>"><img src="e.gif" title="<?=gettext("Edit dataset");?>" border="0"></a>&nbsp;
							<a href="disks_zfs_dataset.php?act=del&id=<?=$i;?>" onclick="return confirm('<?=gettext("Do you really want to delete this dataset?");?>')"><img src="x.gif" title="<?=gettext("Delete dataset");?>" border="0"></a>
						</td>
					</tr>
					<?php $i++; endforeach;?>
					<tr>
						<td class="list" colspan="3"></td>
						<td class="list">
							<a href="disks_zfs_dataset_edit.php"><img src="plus.gif" title="<?=gettext("Add dataset");?>" border="0"></a>
						</td>
					</tr>
				</table>
			</form>
		</td>
	</tr>
</table>
<?php include("fend.inc");?>
