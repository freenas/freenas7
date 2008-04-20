#!/usr/local/bin/php
<?php
/*
	system_sysctl.php
	Copyright © 2008 Nelson Silva (nsilva@hotlap.org)
	All rights reserved.

	part of FreeNAS (http://www.freenas.org)
	Copyright (C) 2005-2008 Olivier Cochard-Labbé <olivier@freenas.org>.
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

if (!is_array($config['system']['sysctl']['param']))
	$config['system']['sysctl']['param'] = array();

array_sort_key($config['system']['sysctl']['param'], "name");

$a_sysctlvar = &$config['system']['sysctl']['param'];

if ($_POST) {
	if ($_POST['apply']) {
		$retval = 0;
		if (!file_exists($d_sysrebootreqd_path)) {
			config_lock();
			$retval |= rc_update_service("sysctl");
			config_unlock();
		}
		$savemsg = get_std_save_message($retval);
		if ($retval == 0) {
			if (file_exists($d_sysctldirty_path))
				unlink($d_sysctldirty_path);
		}
	}
}

if ($_GET['act'] === "del") {
	if ($_GET['id'] === "all") {
		foreach ($a_sysctlvar as $sysctlvark => $sysctlvarv) {
			unset($a_sysctlvar[$sysctlvark]);
		}
		write_config();
		touch($d_sysctldirty_path);
		header("Location: system_sysctl.php");
		exit;
	} else {
		if ($a_sysctlvar[$_GET['id']]) {
			unset($a_sysctlvar[$_GET['id']]);
			write_config();
			touch($d_sysctldirty_path);
			header("Location: system_sysctl.php");
			exit;
		}
	}
}
?>
<?php include("fbegin.inc");?>
<table width="100%" border="0" cellpadding="0" cellspacing="0">
	<tr>
    <td class="tabnavtbl">
      <ul id="tabnav">
      	<li class="tabinact"><a href="system_advanced.php"><?=gettext("Advanced");?></a></li>
      	<li class="tabinact"><a href="system_email.php"><?=gettext("Email");?></a></li>
      	<li class="tabinact"><a href="system_proxy.php"><?=gettext("Proxy");?></a></li>
      	<li class="tabinact"><a href="system_swap.php"><?=gettext("Swap");?></a></li>
        <li class="tabinact"><a href="system_rc.php"><?=gettext("Command scripts");?></a></li>
        <li class="tabinact"><a href="system_cron.php"><?=gettext("Cron");?></a></li>
        <li class="tabinact"><a href="system_rcconf.php"><?=gettext("rc.conf");?></a></li>
        <li class="tabact"><a href="system_sysctl.php" title="<?=gettext("Reload page");?>"><?=gettext("sysctl.conf");?></a></li>
      </ul>
    </td>
  </tr>
  <tr>
    <td class="tabcont">
    	<form action="system_sysctl.php" method="post">
    		<?php if ($savemsg) print_info_box($savemsg);?>
	    	<?php if (file_exists($d_sysctldirty_path)):?><p>
	      <?php print_info_box_np(gettext("The configuration has been changed.<br>You must apply the changes in order for them to take effect."));?><br/>
	      <input name="apply" type="submit" class="formbtn" id="apply" value="<?=gettext("Apply changes");?>"></p>
	      <?php endif;?>
	      <table width="100%" border="0" cellpadding="0" cellspacing="0">
	        <tr>
	          <td width="40%" class="listhdrr"><?=gettext("MIB");?></td>
	          <td width="20%" class="listhdrr"><?=gettext("Value");?></td>
	          <td width="30%" class="listhdrr"><?=gettext("Comment");?></td>
	          <td width="10%" class="list"></td>
	        </tr>
	        <?php if($a_sysctlvar && is_array($a_sysctlvar)): ?>
				  <?php $i = 0; foreach($a_sysctlvar as $sysctlvarv): ?>
	        <tr>
	          <td class="listlr"><?=htmlspecialchars($sysctlvarv['name']);?>&nbsp;</td>
	          <td class="listr"><?=htmlspecialchars($sysctlvarv['value']);?>&nbsp;</td>
	          <td class="listr"><?=$sysctlvarv['comment'];?>&nbsp;</td>
	          <td valign="middle" nowrap class="list">
	            <a href="system_sysctl_edit.php?id=<?=$i;?>"><img src="e.gif" title="<?=gettext("Edit MIB");?>" width="17" height="17" border="0"></a>&nbsp;
	            <a href="system_sysctl.php?act=del&id=<?=$i;?>" onclick="return confirm('<?=gettext("Do you really want to delete this MIB?");?>')"><img src="x.gif" title="<?=gettext("Delete MIB");?>" width="17" height="17" border="0"></a>
	          </td>
	        </tr>
	        <?php $i++; endforeach;?>
	        <?php endif;?>
					<tr>
	          <td class="list" colspan="3"></td>
	          <td class="list"><a href="system_sysctl_edit.php"><img src="plus.gif" title="<?=gettext("Add MIB");?>" width="17" height="17" border="0"></a>&nbsp;
	          	<?php if(count($a_sysctlvar) > 0):?>
							<a href="system_sysctl.php?act=del&id=all" onclick="return confirm('<?=gettext("Do you really want to delete all MIBs?");?>')"><img src="x.gif" title="<?=gettext("Delete all MIBs");?>" width="17" height="17" border="0"></a>
							<?php endif;?>
						</td>
	        </tr>
	      </table>
	      <p>
					<span class="vexpl">
						<span class="red"><strong><?=gettext("Note");?>:</strong></span><br/>
						<?php echo gettext("These MIBs will be added to /etc/sysctl.conf. This allow you to make changes to a running system.");?>
					</span>
				</p>
			</form>
	  </td>
  </tr>
</table>
<?php include("fend.inc");?>
