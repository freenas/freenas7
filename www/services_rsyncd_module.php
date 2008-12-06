#!/usr/local/bin/php
<?php
/*
	services_rsyncd_module.php
	Copyright (C) 2006-2008 Volker Theile (votdev@gmx.de)
	All rights reserved.

	part of FreeNAS (http://www.freenas.org)
	Copyright (C) 2005-2008 Olivier Cochard <olivier@freenas.org>.
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

$pgtitle = array(gettext("Services"), gettext("Rsync"), gettext("Server"), gettext("Modules"));

if ($_POST) {
	$pconfig = $_POST;

	if ($_POST['apply']) {
		$retval = 0;
		if (!file_exists($d_sysrebootreqd_path)) {
			$retval |= ui_process_updatenotification("rsyncd", "rsyncd_process_updatenotification");
			config_lock();
			$retval |= rc_update_service("rsyncd");
			$retval |= rc_update_service("mdnsresponder");
			config_unlock();
		}
		$savemsg = get_std_save_message($retval);
		if ($retval == 0) {
			ui_cleanup_updatenotification("rsyncd");
		}
	}
}

if (!is_array($config['rsyncd']['module']))
	$config['rsyncd']['module'] = array();

array_sort_key($config['rsyncd']['module'], "name");
$a_module = &$config['rsyncd']['module'];

if ($_GET['act'] === "del") {
	if ($a_module[$_GET['id']]) {
		ui_set_updatenotification("rsyncd", UPDATENOTIFICATION_MODE_DIRTY, $a_module[$_GET['id']]['uuid']);
		header("Location: services_rsyncd_module.php");
		exit;
	}
}

function rsyncd_process_updatenotification($mode, $data) {
	global $config;

	$retval = 0;

	switch ($mode) {
		case UPDATENOTIFICATION_MODE_NEW:
		case UPDATENOTIFICATION_MODE_MODIFIED:
			break;
		case UPDATENOTIFICATION_MODE_DIRTY:
			if (is_array($config['rsyncd']['module'])) {
				$index = array_search_ex($data, $config['rsyncd']['module'], "uuid");
				if (false !== $index) {
					unset($config['rsyncd']['module'][$index]);
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
				<li class="tabact"><a href="services_rsyncd.php" title="<?=gettext("Reload page");?>"><span><?=gettext("Server");?></span></a></li>
			  <li class="tabinact"><a href="services_rsyncd_client.php"><span><?=gettext("Client");?></span></a></li>
			  <li class="tabinact"><a href="services_rsyncd_local.php"><span><?=gettext("Local");?></span></a></li>
			</ul>
		</td>
	</tr>
	<tr>
		<td class="tabnavtbl">
			<ul id="tabnav">
				<li class="tabinact"><a href="services_rsyncd.php"><span><?=gettext("Settings");?></span></a></li>
				<li class="tabact"><a href="services_rsyncd_module.php" title="<?=gettext("Reload page");?>"><span><?=gettext("Modules");?></span></a></li>
			</ul>
		</td>
	</tr>
  <tr>
    <td class="tabcont">
      <form action="services_rsyncd_module.php" method="post">
        <?php if ($savemsg) print_info_box($savemsg);?>
        <?php if (ui_exists_updatenotification("rsyncd")) print_config_change_box();?>
        <table width="100%" border="0" cellpadding="0" cellspacing="0">
          <tr>
          	<td width="15%" class="listhdrr"><?=gettext("Name");?></td>
            <td width="35%" class="listhdrr"><?=gettext("Path");?></td>
            <td width="20%" class="listhdrr"><?=gettext("Comment");?></td>
            <td width="10%" class="listhdrr"><?=gettext("List");?></td>
            <td width="10%" class="listhdrr"><?=gettext("Access mode");?></td>
            <td width="10%" class="list"></td>
          </tr>
  			  <?php $i = 0; foreach($a_module as $modulev):?>
  			  <?php $notificationmode = ui_get_updatenotification_mode("rsyncd", $modulev['uuid']);?>
          <tr>
            <td class="listlr"><?=htmlspecialchars($modulev['name']);?>&nbsp;</td>
            <td class="listr"><?=htmlspecialchars($modulev['path']);?>&nbsp;</td>
            <td class="listr"><?=htmlspecialchars($modulev['comment']);?>&nbsp;</td>
            <td class="listbg"><?=htmlspecialchars(isset($modulev['list'])?gettext("Yes"):gettext("No"));?></td>
            <td class="listbg"><?=htmlspecialchars($modulev['rwmode']);?>&nbsp;</td>
            <?php if (UPDATENOTIFICATION_MODE_DIRTY != $notificationmode):?>
            <td valign="middle" nowrap class="list">
              <a href="services_rsyncd_module_edit.php?id=<?=$i;?>"><img src="e.gif" title="<?=gettext("Edit module");?>" border="0"></a>
              <a href="services_rsyncd_module.php?act=del&id=<?=$i;?>" onclick="return confirm('<?=gettext("Do you really want to delete this module?");?>')"><img src="x.gif" title="<?=gettext("Delete module");?>" border="0"></a>
            </td>
						<?php else:?>
						<td valign="middle" nowrap class="list">
							<img src="del.gif" border="0">
						</td>
						<?php endif;?>
          </tr>
          <?php $i++; endforeach;?>
          <tr>
            <td class="list" colspan="5"></td>
            <td class="list"><a href="services_rsyncd_module_edit.php"><img src="plus.gif" title="<?=gettext("Add module");?>" border="0"></a></td>
          </tr>
        </table>
      </form>
    </td>
  </tr>
</table>
<?php include("fend.inc");?>
