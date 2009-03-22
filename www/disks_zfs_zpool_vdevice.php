#!/usr/local/bin/php
<?php
/*
	disks_zfs_zpool_vdevice.php
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

$pgtitle = array(gettext("Disks"), gettext("ZFS"), gettext("Pools"), gettext("Virtual device"));

if (!isset($config['zfs']['vdevices']) || !is_array($config['zfs']['vdevices']['vdevice']))
	$config['zfs']['vdevices']['vdevice'] = array();

array_sort_key($config['zfs']['vdevices']['vdevice'], "name");

$a_vdevice = &$config['zfs']['vdevices']['vdevice'];

if ($_GET['act'] === "del") {
	unset($errormsg);
	if ($a_vdevice[$_GET['id']]) {
		mwexec("zpool destroy {$a_vdevice[$_GET['id']]['name']}");
		unset($a_vdevice[$_GET['id']]);
		write_config();
		header("Location: disks_zfs_zpool_vdevice.php");
		exit;
	}
}
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
				<li class="tabact"><a href="disks_zfs_zpool_vdevice.php" title="<?=gettext("Reload page");?>"><span><?=gettext("Virtual device");?></span></a></li>
				<li class="tabinact"><a href="disks_zfs_zpool.php"><span><?=gettext("Pool");?></span></a></li>
				<li class="tabinact"><a href="disks_zfs_zpool_tools.php"><span><?=gettext("Tools");?></span></a></li>
				<li class="tabinact"><a href="disks_zfs_zpool_info.php"><span><?=gettext("Information");?></span></a></li>
				<li class="tabinact"><a href="disks_zfs_zpool_io.php"><span><?=gettext("I/O statistics");?></span></a></li>
  		</ul>
  	</td>
	</tr>
  <tr>
    <td class="tabcont">
			<form action="disks_zfs_zpool_vdevice.php" method="post">
				<?php if ($savemsg) print_info_box($savemsg); ?>
				<?php if (file_exists($d_sysrebootreqd_path)) print_info_box(get_std_save_message(0));?>
				<table width="100%" border="0" cellpadding="0" cellspacing="0">
					<tr>
						<td width="20%" class="listhdrr"><?=gettext("Name");?></td>
						<td width="15%" class="listhdrr"><?=gettext("Type");?></td>
						<td width="55%" class="listhdrr"><?=gettext("Comment");?></td>
						<td width="10%" class="list"></td>
					</tr>
				  <?php $i = 0; foreach ($a_vdevice as $vdevicev):?>
					<tr>
						<td class="listlr"><?=htmlspecialchars($vdevicev['name']);?></td>
						<td class="listr"><?=htmlspecialchars($vdevicev['type']);?></td>
						<td class="listr"><?=htmlspecialchars($vdevicev['desc']);?>&nbsp;</td>
						<td valign="middle" nowrap class="list">
							<a href="disks_zfs_zpool_vdevice_edit.php?id=<?=$i;?>"><img src="e.gif" title="<?=gettext("Edit device");?>" border="0"></a>&nbsp;
							<a href="disks_zfs_zpool_vdevice.php?act=del&id=<?=$i;?>" onclick="return confirm('<?=gettext("Do you really want to delete this device?");?>')"><img src="x.gif" title="<?=gettext("Delete device");?>" border="0"></a>
						</td>
					</tr>
					<?php $i++; endforeach;?>
					<tr>
						<td class="list" colspan="3"></td>
						<td class="list">
							<a href="disks_zfs_zpool_vdevice_edit.php"><img src="plus.gif" title="<?=gettext("Add device");?>" border="0"></a>
						</td>
					</tr>
				</table>
			</form>
		</td>
	</tr>
</table>
<?php include("fend.inc");?>
