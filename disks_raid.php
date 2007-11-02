#!/usr/local/bin/php
<?php
/*
	disks_raid.php
	Copyright © 2006-2007 Volker Theile (votdev@gmx.de)
	All rights reserved.

	part of FreeNAS (http://www.freenas.org)
	Copyright (C) 2005-2007 Olivier Cochard-Labbe <olivier@freenas.org>.
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

$pgtitle = array(gettext("Disks"),gettext("RAID"));

$araidlist = get_sraid_disks_list();

if ($_POST) {
	$pconfig = $_POST;

	if ($_POST['apply']) {
		$retval = 0;
		$savemsg = get_std_save_message($retval);
		if ($retval == 0) {
			if (file_exists($d_raidconfdirty_path))
				unlink($d_raidconfdirty_path);
		}
	}
}

if ($_GET['act'] == "del") {
	if ($a_disk_conf[$_GET['id']]) {
		write_config();
		touch($d_raidconfdirty_path);
		header("Location: disks_raid.php");
		exit;
	}
}
?>
<?php include("fbegin.inc"); ?>
<form action="disks_manage.php" method="post">
<?php if ($savemsg) print_info_box($savemsg); ?>
<?php if (file_exists($d_raidconfdirty_path)): ?><p>
<?php print_info_box_np(gettext("The disk list has been changed.<br>You must apply the changes in order for them to take effect."));?><br>
<input name="apply" type="submit" class="formbtn" id="apply" value="<?=gettext("Apply changes");?>"></p>
<?php endif; ?>
<table width="100%" border="0" cellpadding="0" cellspacing="0">
	<tr>
		<td width="20%" class="listhdrr"><?=gettext("Volume Name");?></td>
		<td width="30%" class="listhdrr"><?=gettext("Type");?></td>
		<td width="20%" class="listhdrr"><?=gettext("Size");?></td>
		<td width="20%" class="listhdrr"><?=gettext("Status");?></td>
		<td width="10%" class="list"></td>
	</tr>
  <?php $i = 0; foreach ($araidlist as $raid):?>
	<tr>
		<td class="listlr"><?=htmlspecialchars($raid['name']);?></td>
		<td class="listr"><?=htmlspecialchars($raid['type']);?></td>
		<td class="listr"><?=htmlspecialchars($raid['size']);?></td>
		<td class="listbg"><?=htmlspecialchars($raid['desc']);?></td>
		<td valign="middle" nowrap class="list"> <a href="disks_raid_edit.php?id=<?=$i;?>"><img src="e.gif" title="<?=gettext("Edit RAID");?>" width="17" height="17" border="0"></a>&nbsp;<a href="disks_manage.php?act=del&id=<?=$i;?>" onclick="return confirm('<?=gettext("Do you really want to delete this raid volume? All elements that still use it will become invalid (e.g. share)!");?>')"><img src="x.gif" title="<?=gettext("Delete RAID");?>" width="17" height="17" border="0"></a></td>
	</tr>
	<?php $i++; endforeach;?>
	<tr> 
		<td class="list" colspan="4"></td>
		<td class="list"> <a href="disks_raid_edit.php"><img src="plus.gif" title="<?=gettext("Add RAID"); ?>" width="17" height="17" border="0"></a></td>
	</tr>
</table>
</form>
<?php include("fend.inc");?>
