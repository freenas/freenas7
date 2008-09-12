#!/usr/local/bin/php
<?php
/*
	access_users.php
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

$pgtitle = array(gettext("Access"), gettext("Users"));

if ($_POST) {
	$pconfig = $_POST;

	if ($_POST['apply']) {
		$retval = 0;
		if (!file_exists($d_sysrebootreqd_path)) {
			$retval |= ui_process_updatenotification("userdb_user", "userdbuser_process_updatenotification");
			config_lock();
			$retval |= rc_exec_service("userdb");
			$retval |= rc_exec_service("websrv_htpasswd");
			if (isset($config['samba']['enable'])) {
				$retval |= rc_exec_service("smbpasswd");
				$retval |= rc_update_service("samba");
			}
			config_unlock();
		}
		$savemsg = get_std_save_message($retval);
		if ($retval == 0) {
			ui_cleanup_updatenotification("userdb_user");
		}
	}
}

if (!is_array($config['access']['user']))
	$config['access']['user'] = array();

array_sort_key($config['access']['user'], "login");
$a_user = &$config['access']['user'];

$a_group = system_get_group_list();

if ($_GET['act'] === "del") {
	if ($a_user[$_GET['id']]) {
		ui_set_updatenotification("userdb_user", UPDATENOTIFICATION_MODE_DIRTY, $a_user[$_GET['id']]['uuid']);
		header("Location: access_users.php");
		exit;
	}
}

function userdbuser_process_updatenotification($mode, $data) {
	global $config;

	$retval = 0;

	switch ($mode) {
		case UPDATENOTIFICATION_MODE_NEW:
		case UPDATENOTIFICATION_MODE_MODIFIED:
			break;
		case UPDATENOTIFICATION_MODE_DIRTY:
			if (is_array($config['access']['user'])) {
				$index = array_search_ex($data, $config['access']['user'], "uuid");
				if (false !== $index) {
					unset($config['access']['user'][$index]);
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
				<li class="tabact"><a href="access_users.php" title="<?=gettext("Reload page");?>"><span><?=gettext("Users");?></span></a></li>
				<li class="tabinact"><a href="access_users_groups.php"><span><?=gettext("Groups");?></span></a></li>
			</ul>
		</td>
	</tr>
  <tr>
    <td class="tabcont">
			<form action="access_users.php" method="post">
				<?php if ($savemsg) print_info_box($savemsg);?>
				<?php if (ui_exists_updatenotification("userdb_user")) print_config_change_box();?>
				<table width="100%" border="0" cellpadding="0" cellspacing="0">
					<tr>
						<td width="20%" class="listhdrr"><?=gettext("User");?></td>
						<td width="35%" class="listhdrr"><?=gettext("Full Name");?></td>
						<td width="5%" class="listhdrr"><?=gettext("UID");?></td>
						<td width="30%" class="listhdrr"><?=gettext("Group");?></td>
						<td width="10%" class="list"></td>
					</tr>
					<?php $i = 0; foreach ($a_user as $userv):?>
					<?php $notificationmode = ui_get_updatenotification_mode("userdb_user", $userv['uuid']);?>
					<tr>
						<td class="listlr"><?=htmlspecialchars($userv['login']);?>&nbsp;</td>
						<td class="listr"><?=htmlspecialchars($userv['fullname']);?>&nbsp;</td>
						<td class="listr"><?=htmlspecialchars($userv['id']);?>&nbsp;</td>
						<td class="listr"><?=array_search($userv['primarygroup'], $a_group);?>&nbsp;</td>
						<?php if (UPDATENOTIFICATION_MODE_DIRTY != $notificationmode):?>
						<td valign="middle" nowrap class="list">
							<a href="access_users_edit.php?id=<?=$i;?>"><img src="e.gif" title="<?=gettext("Edit user");?>" border="0"></a>&nbsp;
							<a href="access_users.php?act=del&id=<?=$i;?>" onclick="return confirm('<?=gettext("Do you really want to delete this user?");?>')"><img src="x.gif" title="<?=gettext("Delete user");?>" border="0"></a>
						</td>
						<?php else:?>
						<td valign="middle" nowrap class="list">
							<img src="del.gif" border="0">
						</td>
						<?php endif;?>
					</tr>
					<?php $i++; endforeach;?>
					<tr>
						<td class="list" colspan="4"></td>
						<td class="list">
							<a href="access_users_edit.php"><img src="plus.gif" title="<?=gettext("Add user");?>" border="0"></a>
						</td>
					</tr>
				</table>
			</form>
		</td>
	</tr>
</table>
<?php include("fend.inc");?>
