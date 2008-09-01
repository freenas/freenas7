#!/usr/local/bin/php
<?php
/*
	services_rsyncd_client.php
	part of FreeNAS (http://www.freenas.org)
	Copyright (C) 2005-2008 Olivier Cochard-Labb� <olivier@freenas.org>.
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

$pgtitle = array(gettext("Services"), gettext("RSYNC"), gettext("Client"));

if (!is_array($config['rsync'])) {
	$config['rsync'] = array();
	if (!is_array($config['rsync']['rsyncclient']))
		$config['rsync']['rsyncclient'] = array();
} else if (!is_array($config['rsync']['rsyncclient'])) {
	$config['rsync']['rsyncclient'] = array();
}

$a_rsyncclient = &$config['rsync']['rsyncclient'];

if ($_POST) {
	$pconfig = $_POST;

	if ($_POST['apply']) {
		$retval = 0;
		if (!file_exists($d_sysrebootreqd_path)) {
			$retval |= ui_process_updatenotification("rsyncclient", "rsyncclient_process_updatenotification");
			config_lock();
			$retval |= rc_exec_service("rsync_client");
			$retval |= rc_update_service("cron");
			config_unlock();
		}
		$savemsg = get_std_save_message($retval);
		if ($retval == 0) {
			ui_cleanup_updatenotification("rsyncclient");
		}
	}
}

if ($_GET['act'] === "del") {
	if ($a_rsyncclient[$_GET['id']]) {
		ui_set_updatenotification("rsyncclient", UPDATENOTIFICATION_MODE_DIRTY, $a_rsyncclient[$_GET['id']]['uuid']);
		header("Location: services_rsyncd_client.php");
		exit;
	}
}

function rsyncclient_process_updatenotification($mode, $data) {
	global $config;

	$retval = 0;

	switch ($mode) {
		case UPDATENOTIFICATION_MODE_NEW:
		case UPDATENOTIFICATION_MODE_MODIFIED:
			break;
		case UPDATENOTIFICATION_MODE_DIRTY:
			if (is_array($config['rsync']['rsyncclient'])) {
				$index = array_search_ex($data, $config['rsync']['rsyncclient'], "uuid");
				if (false !== $index) {
					unset($config['rsync']['rsyncclient'][$index]);
					write_config();
				}
				@unlink("/var/run/rsync_client_{$data}.sh");
			}
			break;
	}

	return $retval;
}
?>
<?php include("fbegin.inc"); ?>
<table width="100%" border="0" cellpadding="0" cellspacing="0">
	<tr>
		<td class="tabnavtbl">
			<ul id="tabnav">
				<li class="tabinact"><a href="services_rsyncd.php"><span><?=gettext("Server");?></span></a></li>
				<li class="tabact"><a href="services_rsyncd_client.php" title="<?=gettext("Reload page");?>"><span><?=gettext("Client");?></span></a></li>
				<li class="tabinact"><a href="services_rsyncd_local.php"><span><?=gettext("Local");?></span></a></li>
			</ul>
		</td>
	</tr>
  <tr>
    <td class="tabcont">
      <form action="services_rsyncd_client.php" method="post">
        <?php if ($savemsg) print_info_box($savemsg);?>
        <?php if (ui_exists_updatenotification("rsyncclient")) print_config_change_box();?>
        <table width="100%" border="0" cellpadding="0" cellspacing="0">
          <tr>
						<td width="20%" class="listhdrr"><?=gettext("Remote module (source)"); ?></td>
						<td width="20%" class="listhdrr"><?=gettext("Remote address"); ?></td>
						<td width="20%" class="listhdrr"><?=gettext("Local share (destination)"); ?></td>
						<td width="30%" class="listhdrr"><?=gettext("Description"); ?></td>
            <td width="10%" class="list"></td>
          </tr>
  			  <?php $i = 0; foreach($a_rsyncclient as $rsyncclient):?>
  			  <?php $notificationmode = ui_get_updatenotification_mode("rsyncclient", $rsyncclient['uuid']);?>
          <tr>   
						<td class="listr"><?=htmlspecialchars($rsyncclient['remoteshare']);?>&nbsp;</td>
						<td class="listr"><?=htmlspecialchars($rsyncclient['rsyncserverip']);?>&nbsp;</td>
						<td class="listr"><?=htmlspecialchars($rsyncclient['localshare']);?>&nbsp;</td>
						<td class="listr"><?=htmlspecialchars($rsyncclient['description']);?>&nbsp;</td>
						<?php if (UPDATENOTIFICATION_MODE_DIRTY != $notificationmode):?>
            <td valign="middle" nowrap class="list">
							<a href="services_rsyncd_client_edit.php?id=<?=$i;?>"><img src="e.gif" title="<?=gettext("Edit RSYNC");?>" width="17" height="17" border="0"></a>&nbsp;
              <a href="services_rsyncd_client.php?act=del&id=<?=$i;?>" onclick="return confirm('<?=gettext("Do you really want to delete this RSYNC?");?>')"><img src="x.gif" title="<?=gettext("Delete RSYNC"); ?>" width="17" height="17" border="0"></a>
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
            <td class="list"><a href="services_rsyncd_client_edit.php"><img src="plus.gif" title="<?=gettext("Add RSYNC");?>" width="17" height="17" border="0"></a></td>
			    </tr>
        </table>
      </form>
	  </td>
  </tr>
</table>
<?php include("fend.inc");?>
