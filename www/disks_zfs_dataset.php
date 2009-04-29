#!/usr/local/bin/php
<?php
/*
	disks_zfs_dataset.php
	Copyright (c) 2008-2009 Volker Theile (votdev@gmx.de)
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
			// Process notifications
			updatenotify_process("zfsdataset", "zfsdataset_process_updatenotification");
		}
		$savemsg = get_std_save_message($retval);
		if ($retval == 0) {
			updatenotify_delete("zfsdataset");
		}
	}
}

if ($_GET['act'] === "del") {
	updatenotify_set("zfsdataset", UPDATENOTIFY_MODE_DIRTY, $_GET['uuid']);
	header("Location: disks_zfs_dataset.php");
	exit;
}

function zfsdataset_process_updatenotification($mode, $data) {
	global $config;

	$retval = 0;

	switch ($mode) {
		case UPDATENOTIFY_MODE_NEW:
			$retval = zfs_dataset_configure($data);
			break;

		case UPDATENOTIFY_MODE_MODIFIED:
			$retval = zfs_dataset_properties($data);
			break;

		case UPDATENOTIFY_MODE_DIRTY:
			zfs_dataset_destroy($data);
			$cnid = array_search_ex($data, $config['zfs']['datasets']['dataset'], "uuid");
			if (FALSE !== $cnid) {
				unset($config['zfs']['datasets']['dataset'][$cnid]);
				write_config();
			}
			break;
	}

	return $retval;
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
				<?php if (updatenotify_exists("zfsdataset")) print_config_change_box();?>
				<table width="100%" border="0" cellpadding="0" cellspacing="0">
					<tr>
						<td width="20%" class="listhdrr"><?=gettext("Pool");?></td>
						<td width="25%" class="listhdrr"><?=gettext("Name");?></td>
						<td width="45%" class="listhdrr"><?=gettext("Description");?></td>
						<td width="10%" class="list"></td>
					</tr>
					<?php foreach ($a_dataset as $datasetv):?>
					<?php $notificationmode = updatenotify_get_mode("zfsdataset", $datasetv['uuid']);?>
					<tr>
						<td class="listlr"><?=htmlspecialchars($datasetv['pool'][0]);?>&nbsp;</td>
						<td class="listr"><?=htmlspecialchars($datasetv['name']);?>&nbsp;</td>
						<td class="listbg"><?=htmlspecialchars($datasetv['desc']);?>&nbsp;</td>
						<?php if (UPDATENOTIFY_MODE_DIRTY != $notificationmode):?>
						<td valign="middle" nowrap class="list">
							<a href="disks_zfs_dataset_edit.php?uuid=<?=$datasetv['uuid'];?>"><img src="e.gif" title="<?=gettext("Edit dataset");?>" border="0"></a>&nbsp;
							<a href="disks_zfs_dataset.php?act=del&uuid=<?=$datasetv['uuid'];?>" onclick="return confirm('<?=gettext("Do you really want to delete this dataset?");?>')"><img src="x.gif" title="<?=gettext("Delete dataset");?>" border="0"></a>
						</td>
						<?php else:?>
						<td valign="middle" nowrap class="list">
							<img src="del.gif" border="0">
						</td>
						<?php endif;?>
					</tr>
					<?php endforeach;?>
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
