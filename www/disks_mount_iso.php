#!/usr/local/bin/php
<?php
/*
	disks_mount_iso.php
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

$pgtitle = array(gettext("Disks"),gettext("Mount Point"),gettext("ISO"));

if (!is_array($config['mounts']['iso']))
	$config['mounts']['iso'] = array();

mount_iso_sort();

$a_mount_iso = &$config['mounts']['iso'];

if ($_POST) {
	$pconfig = $_POST;

	if ($_POST['apply']) {
		$retval = 0;
		if (!file_exists($d_sysrebootreqd_path)) {
			config_lock();
			/* reload all components that mount disk */
			disks_mount_iso_all();
			/* reload all components that use mount */
			services_samba_configure();
			services_nfs_configure();
			services_rsyncd_configure();
			services_afpd_configure();
			config_unlock();
		}
		$savemsg = get_std_save_message($retval);
		if ($retval == 0) {
			if (file_exists($d_mount_iso_dirty_path))
				unlink($d_mount_iso_dirty_path);
		}
	}
}
if ($_GET['act'] == "del")
{
	if ($a_mount_iso[$_GET['id']]) {
		disks_umount($a_mount[$_GET['id']]);
		unset($a_mount_iso[$_GET['id']]);
		write_config();
		touch($d_mount_iso_dirty_path);
		header("Location: disks_mount_iso.php");
		exit;
	}
}
if ($_GET['act'] == "ret")
{
	if ($a_mount_iso[$_GET['id']]) {
		disks_mount_iso($a_mount_iso[$_GET['id']]);
		header("Location: disks_mount_iso.php");
		exit;
	}
}
?>
<?php include("fbegin.inc"); ?>
<?php if($errormsg) print_input_errors($errormsg);?>
<table width="100%" border="0" cellpadding="0" cellspacing="0">
  <tr>
    <td class="tabnavtbl">
      <ul id="tabnav">
        <li class="tabinact"><a href="disks_mount.php"><?=gettext("Manage");?></a></li>
        <li class="tabinact"><a href="disks_mount_tools.php"><?=gettext("Tools");?></a></li>
        <li class="tabinact"><a href="disks_mount_fsck.php"><?=gettext("Fsck");?></a></li>
		<li class="tabact"><a href="disks_mount_iso.php" style="color:black" title="<?=gettext("Reload page");?>"><?=gettext("ISO");?></a></li>
		
      </ul>
    </td>
  </tr>
  <tr> 
    <td class="tabcont">
      <form action="disks_mount_iso.php" method="post">
        <?php if ($savemsg) print_info_box($savemsg); ?>
        <?php if (file_exists($d_mount_iso_dirty_path)): ?><p>
        <?php print_info_box_np(gettext("The ISO list has been changed.<br>You must apply the changes in order for them to take effect."));?><br>
        <input name="apply" type="submit" class="formbtn" id="apply" value="<?=gettext("Apply changes");?>"></p>
        <?php endif; ?>
        <table width="100%" border="0" cellpadding="0" cellspacing="0">
          <tr>
            <td width="40%" class="listhdrr"><?=gettext("Filename"); ?></td>
            <td width="25%" class="listhdrr"><?=gettext("Share Name") ;?></td>
            <td width="25%" class="listhdr"><?=gettext("Status") ;?></td>
            <td width="10%" class="list"></td>
          </tr>
  			  <?php $i = 0; foreach($a_mount_iso as $mount_iso): ?>
          <tr>
            <td class="listlr"><?=htmlspecialchars($mount_iso['filename']);?>&nbsp;</td>
            <td class="listr"><?=htmlspecialchars($mount_iso['sharename']);?>&nbsp;</td>
            <td class="listbg">
              <?php
              if (file_exists($d_mount_iso_dirty_path)) {
                echo(gettext("Configuring"));
              } else {
                if(disks_ismounted_sharename($mount_iso['sharename'])) {
									echo(gettext("OK"));
                } else {
                  echo(gettext("Error") . " - <a href=\"disks_mount_iso.php?act=ret&id={$i}\">" . gettext("Retry") . "</a>");
                }
              }
              ?>&nbsp;
            </td>
            <td valign="middle" nowrap class="list">
              <a href="disks_mount_iso_edit.php?id=<?=$i;?>"><img src="e.gif" title="edit mount" width="17" height="17" border="0"></a>&nbsp;
              <a href="disks_mount_iso.php?act=del&id=<?=$i;?>" onclick="return confirm('<?=gettext("Do you really want to delete this mount point? All elements that still use it will become invalid (e.g. share)!");?>')"><img src="x.gif" title="<?=gettext("delete mount"); ?>" width="17" height="17" border="0"></a>
            </td>
          </tr>
          <?php $i++; endforeach; ?>
          <tr> 
            <td class="list" colspan="3"></td>
            <td class="list"><a href="disks_mount_iso_edit.php"><img src="plus.gif" title="<?=gettext("add ISO to mount");?>" width="17" height="17" border="0"></a></td>
          </tr>
        </table>
      </form>
    </td>
  </tr>
</table>
<?php include("fend.inc"); ?>
