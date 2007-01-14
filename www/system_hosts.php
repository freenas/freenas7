#!/usr/local/bin/php
<?php
/*
	system_hosts.php
	
	part of FreeNAS (http://www.freenas.org)
	Copyright (C) 2005-2007 Olivier Cochard-Labbe <olivier@freenas.org>.
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

$pgtitle = array(_SYSTEMROUTESPHP_NAME,_MENULEFT_SYSHOSTS);


if (!is_array($config['system']['hosts']))
	$config['system']['hosts'] = array();

hosts_sort();

$a_hosts = &$config['system']['hosts'];

if ($_POST) {

	$pconfig = $_POST;

	if ($_POST['apply']) {
		$retval = 0;
		if (!file_exists($d_sysrebootreqd_path)) {
			config_lock();
			/* reload all components that use host */
			$retval = system_hosts_generate();
			config_unlock();
		}
		$savemsg = get_std_save_message($retval);
		if ($retval == 0) {
			if (file_exists($d_hostsdirty_path))
				unlink($d_hostsdirty_path);
		}
	}
}

if ($_GET['act'] == "del") {
	if ($a_hosts[$_GET['id']]) {
		unset($a_hosts[$_GET['id']]);
		write_config();
		touch($d_hostsdirty_path);
		header("Location: system_hosts.php");
		exit;
	}
}
?>
<?php include("fbegin.inc"); ?>
<form action="system_hosts.php" method="post">
<?php if ($savemsg) print_info_box($savemsg); ?>
<?php if (file_exists($d_hostsdirty_path)): ?><p>
<?php print_info_box_np(_SYSTEMHOSTSPHP_MSG_CHANGED);?><br>
<input name="apply" type="submit" class="formbtn" id="apply" value="<?=_APPLY;?>"></p>
<?php endif; ?>
              <table width="100%" border="0" cellpadding="0" cellspacing="0">
                <tr>
                  <td width="25%" class="listhdrr"><?=_INTPHP_DHCPHOSTNAME;?></td>
                  <td width="30%" class="listhdrr"><?=_INTPHP_IP;?></td>
                  <td width="35%" class="listhdr"><?=_DESC;?></td>
                  <td width="10%" class="list"></td>
				</tr>
			  <?php $i = 0; foreach ($a_hosts as $host): ?>
                <tr>
                  <td class="listlr">
                    <?=htmlspecialchars($host['name']);?>
                  </td>
                  <td class="listr">
                    <?=htmlspecialchars($host['address']);?>
                  </td>
                  <td class="listbg">
                    <?=htmlspecialchars($host['descr']);?>&nbsp;
                  </td>
                  <td valign="middle" nowrap class="list"> <a href="system_hosts_edit.php?id=<?=$i;?>"><img src="e.gif" title="<?=_SYSTEMHOSTSPHP_EDITHOST;?>" width="17" height="17" border="0"></a>
                     &nbsp;<a href="system_hosts.php?act=del&id=<?=$i;?>" onclick="return confirm('<?=_SYSTEMHOSTSPHP_MSGCONFIRM;?>')"><img src="x.gif" title="<?=_SYSTEMHOSTSPHP_DELHOST;?>" width="17" height="17" border="0"></a></td>
				</tr>
			  <?php $i++; endforeach; ?>
                <tr> 
                  <td class="list" colspan="3"></td>
                  <td class="list"> <a href="system_hosts_edit.php"><img src="plus.gif" title="<?=_SYSTEMHOSTSPHP_ADDHOST;?>" width="17" height="17" border="0"></a></td>
				</tr>
              </table>
            </form>
<p><span class="vexpl"><span class="red"><strong>Note:<br>
                </strong></span><?=_SYSTEMROUTESPHP_TEXT;?></span></p>
<?php include("fend.inc"); ?>
