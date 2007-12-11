#!/usr/local/bin/php
<?php
/*
	disks_raid_gvinum.php
	part of FreeNAS (http://www.freenas.org)
	Copyright (C) 2005-2007 Olivier Cochard-Labbé <olivier@freenas.org>.
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

$pgtitle = array(gettext("Disks"),gettext("Geom Vinum"),gettext("Manage RAID"));

if (!is_array($config['gvinum']['vdisk']))
	$config['gvinum']['vdisk'] = array();

array_sort_key($config['gvinum']['vdisk'], "name");

$a_raid = &$config['gvinum']['vdisk'];

if ($_POST) {
	$pconfig = $_POST;

	if ($_POST['apply']) {
		$retval = 0;
		if (!file_exists($d_sysrebootreqd_path)) {
			config_lock();
			$retval |= disks_raid_gvinum_configure();
			config_unlock();
			write_config();
		}
		$savemsg = get_std_save_message($retval);
		if ($retval == 0) {
			if (file_exists($d_raidconfdirty_path))
				unlink($d_raidconfdirty_path);
		}
	}
}

if ($_GET['act'] == "del") {
	unset($errormsg);
	if ($a_raid[$_GET['id']]) {
		// Check if disk is mounted.
		if(0 == disks_ismounted_ex($a_raid[$_GET['id']]['devicespecialfile'], "devicespecialfile")) {
			$raidname=$a_raid[$_GET['id']]['name'];
			disks_raid_gvinum_delete($raidname);
			unset($a_raid[$_GET['id']]);
			write_config();
			touch($d_raidconfdirty_path);
			header("Location: disks_raid_gvinum.php");
			exit;
		} else {
			$errormsg = sprintf( gettext("The RAID volume is currently mounted! Remove the <a href=%s>mount point</a> first before proceeding."), "disks_mount.php");
		}
	}
}
?>
<?php include("fbegin.inc");?>
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
			<form action="disks_raid_gvinum.php" method="post">
				<?php if ($errormsg) print_error_box($errormsg); ?>
				<?php if ($savemsg) print_info_box($savemsg); ?>
				<?php if (file_exists($d_raidconfdirty_path)): ?><p>
				<?php print_info_box_np(gettext("The Raid configuration has been changed.<br>You must apply the changes in order for them to take effect."));?><br>
				<input name="apply" type="submit" class="formbtn" id="apply" value="<?=gettext("Apply changes"); ?>"></p>
				<?php endif; ?>
				<table width="100%" border="0" cellpadding="0" cellspacing="0">
					<tr>
						<td width="25%" class="listhdrr"><?=gettext("Volume Name");?></td>
						<td width="25%" class="listhdrr"><?=gettext("Type");?></td>
						<td width="20%" class="listhdrr"><?=gettext("Size");?></td>
						<td width="20%" class="listhdrr"><?=gettext("Status");?></td>
						<td width="10%" class="list"></td>
					</tr>
					<?php $raidstatus = get_gvinum_disks_list();?>
					<?php $i = 0; foreach ($a_raid as $raid):?>
					<?php
          $size = gettext("Unknown");
          $status = gettext("Stopped");
					$configuring = file_exists($d_raidconfdirty_path) && in_array($raid['name']."\n",file($d_raidconfdirty_path));
          if (true == $configuring) {
          	$size = gettext("Configuring");
          	$status = gettext("Configuring");
          } else {
          	if (is_array($raidstatus) && array_key_exists($raid['name'], $raidstatus)) {
          		$size = $raidstatus[$raid['name']]['size'];
          		$status = $raidstatus[$raid['name']]['state'];
						}
					}
          ?>
					<tr>
						<td class="listlr"><?=htmlspecialchars($raid['name']);?></td>
						<td class="listr"><?=htmlspecialchars($raid['type']);?></td>
						<td class="listr"><?=$size;?>&nbsp;</td>
						<td class="listbg"><?=$status;?>&nbsp;</td>
						<td valign="middle" nowrap class="list">
							<a href="disks_raid_gvinum_edit.php?id=<?=$i;?>"><img src="e.gif" title="<?=gettext("Edit RAID"); ?>" width="17" height="17" border="0"></a>&nbsp;
							<a href="disks_raid_gvinum.php?act=del&id=<?=$i;?>" onclick="return confirm('<?=gettext("Do you really want to delete this raid volume? All elements that still use it will become invalid (e.g. share)!") ;?>')"><img src="x.gif" title="<?=gettext("Delete RAID") ;?>" width="17" height="17" border="0"></a>
						</td>
					</tr>
					<?php $i++; endforeach;?>
					<tr>
						<td class="list" colspan="4"></td>
						<td class="list">
							<a href="disks_raid_gvinum_edit.php"><img src="plus.gif" title="<?=gettext("Add RAID");?>" width="17" height="17" border="0"></a>
						</td>
					</tr>
				</table>
			</form>
			<p><span class="vexpl"><span class="red"><strong><?=gettext("Note");?>:</strong></span><br><?php echo sprintf( gettext("Optional configuration step: Configuring a virtual RAID disk using your <a href='%s'>previously configured disk</a>.<br>Wait for the '%s' status before format and mount it!"), "disks_manage.php", "up");?></span></p>
		</td>
	</tr>
</table>
<?php include("fend.inc");?>
