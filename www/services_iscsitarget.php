#!/usr/local/bin/php
<?php
/*
	services_iscsitarget.php
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

$pgtitle = array(gettext("Services"),gettext("iSCSI Target"));

if (!is_array($config['iscsitarget']['vdisk']))
	$config['iscsitarget']['vdisk'] = array();

//iscsitarget_sort();

$a_iscsitarget = &$config['iscsitarget']['vdisk'];

if ($_POST) {
	$pconfig = $_POST;

	if ($_POST['apply']) {
		$retval = 0;
		if (!file_exists($d_sysrebootreqd_path)) {
			config_lock();
			$retval |= rc_update_service("iscsi_target");
			config_unlock();
		}
		$savemsg = get_std_save_message($retval);
		if ($retval == 0) {
			if (file_exists($d_iscsitargetdirty_path))
				unlink($d_iscsitargetdirty_path);
		}
	}
}
if ($_GET['act'] == "del")
{
	if ($a_iscsitarget[$_GET['id']]) {
			unset($a_iscsitarget[$_GET['id']]);
			write_config();
			touch($d_iscsitargetdirty_path);
			header("Location: services_iscsitarget.php");
			exit;
	}
}
?>
<?php include("fbegin.inc"); ?>
<table width="100%" border="0" cellpadding="0" cellspacing="0">
  <td class="tabcont">
    <form action="services_iscsitarget.php" method="post">
      <?php if ($savemsg) print_info_box($savemsg); ?>
      <?php if (file_exists($d_iscsitargetdirty_path)): ?><p>
      <?php print_info_box_np(gettext("The iSCSI target list has been changed.<br>You must apply the changes in order for them to take effect."));?><br>
      <input name="apply" type="submit" class="formbtn" id="apply" value="<?=gettext("Apply changes");?>"></p>
      <?php endif; ?>
      <table width="100%" border="0" cellpadding="0" cellspacing="0">
        <tr>
					<td width="25%" class="listhdrr"><?=gettext("Target name"); ?></td>
					<td width="25%" class="listhdrr"><?=gettext("Network"); ?></td>
					<td width="25%" class="listhdrr"><?=gettext("Mount used"); ?></td>
					<td width="25%" class="listhdrr"><?=gettext("Size"); ?></td>
					<td width="10%" class="list"></td>
        </tr>
			  <?php $i = 0; foreach($a_iscsitarget as $iscsitarget): ?>
        <tr>
					<td class="listr">iqn.1994-04.org.netbsd.iscsi-target:target<?=htmlspecialchars($i);?>&nbsp;</td>
          <td class="listr"><?=htmlspecialchars($iscsitarget['network']);?>&nbsp;</td>
					<td class="listr"><?=htmlspecialchars($iscsitarget['sharename']);?>&nbsp;</td>
					<td class="listr"><?=htmlspecialchars($iscsitarget['size']);?>&nbsp;</td>
          <td valign="middle" nowrap class="list"> 
            <a href="services_iscsitarget.php?act=del&id=<?=$i;?>" onclick="return confirm('<?=gettext("Do you really want to delete this target?");?>')"><img src="x.gif" title="<?=gettext("Delete target"); ?>" width="17" height="17" border="0"></a>
          </td>
        </tr>
        <?php $i++; endforeach; ?>
        <tr> 
          <td class="list" colspan="4"></td>
          <td class="list"><a href="services_iscsitarget_edit.php"><img src="plus.gif" title="<?=gettext("Add target");?>" width="17" height="17" border="0"></a></td>
		    </tr>
			</table>
		</form>
	  <p><span class="vexpl"><span class="red"><strong><?=gettext("Warning");?>:</strong></span><br><?=gettext("You must have a minimum of 256MB of RAM for using iSCSI-target.");?></p>
	</td>
</table>
<?php include("fend.inc"); ?>
