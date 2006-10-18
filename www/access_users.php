#!/usr/local/bin/php
<?php
/*
	access_users.php
	part of FreeNAS (http://www.freenas.org)
	Copyright (C) 2005-2006 Olivier Cochard-Labbé <olivier@freenas.org>.
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

$pgtitle = array(_ACCESS_NAME,_ACCESS_USERS);

if (!is_array($config['access']['user']))
	$config['access']['user'] = array();
	
users_sort();

$a_user_conf = &$config['access']['user'];

if ($_POST) {

	$pconfig = $_POST;

	if ($_POST['apply']) {
		$retval = 0;
		if (!file_exists($d_sysrebootreqd_path)) 
		{
			config_lock();
			/* Re-generate the config file */
			system_users_create();
			if (isset($config['samba']['enable']))
				system_user_samba();
			
			config_unlock();
		}
		$savemsg = get_std_save_message($retval);
		if ($retval == 0) {
			if (file_exists($d_userconfdirty_path))
				unlink($d_userconfdirty_path);
		}
	}
}

if ($_GET['act'] == "del") 
{
	if ($a_user_conf[$_GET['id']]) 
	{
		unset($a_user_conf[$_GET['id']]);
		
		/* Re-generate the config file */
		system_users_create();
		if (isset($config['samba']['enable']))
			system_user_samba();
		
		write_config();
		touch($d_userconfdirty_path);
		header("Location: access_users.php");
		exit;
	}
}


?>
<?php include("fbegin.inc"); ?>
<table width="100%" border="0" cellpadding="0" cellspacing="0">
  <tr><td class="tabnavtbl">
  <ul id="tabnav">
	<li class="tabact">Users</li>
	<li class="tabinact"><a href="access_users_groups.php"><?=_ACCESS_GROUPS;?></a></li>
  </ul>
  </td></tr>
  <tr> 
    <td class="tabcont">
<form action="access_users.php" method="post">
<?php if ($savemsg) print_info_box($savemsg); ?>
<?php if (file_exists($d_userconfdirty_path)): ?><p>
<?php print_info_box_np(_ACCESSUSERS_MSGCHANGED);?><br>
<input name="apply" type="submit" class="formbtn" id="apply" value="<?=_APPLY;?>"></p>
<?php endif; ?>

              <table width="100%" border="0" cellpadding="0" cellspacing="0">
                <tr>
                  <td width="20%" class="listhdrr"><?=_ACCESS_USER;?></td>
                  <td width="50%" class="listhdrr"><?=_ACCESS_FULLNAME;?></td>
                  <td width="20%" class="listhdrr"><?=_ACCESS_GROUP;?></td>
                  <td width="10%" class="list"></td>
				</tr>
			  <?php $i = 0; foreach ($a_user_conf as $user): ?>
                <tr>
                  <td class="listbg">
                    <?=htmlspecialchars($user['login']);?>&nbsp;
                  </td>
                  <td class="listbg">
                    <?=htmlspecialchars($user['fullname']);?>&nbsp;
                  </td>
                  <td class="listbg">
                    <?=htmlspecialchars($user['usergroup']);?>&nbsp;
                  </td>
                   <td valign="middle" nowrap class="list"><a href="access_users_edit.php?id=<?=$i;?>"><img src="e.gif" title="<?=_ACCESSUSERS_EDITUSER;?>" width="17" height="17" border="0"></a>&nbsp;
                   <a href="access_users.php?act=del&id=<?=$i;?>" onclick="return confirm('<?=_ACCESSUSERS_CONFDEL;?>')"><img src="x.gif" title="<?=_ACCESSUSERS_DELUSER;?>" width="17" height="17" border="0"></a></td>
				</tr>
			  <?php $i++; endforeach; ?>
                <tr> 
                  <td class="list" colspan="3"></td>
                  <td class="list"> <a href="access_users_edit.php"><img src="plus.gif" title="<?=_ACCESSUSERS_ADDUSER;?>" width="17" height="17" border="0"></a></td>
				</tr>
              </table>
            </form>
</td></tr></table>
<?php include("fend.inc"); ?>
