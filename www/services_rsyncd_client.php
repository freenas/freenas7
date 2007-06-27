#!/usr/local/bin/php
<?php
/*
	services_rsyncd_client.php
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

$pgtitle = array(gettext("Services"),gettext("RSYNC"),gettext("Client"));

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
			config_lock();
			$retval |= rc_exec_service("rsync_client");
			$retval |= services_cron_configure();
			config_unlock();
		}
		$savemsg = get_std_save_message($retval);
		if ($retval == 0) {
			if (file_exists($d_rsyncclientdirty_path))
				unlink($d_rsyncclientdirty_path);
		}
	}
}
if ($_GET['act'] == "del") {
	if ($a_rsyncclient[$_GET['id']]) {
			unset($a_rsyncclient[$_GET['id']]);
			write_config();
			touch($d_rsyncclientdirty_path);
			header("Location: services_rsyncd_client.php");
			exit;
	}
}
?>
<?php include("fbegin.inc"); ?>
<table width="100%" border="0" cellpadding="0" cellspacing="0">
	<tr>
		<td class="tabnavtbl">
			<ul id="tabnav">
				<li class="tabinact"><a href="services_rsyncd.php"><?=gettext("Server") ;?></a></li>
				<li class="tabact"><a href="services_rsyncd_client.php" style="color:black" title="<?=gettext("Reload page");?>"><?=gettext("Client") ;?></a></li>
				<li class="tabinact"><a href="services_rsyncd_local.php"><?=gettext("Local") ;?></a></li>
			</ul>
		</td>
	</tr>
  <tr>
    <td class="tabcont">
      <form action="services_rsyncd_client.php" method="post">
        <?php if ($savemsg) print_info_box($savemsg); ?>
        <?php if (file_exists($d_rsyncclientdirty_path)): ?><p>
        <?php print_info_box_np(gettext("The RSYNC client list has been changed.<br>You must apply the changes in order for them to take effect."));?><br>
        <input name="apply" type="submit" class="formbtn" id="apply" value="<?=gettext("Apply changes");?>"></p>
        <?php endif; ?>
        <table width="100%" border="0" cellpadding="0" cellspacing="0">
          <tr>
						<td width="20%" class="listhdrr"><?=gettext("Remote share (source)"); ?></td>
						<td width="20%" class="listhdrr"><?=gettext("Remote address"); ?></td>
						<td width="20%" class="listhdrr"><?=gettext("Local share (destination)"); ?></td>
						<td width="30%" class="listhdrr"><?=gettext("Description"); ?></td>
            <td width="10%" class="list"></td>
          </tr>
  			  <?php $i = 0; foreach($a_rsyncclient as $rsyncclient): ?>
          <tr>   
						<td class="listr"><?=htmlspecialchars($rsyncclient['remoteshare']);?>&nbsp;</td>
						<td class="listr"><?=htmlspecialchars($rsyncclient['rsyncserverip']);?>&nbsp;</td>
						<td class="listr"><?=htmlspecialchars($rsyncclient['localshare']);?>&nbsp;</td>
						<td class="listr"><?=htmlspecialchars($rsyncclient['description']);?>&nbsp;</td>
            <td valign="middle" nowrap class="list">
							<a href="services_rsyncd_client_edit.php?id=<?=$i;?>"><img src="e.gif" title="<?=gettext("Edit RSYNC");?>" width="17" height="17" border="0"></a>&nbsp;
              <a href="services_rsyncd_client.php?act=del&id=<?=$i;?>" onclick="return confirm('<?=gettext("Do you really want to delete this RSYNC?");?>')"><img src="x.gif" title="<?=gettext("Delete RSYNC"); ?>" width="17" height="17" border="0"></a>
            </td>
          </tr>
          <?php $i++; endforeach; ?>
          <tr> 
            <td class="list" colspan="4"></td>
            <td class="list"><a href="services_rsyncd_client_edit.php"><img src="plus.gif" title="<?=gettext("Add RSYNC");?>" width="17" height="17" border="0"></a></td>
			    </tr>
        </table>
      </form>
	  </td>
  </tr>
</table>
<?php include("fend.inc"); ?>
