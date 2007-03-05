#!/usr/local/bin/php
<?php
/*
	disks_manage_iscsi.php
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

$pgtitle = array(gettext("Disks"),gettext("Management"));

if (!is_array($config['iscsiinit']['vdisk']))
	$config['iscsiinit']['vdisk'] = array();

iscsiinit_sort();

$a_iscsiinit = &$config['iscsiinit']['vdisk'];

if ($_POST) {
	$pconfig = $_POST;

	if ($_POST['apply']) {
		$retval = 0;
		if (!file_exists($d_sysrebootreqd_path)) {
			config_lock();
			services_iscsiinit_configure();
			config_unlock();
		}
		$savemsg = get_std_save_message($retval);
		if ($retval == 0) {
			if (file_exists($d_iscsiinitdirty_path))
				unlink($d_iscsiinitdirty_path);
		}
	}
}
if ($_GET['act'] == "del")
{
	if ($a_iscsiinit[$_GET['id']]) {
		/* if(disks_mount_check($a_iscsiinit[$_GET['id']]['fullname'])) {
			*/
			unset($a_iscsiinit[$_GET['id']]);
			write_config();
			touch($d_iscsiinitdirty_path);
			header("Location: disks_manage_iscsi.php");
			exit;
        /* } else {
                  $errormsg[] = gettext("This iscsi disk must be unlounted before to be delete");
        } */
	}
}
?>
<?php include("fbegin.inc"); ?>
<?php if($errormsg) print_input_errors($errormsg);?>
<table width="100%" border="0" cellpadding="0" cellspacing="0">
<tr>
    <td class="tabnavtbl">
      <ul id="tabnav">
      	<li class="tabinact"><a href="disks_manage.php"><?=gettext("Manage");?></a></li>
				<li class="tabact"><a href="disks_manage_iscsi.php" style="color:black" title="<?=gettext("Reload page");?>"><?=gettext("iSCSI Initiator");?></a></li>
      </ul>
    </td>
  </tr>
  <tr> 
    <td class="tabcont">
      <form action="disks_manage_iscsi.php" method="post">
        <?php if ($savemsg) print_info_box($savemsg); ?>
        <?php if (file_exists($d_iscsiinitdirty_path)): ?><p>
        <?php print_info_box_np(gettext("The iSCSI disk list has been changed.<br>You must apply the changes in order for them to take effect."));?><br>
        <input name="apply" type="submit" class="formbtn" id="apply" value="<?=gettext("Apply changes");?>"></p>
        <?php endif; ?>
        <table width="100%" border="0" cellpadding="0" cellspacing="0">
          <tr>
            <td width="25%" class="listhdrr"><?=gettext("Name"); ?></td>
						<td width="25%" class="listhdrr"><?=gettext("Target address"); ?></td>
            <td width="25%" class="listhdrr"><?=gettext("Target name"); ?></td>
            <td width="10%" class="list"></td>
          </tr>
  			  <?php $i = 0; foreach($a_iscsiinit as $iscsiinit): ?>
          <tr>
            <td class="listlr"><?=htmlspecialchars($iscsiinit['name']);?>&nbsp;</td>
						<td class="listr"><?=htmlspecialchars($iscsiinit['targetname']);?>&nbsp;</td>
            <td class="listr"><?=htmlspecialchars($iscsiinit['targetaddress']);?>&nbsp;</td>
            <td valign="middle" nowrap class="list"> <a href="disks_manage_iscsi_edit.php?id=<?=$i;?>"><img src="e.gif" title="<?=gettext("Edit initiator");?>" width="17" height="17" border="0">
              <a href="disks_manage_iscsi.php?act=del&id=<?=$i;?>" onclick="return confirm('<?=gettext("Do you really want to delete this initiator? All elements that still use it will become invalid (e.g. share)!");?>')"><img src="x.gif" title="<?=gettext("Delete initiator"); ?>" width="17" height="17" border="0"></a>
            </td>
          </tr>
          <?php $i++; endforeach; ?>
          <tr> 
            <td class="list" colspan="4"></td>
            <td class="list"><a href="disks_manage_iscsi_edit.php"><img src="plus.gif" title="<?=gettext("Add initiator");?>" width="17" height="17" border="0"></a></td>
			    </tr>
        </table>
      </form>
	  </td>
  </tr>
</table>
<?php include("fend.inc"); ?>
