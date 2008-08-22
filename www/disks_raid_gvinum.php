#!/usr/local/bin/php
<?php
/*
	disks_raid_gvinum.php
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

$pgtitle = array(gettext("Disks"), gettext("Software RAID"), gettext("Geom Vinum"), gettext("Management"));

if (!is_array($config['gvinum']['vdisk']))
	$config['gvinum']['vdisk'] = array();

array_sort_key($config['gvinum']['vdisk'], "name");

$a_raid = &$config['gvinum']['vdisk'];

if ($_POST) {
	$pconfig = $_POST;

	if ($_POST['apply']) {
		$retval = 0;
		if (!file_exists($d_sysrebootreqd_path)) {
			// Process notifications
			$retval = ui_process_updatenotification("raid_gvinum", "gvinum_process_updatenotification");
		}
		$savemsg = get_std_save_message($retval);
		if ($retval == 0) {
			ui_cleanup_updatenotification("raid_gvinum");
		}
		header("Location: disks_raid_gvinum.php");
		exit;
	}
}

if ($_GET['act'] === "del") {
	unset($errormsg);
	if ($a_raid[$_GET['id']]) {
		// Check if disk is mounted.
		if (0 == disks_ismounted_ex($a_raid[$_GET['id']]['devicespecialfile'], "devicespecialfile")) {
			ui_set_updatenotification("raid_gvinum", UPDATENOTIFICATION_MODE_DIRTY, $a_raid[$_GET['id']]['uuid']);
			header("Location: disks_raid_gvinum.php");
			exit;
		} else {
			$errormsg = sprintf( gettext("The RAID volume is currently mounted! Remove the <a href=%s>mount point</a> first before proceeding."), "disks_mount.php");
		}
	}
}

function gvinum_process_updatenotification($mode, $data) {
	global $config;

	$retval = 0;

	switch ($mode) {
		case UPDATENOTIFICATION_MODE_NEW:
			$retval |= disks_raid_gvinum_configure($data);
			break;
		case UPDATENOTIFICATION_MODE_MODIFIED:
			$retval |= rc_exec_service("geom start vinum");
			break;
		case UPDATENOTIFICATION_MODE_DIRTY:
			$retval |= disks_raid_gvinum_delete($data);
			if (is_array($config['gvinum']['vdisk'])) {
				$index = array_search_ex($data, $config['gvinum']['vdisk'], "uuid");
				if (false !== $index) {
					unset($config['gvinum']['vdisk'][$index]);
					write_config();
				}
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
				<li class="tabinact"><a href="disks_raid_gconcat.php"><span><?=gettext("JBOD"); ?></span></a></li>
				<li class="tabinact"><a href="disks_raid_gstripe.php"><span><?=gettext("RAID 0"); ?></span></a></li>
				<li class="tabinact"><a href="disks_raid_gmirror.php"><span><?=gettext("RAID 1"); ?></span></a></li>
				<li class="tabinact"><a href="disks_raid_graid5.php"><span><?=gettext("RAID 5"); ?></span></a></li>
				<li class="tabact"><a href="disks_raid_gvinum.php" title="<?=gettext("Reload page");?>" ><span><?=gettext("Geom Vinum"); ?> <?=gettext("(unstable)") ;?></span></a></li>
			</ul>
		</td>
	</tr>
	<tr>
		<td class="tabnavtbl">
			<ul id="tabnav">
				<li class="tabact"><a href="disks_raid_gvinum.php" title="<?=gettext("Reload page");?>" ><span><?=gettext("Management");?></span></a></li>
				<li class="tabinact"><a href="disks_raid_gvinum_tools.php"><span><?=gettext("Tools"); ?></span></a></li>
				<li class="tabinact"><a href="disks_raid_gvinum_info.php"><span><?=gettext("Information"); ?></span></a></li>
			</ul>
		</td>
	</tr>
	<tr>
		<td class="tabcont">
			<form action="disks_raid_gvinum.php" method="post">
				<?php if ($errormsg) print_error_box($errormsg); ?>
				<?php if ($savemsg) print_info_box($savemsg); ?>
				<?php if (ui_exists_updatenotification_mode("raid_gvinum", UPDATENOTIFICATION_MODE_DIRTY)) print_warning_box(gettext("Warning: You are going to delete a RAID volume. All data will get lost and can not be recovered."));?>
				<?php if (ui_exists_updatenotification("raid_gvinum")) print_config_change_box();?>
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
      		if (is_array($raidstatus) && array_key_exists($raid['name'], $raidstatus)) {
        		$size = $raidstatus[$raid['name']]['size'];
        		$status = $raidstatus[$raid['name']]['state'];
					}

					$notificationmode = ui_get_updatenotification_mode("raid_gvinum", $raid['uuid']);
					switch ($notificationmode) {
						case UPDATENOTIFICATION_MODE_NEW:
							$size = gettext("Initializing");
							$status = gettext("Initializing");
							break;
						case UPDATENOTIFICATION_MODE_MODIFIED:
							$size = gettext("Modifying");
							$status = gettext("Modifying");
							break;
						case UPDATENOTIFICATION_MODE_DIRTY:
							$status = gettext("Deleting");
							break;
					}
          ?>
					<tr>
						<td class="listlr"><?=htmlspecialchars($raid['name']);?></td>
						<td class="listr"><?=htmlspecialchars($raid['type']);?></td>
						<td class="listr"><?=$size;?>&nbsp;</td>
						<td class="listbg"><?=$status;?>&nbsp;</td>
						<?php if (UPDATENOTIFICATION_MODE_DIRTY != $notificationmode):?>
						<td valign="middle" nowrap class="list">
							<a href="disks_raid_gvinum_edit.php?id=<?=$i;?>"><img src="e.gif" title="<?=gettext("Edit RAID"); ?>" width="17" height="17" border="0"></a>&nbsp;
							<a href="disks_raid_gvinum.php?act=del&id=<?=$i;?>" onclick="return confirm('<?=gettext("Do you really want to delete this volume?\\n!!! Note, all data will get lost and can not be recovered. !!!") ;?>')"><img src="x.gif" title="<?=gettext("Delete RAID") ;?>" width="17" height="17" border="0"></a>
						</td>
						<?php endif;?>
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
			<span class="vexpl">
				<span class="red"><strong><?=gettext("Info");?>:</strong></span><br/>
				<?=sprintf(gettext("%s is used to create %s volumes."), "GEOM Vinum", "RAID");?>
			</span>
		</td>
	</tr>
</table>
<?php include("fend.inc");?>
