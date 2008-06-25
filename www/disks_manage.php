#!/usr/local/bin/php
<?php
/*
	disks_manage.php
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

$pgtitle = array(gettext("Disks"),gettext("Management"));

if (!is_array($config['disks']['disk']))
	$config['disks']['disk'] = array();

array_sort_key($config['disks']['disk'], "name");

$a_disk_conf = &$config['disks']['disk'];

if ($_POST) {
	$pconfig = $_POST;

	if ($_POST['apply']) {
		$retval = 0;
		if (!file_exists($d_sysrebootreqd_path)) {
			$retval |= ui_process_updatenotification($d_diskdirty_path, "diskmanagement_process_updatenotification");
			config_lock();
			$retval |= rc_update_service("ataidle");
			$retval |= rc_update_service("smartd");
			config_unlock();
		}
		$savemsg = get_std_save_message($retval);
		if ($retval == 0) {
			ui_cleanup_updatenotification($d_diskdirty_path);
		}
	}
}

if ($_GET['act'] === "del") {
	if ($a_disk_conf[$_GET['id']]) {
		ui_set_updatenotification($d_diskdirty_path, UPDATENOTIFICATION_MODE_DIRTY, $a_disk_conf[$_GET['id']]['uuid']);
		header("Location: disks_manage.php");
		exit;
	}
}

function diskmanagement_process_updatenotification($mode, $data) {
	global $config;

	$retval = 0;

	switch ($mode) {
		case UPDATENOTIFICATION_MODE_NEW:
		case UPDATENOTIFICATION_MODE_MODIFIED:
			break;
		case UPDATENOTIFICATION_MODE_DIRTY:
			if (is_array($config['disks']['disk'])) {
				$index = array_search_ex($data, $config['disks']['disk'], "uuid");
				if (false !== $index) {
					unset($config['disks']['disk'][$index]);
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
				<li class="tabact"><a href="disks_manage.php" title="<?=gettext("Reload page");?>"><?=gettext("Management");?></a></li>
				<li class="tabinact"><a href="disks_manage_smart.php"><?=gettext("S.M.A.R.T.");?></a></li>
				<li class="tabinact"><a href="disks_manage_iscsi.php"><?=gettext("iSCSI Initiator");?></a></li>
  		</ul>
  	</td>
	</tr>
  <tr>
    <td class="tabcont">
			<form action="disks_manage.php" method="post">
				<?php if ($savemsg) print_info_box($savemsg); ?>
				<?php if (file_exists($d_diskdirty_path)) print_config_change_box();?>
				<table width="100%" border="0" cellpadding="0" cellspacing="0">
					<tr>
						<td width="5%" class="listhdrr"><?=gettext("Disk"); ?></td>
						<td width="5%" class="listhdrr"><?=gettext("Size"); ?></td>
						<td width="50%" class="listhdrr"><?=gettext("Description"); ?></td>
						<td width="10%" class="listhdrr"><?=gettext("Standby time"); ?></td>
						<td width="10%" class="listhdrr"><?=gettext("File system"); ?></td>
						<td width="10%" class="listhdrr"><?=gettext("Status"); ?></td>
						<td width="10%" class="list"></td>
					</tr>
					<?php $i = 0; foreach ($a_disk_conf as $disk):?>
					<?php
					$notificationmode = ui_get_updatenotification_mode($d_diskdirty_path, $disk['uuid']);
					switch ($notificationmode) {
						case UPDATENOTIFICATION_MODE_NEW:
							$status = gettext("Initializing");
							break;
						case UPDATENOTIFICATION_MODE_MODIFIED:
							$status = gettext("Modifying");
							break;
						case UPDATENOTIFICATION_MODE_DIRTY:
							$status = gettext("Deleting");
							break;
						default:
							$status = (0 == disks_exists($disk['devicespecialfile'])) ? gettext("ONLINE") : gettext("MISSING");
							break;
					}
					?>
					<tr>
						<td class="listlr"><?=htmlspecialchars($disk['name']);?></td>
						<td class="listr"><?=htmlspecialchars($disk['size']);?></td>
						<td class="listr"><?=htmlspecialchars($disk['desc']);?>&nbsp;</td>
						<td class="listr"><?php if ($disk['harddiskstandby']) { echo $disk['harddiskstandby']; } else { echo gettext("Always on"); }?>&nbsp;</td>
						<td class="listr"><?=($disk['fstype']) ? get_fstype_shortdesc($disk['fstype']) : gettext("Unknown or unformatted")?>&nbsp;</td>
						<td class="listbg"><?=$status;?>&nbsp;</td>
						<?php if (UPDATENOTIFICATION_MODE_DIRTY != $notificationmode):?>
						<td valign="middle" nowrap class="list">
							<a href="disks_manage_edit.php?id=<?=$i;?>"><img src="e.gif" title="<?=gettext("Edit disk");?>" width="17" height="17" border="0"></a>&nbsp;
							<a href="disks_manage.php?act=del&id=<?=$i;?>" onclick="return confirm('<?=gettext("Do you really want to delete this disk? All elements that still use it will become invalid (e.g. share)!"); ?>')"><img src="x.gif" title="<?=gettext("Delete disk"); ?>" width="17" height="17" border="0"></a>
						</td>
						<?php endif;?>
					</tr>
					<?php $i++; endforeach;?>
					<tr>
						<td class="list" colspan="6"></td>
						<td class="list"> <a href="disks_manage_edit.php"><img src="plus.gif" title="<?=gettext("Add disk"); ?>" width="17" height="17" border="0"></a></td>
					</tr>
				</table>
			</form>
		</td>
	</tr>
</table>
<?php include("fend.inc");?>
