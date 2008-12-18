#!/usr/local/bin/php
<?php
/*
	system_hosts.php

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

$pgtitle = array(gettext("Network"), gettext("Hosts"));

if ($_POST) {
	$pconfig = $_POST;

	if ($_POST['apply']) {
		$retval = 0;
		if (!file_exists($d_sysrebootreqd_path)) {
			$retval |= updatenotify_process("hosts", "hosts_process_updatenotification");
			config_lock();
			$retval |= rc_exec_service("hosts"); // Update /etc/hosts
			config_unlock();
		}
		$savemsg = get_std_save_message($retval);
		if ($retval == 0) {
			updatenotify_delete("hosts");
		}
	}
}

if (!is_array($config['system']['hosts']))
	$config['system']['hosts'] = array();

array_sort_key($config['system']['hosts'], "name");
$a_hosts = &$config['system']['hosts'];

if ($_GET['act'] === "del") {
	updatenotify_set("hosts", UPDATENOTIFY_MODE_DIRTY, $_GET['uuid']);
	header("Location: system_hosts.php");
	exit;
}

function hosts_process_updatenotification($mode, $data) {
	global $config;

	$retval = 0;

	switch ($mode) {
		case UPDATENOTIFY_MODE_NEW:
		case UPDATENOTIFY_MODE_MODIFIED:
			break;
		case UPDATENOTIFY_MODE_DIRTY:
			if (is_array($config['system']['hosts'])) {
				$index = array_search_ex($data, $config['system']['hosts'], "uuid");
				if (false !== $index) {
					unset($config['system']['hosts'][$index]);
					write_config();
				}
			}
			break;
	}

	return $retval;
}
?>
<?php include("fbegin.inc"); ?>
<table width="100%" border="0" cellpadding="0" cellspacing="0">
  <tr>
    <td class="tabcont">
			<form action="system_hosts.php" method="post">
				<?php if ($savemsg) print_info_box($savemsg); ?>
				<?php if (updatenotify_exists("hosts")) print_config_change_box();?>
			  <table width="100%" border="0" cellpadding="0" cellspacing="0">
			    <tr>
			      <td width="25%" class="listhdrr"><?=gettext("Hostname");?></td>
			      <td width="30%" class="listhdrr"><?=gettext("IP address");?></td>
			      <td width="35%" class="listhdr"><?=gettext("Description");?></td>
			      <td width="10%" class="list"></td>
					</tr>
					<?php $i = 0; foreach ($a_hosts as $host):?>
					<?php if (empty($host['uuid'])) continue;?>
					<?php $notificationmode = updatenotify_get_mode("hosts", $host['uuid']);?>
					<tr>
						<td class="listlr"><?=htmlspecialchars($host['name']);?>&nbsp;</td>
						<td class="listr"><?=htmlspecialchars($host['address']);?>&nbsp;</td>
						<td class="listbg"><?=htmlspecialchars($host['descr']);?>&nbsp;</td>
						<?php if (UPDATENOTIFY_MODE_DIRTY != $notificationmode):?>
						<td valign="middle" nowrap class="list">
							<a href="system_hosts_edit.php?id=<?=$i;?>"><img src="e.gif" title="<?=gettext("Edit Host");?>" border="0"></a>
							<a href="system_hosts.php?act=del&uuid=<?=$host['uuid'];?>" onclick="return confirm('<?=gettext("Do you really want to delete this host?");?>')"><img src="x.gif" title="<?=gettext("Delete Host");?>" border="0"></a>
						</td>
						<?php else:?>
						<td valign="middle" nowrap class="list">
							<img src="del.gif" border="0">
						</td>
						<?php endif;?>
					</tr>
					<?php $i++; endforeach;?>
					<tr>
						<td class="list" colspan="3"></td>
						<td class="list"><a href="system_hosts_edit.php"><img src="plus.gif" title="<?=gettext("Add Host");?>" border="0"></a></td>
					</tr>
				</table>
				<div id="remarks">
					<?php html_remark("note", gettext("Note"), gettext("Defining a hostname can be useful for NFS."));?>
				</div>
			</form>
		</td>
	</tr>
</table>
<?php include("fend.inc");?>
