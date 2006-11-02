#!/usr/local/bin/php
<?php
/*
	disks_mount.php
	part of FreeNAS (http://www.freenas.org)
	Copyright (C) 2005-2006 Olivier Cochard <olivier@freenas.org>.
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

$pgtitle = array(_DISKS, _DISKSMOUNTPHP_NAME);


if (!is_array($config['mounts']['mount']))
	$config['mounts']['mount'] = array();

mount_sort();

$a_mount = &$config['mounts']['mount'];

if ($_POST) {

	$pconfig = $_POST;

	if ($_POST['apply']) {
		$retval = 0;
		if (!file_exists($d_sysrebootreqd_path)) {
			config_lock();
			/* reload all components that mount disk */
			disks_mount_all();
			/* reload all components that use mount */
			services_samba_configure();
			services_nfs_configure();
			services_rsyncd_configure();
			services_afpd_configure();
			config_unlock();
		}
		$savemsg = get_std_save_message($retval);
		if ($retval == 0) {
			if (file_exists($d_mountdirty_path))
				unlink($d_mountdirty_path);
		}
	}
}

if ($_GET['act'] == "del")
{
	if ($a_mount[$_GET['id']]) {
		disks_umount_adv($a_mount[$_GET['id']]);
		unset($a_mount[$_GET['id']]);
		write_config();
		touch($d_mountdirty_path);
		header("Location: disks_mount.php");
		exit;
	}
}

if ($_GET['act'] == "ret")
{
	if ($a_mount[$_GET['id']]) {
		disks_mount($a_mount[$_GET['id']]);
		header("Location: disks_mount.php");
		exit;
	}
}

?>
<?php include("fbegin.inc"); ?>
<form action="disks_mount.php" method="post">
<?php if ($savemsg) print_info_box($savemsg); ?>
<?php if (file_exists($d_mountdirty_path)): ?><p>
<?php print_info_box_np(_DISKSMOUNTPHP_MSGCHANGED);?><br>
<input name="apply" type="submit" class="formbtn" id="apply" value="<?=_APPLY;?>"></p>
<?php endif; ?>
              <table width="100%" border="0" cellpadding="0" cellspacing="0">
                <tr>
                  <td width="10%" class="listhdrr"><?=_DISK; ?></td>
                  <td width="10%" class="listhdrr"><?=_PARTITION; ?></td>
                  <td width="5%" class="listhdrr"><?=_FILESYSTEM; ?></td>
                  <td width="20%" class="listhdrr"><?=_DISKSMOUNTPHP_SHARENAME ;?></td>
                  <td width="25%" class="listhdrr"><?=_DESC ;?></td>
                  <td width="20%" class="listhdr"><?=_STATUS ;?></td>
                  <td width="10%" class="list"></td>
				</tr>
			  <?php $i = 0; foreach ($a_mount as $mount): ?>
                <tr>
                  <td class="listlr">
                    <?=htmlspecialchars($mount['mdisk']);?> &nbsp;
                  </td>
                  <td class="listr">
                    <?=htmlspecialchars($mount['partition']);?>&nbsp;
                  </td>
                  <td class="listr">
                    <?=htmlspecialchars($mount['fstype']);?>&nbsp;
                  </td>
                   <td class="listr">
                    <?=htmlspecialchars($mount['sharename']);?>&nbsp;
                  </td>
                   <td class="listr">
                    <?=htmlspecialchars($mount['desc']);?>&nbsp;
                  </td>
                 </td>
                   <td class="listbg">
                    <?php
                    if (file_exists($d_mountdirty_path))
						$stat=_CONFIGURING;
					else
					{
						$stat=disks_mount_status($mount);
						if ($stat == "ERROR")
							echo "ERROR - <a href=\"disks_mount.php?act=ret&id=$i\">retry</a>";
						else
							echo $stat;
                    }
                    ?>&nbsp;
                  </td>
                  <td valign="middle" nowrap class="list"> <a href="disks_mount_edit.php?id=<?=$i;?>"><img src="e.gif" title="edit mount" width="17" height="17" border="0"></a>
                     &nbsp;<a href="disks_mount.php?act=del&id=<?=$i;?>" onclick="return confirm('<?=_DISKSMOUNTPHP_DELCONF; ?>')"><img src="x.gif" title="<?=_DISKSMOUNTPHP_DEL; ?>" width="17" height="17" border="0"></a></td>
				</tr>
			  <?php $i++; endforeach; ?>
                <tr> 
                  <td class="list" colspan="6"></td>
                  <td class="list"> <a href="disks_mount_edit.php"><img src="plus.gif" title="<?=_DISKSMOUNTPHP_ADD ; ?>" width="17" height="17" border="0"></a></td>
				</tr>
              </table>
            </form>
<p><span class="vexpl"><span class="red"><strong><?=_NOTE;?>:</strong></span><br><?=_DISKSMOUNTPHP_NOTE;?></p>
<?php include("fend.inc"); ?>
