#!/usr/local/bin/php
<?php
/*
	system_sysctl.php
	Copyright (C) 2008 Nelson Silva (nsilva@hotlap.org)
	All rights reserved.

	Modified by Volker Theile (votdev@gmx.de)

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

$pgtitle = array(gettext("System"), gettext("Advanced"), gettext("sysctl.conf"));

if ($_POST) {
	if ($_POST['apply']) {
		$retval = 0;
		if (!file_exists($d_sysrebootreqd_path)) {
			$retval |= ui_process_updatenotification("sysctl", "sysctl_process_updatenotification");
			config_lock();
			$retval |= rc_update_service("sysctl");
			config_unlock();
		}
		$savemsg = get_std_save_message($retval);
		if ($retval == 0) {
			ui_cleanup_updatenotification("sysctl");
		}
	}
}

if (!is_array($config['system']['sysctl']['param']))
	$config['system']['sysctl']['param'] = array();

array_sort_key($config['system']['sysctl']['param'], "name");
$a_sysctlvar = &$config['system']['sysctl']['param'];

if ($_GET['act'] === "del") {
	if ($_GET['id'] === "all") {
		foreach ($a_sysctlvar as $sysctlvark => $sysctlvarv) {
			ui_set_updatenotification("sysctl", UPDATENOTIFICATION_MODE_DIRTY, $a_sysctlvar[$sysctlvark]['uuid']);
		}
	} else {
		ui_set_updatenotification("sysctl", UPDATENOTIFICATION_MODE_DIRTY, $_GET['uuid']);
	}
	header("Location: system_sysctl.php");
	exit;
}

function sysctl_process_updatenotification($mode, $data) {
	global $config;

	$retval = 0;

	switch ($mode) {
		case UPDATENOTIFICATION_MODE_NEW:
		case UPDATENOTIFICATION_MODE_MODIFIED:
			break;
		case UPDATENOTIFICATION_MODE_DIRTY:
			if (is_array($config['system']['sysctl']['param'])) {
				$index = array_search_ex($data, $config['system']['sysctl']['param'], "uuid");
				if (false !== $index) {
					unset($config['system']['sysctl']['param'][$index]);
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
      	<li class="tabinact"><a href="system_advanced.php"><span><?=gettext("Advanced");?></span></a></li>
      	<li class="tabinact"><a href="system_email.php"><span><?=gettext("Email");?></span></a></li>
      	<li class="tabinact"><a href="system_proxy.php"><span><?=gettext("Proxy");?></span></a></li>
      	<li class="tabinact"><a href="system_swap.php"><span><?=gettext("Swap");?></span></a></li>
        <li class="tabinact"><a href="system_rc.php"><span><?=gettext("Command scripts");?></span></a></li>
        <li class="tabinact"><a href="system_cron.php"><span><?=gettext("Cron");?></span></a></li>
        <li class="tabinact"><a href="system_rcconf.php"><span><?=gettext("rc.conf");?></span></a></li>
        <li class="tabact"><a href="system_sysctl.php" title="<?=gettext("Reload page");?>"><span><?=gettext("sysctl.conf");?></span></a></li>
      </ul>
    </td>
  </tr>
  <tr>
    <td class="tabcont">
    	<form action="system_sysctl.php" method="post">
    		<?php if ($savemsg) print_info_box($savemsg);?>
	    	<?php if (ui_exists_updatenotification("sysctl")) print_config_change_box();?>
	      <table width="100%" border="0" cellpadding="0" cellspacing="0">
	        <tr>
	          <td width="40%" class="listhdrr"><?=gettext("MIB");?></td>
	          <td width="20%" class="listhdrr"><?=gettext("Value");?></td>
	          <td width="30%" class="listhdrr"><?=gettext("Comment");?></td>
	          <td width="10%" class="list"></td>
	        </tr>
				  <?php $i = 0; foreach($a_sysctlvar as $sysctlvarv):?>
				  <?php $notificationmode = ui_get_updatenotification_mode("sysctl", $sysctlvarv['uuid']);?>
	        <tr>
	          <td class="listlr"><?=htmlspecialchars($sysctlvarv['name']);?>&nbsp;</td>
	          <td class="listr"><?=htmlspecialchars($sysctlvarv['value']);?>&nbsp;</td>
	          <td class="listr"><?=htmlspecialchars($sysctlvarv['comment']);?>&nbsp;</td>
	          <?php if (UPDATENOTIFICATION_MODE_DIRTY != $notificationmode):?>
	          <td valign="middle" nowrap class="list">
	            <a href="system_sysctl_edit.php?id=<?=$i;?>"><img src="e.gif" title="<?=gettext("Edit MIB");?>" border="0"></a>
	            <a href="system_sysctl.php?act=del&uuid=<?=$sysctlvarv['uuid'];?>" onclick="return confirm('<?=gettext("Do you really want to delete this MIB?");?>')"><img src="x.gif" title="<?=gettext("Delete MIB");?>" border="0"></a>
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
	          <td class="list"><a href="system_sysctl_edit.php"><img src="plus.gif" title="<?=gettext("Add MIB");?>" border="0"></a>
	          	<?php if (!empty($a_sysctlvar)):?>
							<a href="system_sysctl.php?act=del&id=all" onclick="return confirm('<?=gettext("Do you really want to delete all MIBs?");?>')"><img src="x.gif" title="<?=gettext("Delete all MIBs");?>" border="0"></a>
							<?php endif;?>
						</td>
	        </tr>
	      </table>
	      <div id="remarks">
	      	<?php html_remark("note", gettext("Note"), gettext("These MIBs will be added to /etc/sysctl.conf. This allow you to make changes to a running system."));?>
	      </div>
			</form>
	  </td>
  </tr>
</table>
<?php include("fend.inc");?>
