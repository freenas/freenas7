#!/usr/local/bin/php
<?php
/*
	disks_mount.php
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

$pgtitle = array(gettext("Disks"),gettext("Mount Point"));

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
			$retval |= disks_mount_all();
			$retval |= services_samba_configure();
			$retval |= services_nfs_configure();
			$retval |= services_rsyncd_configure();
			$retval |= services_create_afpd_conf();
			$retval |= rc_update_service("afpd",isset($config['afp']['enable']));
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
		// MUST check if mount point is used by swap.
		if (isset($config['system']['swap_enable']) && ($config['system']['swap_mountname'] == $a_mount[$_GET['id']]['sharename'])) {
			$errormsg[] = gettext("The swap file is using this mount point.");
		} else {
			disks_umount($a_mount[$_GET['id']]);
			unset($a_mount[$_GET['id']]);
			write_config();
			touch($d_mountdirty_path);
			header("Location: disks_mount.php");
			exit;
		}
	}
}

if ($_GET['act'] == "retry")
{
	if ($a_mount[$_GET['id']]) {
		if (0 == disks_mount($a_mount[$_GET['id']])) {
			services_samba_configure();
			services_nfs_configure();
			services_rsyncd_configure();
			rc_update_service("afpd",isset($config['afp']['enable']));
		}
		header("Location: disks_mount.php");
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
        <li class="tabact"><a href="disks_mount.php" style="color:black" title="<?=gettext("Reload page");?>"><?=gettext("Manage");?></a></li>
        <li class="tabinact"><a href="disks_mount_tools.php"><?=gettext("Tools");?></a></li>
        <li class="tabinact"><a href="disks_mount_fsck.php"><?=gettext("Fsck");?></a></li>
      </ul>
    </td>
  </tr>
  <tr> 
    <td class="tabcont">
      <form action="disks_mount.php" method="post">
        <?php if ($savemsg) print_info_box($savemsg); ?>
        <?php if (file_exists($d_mountdirty_path)): ?><p>
        <?php print_info_box_np(gettext("The mount point list has been changed.<br>You must apply the changes in order for them to take effect."));?><br>
        <input name="apply" type="submit" class="formbtn" id="apply" value="<?=gettext("Apply changes");?>"></p>
        <?php endif; ?>
        <table width="100%" border="0" cellpadding="0" cellspacing="0">
          <tr>
            <td width="20%" class="listhdrr"><?=gettext("Disk"); ?></td>
            <td width="5%" class="listhdrr"><?=gettext("File system"); ?></td>
            <td width="20%" class="listhdrr"><?=gettext("Share Name") ;?></td>
            <td width="25%" class="listhdrr"><?=gettext("Description") ;?></td>
            <td width="20%" class="listhdr"><?=gettext("Status") ;?></td>
            <td width="10%" class="list"></td>
          </tr>
  			  <?php $i = 0; foreach($a_mount as $mount): ?>
          <tr>
          	<?php if ("disk" === $mount['type']): ?>
            <td class="listlr"><?=htmlspecialchars($mount['fullname']);?>&nbsp;</td>
            <?php else: ?>
            <td class="listlr"><?=htmlspecialchars($mount['filename']);?>&nbsp;</td>
            <?php endif; ?>
            <td class="listr"><?=htmlspecialchars($mount['fstype']);?>&nbsp;</td>
            <td class="listr"><?=htmlspecialchars($mount['sharename']);?>&nbsp;</td>
            <td class="listr"><?=htmlspecialchars($mount['desc']);?>&nbsp;</td>
            <td class="listbg">
              <?php
              if (file_exists($d_mountdirty_path)) {
                echo(gettext("Configuring"));
              } else {
                if(disks_ismounted_ex($mount['sharename'],"sharename")) {
									echo(gettext("OK"));
                } else {
                  echo(gettext("Error") . " - <a href=\"disks_mount.php?act=retry&id={$i}\">" . gettext("Retry") . "</a>");
                }
              }
              ?>&nbsp;
            </td>
            <td valign="middle" nowrap class="list">
            	<?php if ("disk" === $mount['type']): ?>
              <a href="disks_mount_edit.php?id=<?=$i;?>"><img src="e.gif" title="edit mount" width="17" height="17" border="0"></a>&nbsp;
              <?php endif; ?>
              <a href="disks_mount.php?act=del&id=<?=$i;?>" onclick="return confirm('<?=gettext("Do you really want to delete this mount point? All elements that still use it will become invalid (e.g. share)!");?>')"><img src="x.gif" title="<?=gettext("delete mount"); ?>" width="17" height="17" border="0"></a>
            </td>
          </tr>
          <?php $i++; endforeach; ?>
          <tr> 
            <td class="list" colspan="5"></td>
            <td class="list"><a href="disks_mount_edit.php"><img src="plus.gif" title="<?=gettext("add mount");?>" width="17" height="17" border="0"></a></td>
          </tr>
        </table>
      </form>
      <p><span class="vexpl"><span class="red"><strong><?=gettext("Note");?>:</strong></span><br><?php echo sprintf( gettext("Second configuration step: Declaring the filesystem used by your <a href=%s>previously configured disk.</a>"), "disks_manage.php");?></p>
      <p><span class="vexpl"><span class="red"><strong><?=gettext("Warning");?>:</strong></span><br><?php echo sprintf(gettext("UFS and variants are the NATIVE file format for FreeBSD (the underlying OS of %s). Attempting to use other file formats such as FAT, FAT32, EXT2, EXT3, or NTFS can result in unpredictable results, file corruption, and loss of data!"), get_product_name());?></p>
    </td>
  </tr>
</table>
<?php include("fend.inc"); ?>
