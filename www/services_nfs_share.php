#!/usr/local/bin/php
<?php
/*
	services_nfs_share.php
	Copyright (C) 2006-2009 Volker Theile (votdev@gmx.de)
	All rights reserved.

	part of FreeNAS (http://www.freenas.org)
	Copyright (C) 2005-2009 Olivier Cochard <olivier@freenas.org>.
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
require("auth.inc");
require("guiconfig.inc");

$pgtitle = array(gettext("Services"), gettext("NFS"), gettext("Shares"));

if ($_POST) {
	$pconfig = $_POST;

	if ($_POST['apply']) {
		$retval = 0;
		if (!file_exists($d_sysrebootreqd_path)) {
			$retval |= updatenotify_process("nfsshare", "nfsshare_process_updatenotification");
		  config_lock();
			$retval |= rc_update_service("rpcbind"); // !!! Do
			$retval |= rc_update_service("mountd");  // !!! not
			$retval |= rc_update_service("nfsd");    // !!! change
			$retval |= rc_update_service("statd");   // !!! this
			$retval |= rc_update_service("lockd");   // !!! order
			$retval |= rc_update_service("mdnsresponder");
			config_unlock();
		}
		$savemsg = get_std_save_message($retval);
		if ($retval == 0) {
			updatenotify_delete("nfsshare");
		}
	}
}

if (!is_array($config['nfsd']['share']))
	$config['nfsd']['share'] = array();

array_sort_key($config['nfsd']['share'], "path");
$a_share = &$config['nfsd']['share'];

if ($_GET['act'] === "del") {
	updatenotify_set("nfsshare", UPDATENOTIFY_MODE_DIRTY, $_GET['uuid']);
	header("Location: services_nfs_share.php");
	exit;
}

function nfsshare_process_updatenotification($mode, $data) {
	global $config;

	$retval = 0;

	switch ($mode) {
		case UPDATENOTIFY_MODE_NEW:
		case UPDATENOTIFY_MODE_MODIFIED:
			break;
		case UPDATENOTIFY_MODE_DIRTY:
			$cnid = array_search_ex($data, $config['nfsd']['share'], "uuid");
			if (FALSE !== $cnid) {
				unset($config['nfsd']['share'][$cnid]);
				write_config();
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
        <li class="tabinact"><a href="services_nfs.php"><span><?=gettext("Settings");?></span></a></li>
        <li class="tabact"><a href="services_nfs_share.php" title="<?=gettext("Reload page");?>"><span><?=gettext("Shares");?></span></a></li>
      </ul>
    </td>
  </tr>
  <tr>
    <td class="tabcont">
      <form action="services_nfs_share.php" method="post">
        <?php if ($savemsg) print_info_box($savemsg);?>
        <?php if (updatenotify_exists("nfsshare")) print_config_change_box();?>
        <table width="100%" border="0" cellpadding="0" cellspacing="0">
          <tr>
						<td width="30%" class="listhdrr"><?=gettext("Path");?></td>
						<td width="30%" class="listhdrr"><?=gettext("Network");?></td>
						<td width="30%" class="listhdrr"><?=gettext("Comment");?></td>
            <td width="10%" class="list"></td>
          </tr>
  			  <?php foreach ($a_share as $sharev):?>
  			  <?php $notificationmode = updatenotify_get_mode("nfsshare", $sharev['uuid']);?>
          <tr>
						<td class="listlr"><?=htmlspecialchars($sharev['path']);?>&nbsp;</td>
						<td class="listr"><?=htmlspecialchars($sharev['network']);?>&nbsp;</td>
						<td class="listr"><?=htmlspecialchars($sharev['comment']);?>&nbsp;</td>
						<?php if (UPDATENOTIFY_MODE_DIRTY != $notificationmode):?>
            <td valign="middle" nowrap class="list">
              <a href="services_nfs_share_edit.php?uuid=<?=$sharev['uuid'];?>"><img src="e.gif" title="<?=gettext("Edit share");?>" border="0"></a>
              <a href="services_nfs_share.php?act=del&uuid=<?=$sharev['uuid'];?>" onclick="return confirm('<?=gettext("Do you really want to delete this share?");?>')"><img src="x.gif" title="<?=gettext("Delete share");?>" border="0"></a>
            </td>
            <?php else:?>
						<td valign="middle" nowrap class="list">
							<img src="del.gif" border="0">
						</td>
						<?php endif;?>
          </tr>
          <?php endforeach;?>
          <tr>
            <td class="list" colspan="3"></td>
            <td class="list"><a href="services_nfs_share_edit.php"><img src="plus.gif" title="<?=gettext("Add share");?>" border="0"></a></td>
          </tr>
        </table>
      </form>
    </td>
  </tr>
</table>
<?php include("fend.inc");?>
