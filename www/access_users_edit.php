#!/usr/local/bin/php
<?php 
/*
	access_users_edit.php
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

$id = $_GET['id'];
if (isset($_POST['id']))
	$id = $_POST['id'];

$pgtitle = array(_ACCESS_NAME,_ACCESS_USERS,isset($id)?_EDIT:_ADD);	

if (!is_array($config['access']['user']))
	$config['access']['user'] = array();

users_sort();

if (!is_array($config['access']['group']))
	$nogroup_errors[] = _ACCESSUSERSEDIT_MSGGROUPFIRST;
	/* $config['access']['group'] = array(); */
else
	groups_sort();

$a_user = &$config['access']['user'];

$a_group = &$config['access']['group'];


if (isset($id) && $a_user[$id])
{
	$pconfig['login'] = $a_user[$id]['login'];
	$pconfig['fullname'] = $a_user[$id]['fullname'];
	$pconfig['usergroup'] = $a_user[$id]['usergroup'];
	$pconfig['usergroupid'] = $a_user[$id]['usergroupid'];
	$pconfig['password'] = $a_user[$id]['password'];
	$pconfig['passwordconf'] = $pconfig['password'];
	$pconfig['userid'] = $a_user[$id]['id'];
	$pconfig['fullshell'] = isset($a_user[$id]['fullshell']);

}

if ($_POST)
{

	unset($input_errors);
	$pconfig = $_POST;
	
	
	$reqdfields = array();
	$reqdfieldsn = array();
	if ($_POST['enable']) {
		$reqdfields = array_merge($reqdfields, explode(" ", "login fullname password passwordconf usergroup"));
		$reqdfieldsn = array_merge($reqdfieldsn, explode(",", "Login,Fullname,Password,Passwordconf,Usergroup"));
	}
	
	do_input_validation($_POST, $reqdfields, $reqdfieldsn, &$input_errors);
	
	/* check for valid login name */
	if (($_POST['login'] && !is_validlogin($_POST['login']))) {
		$input_errors[] = _ACCESSUSERSEDIT_MSGVALIDLOGIN;
	}
	
	if (($_POST['login'] && in_array($_POST['login'],$reservedlogin))) {
		$input_errors[] = _ACCESSUSERSEDIT_MSGVALIDLOGINRESERVED;
	}
	/* check for valid Full name */
	if (($_POST['fullname'] && !is_validdesc($_POST['fullname']))) {
		$input_errors[] = _ACCESSUSERSEDIT_MSGVALIDFULLNAME;
	}
	
	/* check for name conflicts */
	foreach ($a_user as $user)
	{
		if (isset($id) && ($a_user[$id]) && ($a_user[$id] === $user))
			continue;

		if ($user['login'] == $_POST['login'])
		{
			$input_errors[] = _ACCESSUSERSEDIT_MSGVALIDDUPLICATE;
			break;
		}

	}
	
	/* Check for a password mismatch */
	if ($_POST['password']!=$_POST['passwordconf']) 	{
			$input_errors[] = _ACCESSUSERSEDIT_MSGVALIDPASSERROR;
	}

	if (!$input_errors) 	{
		$users = array();
		
		$users['login'] = $_POST['login'];
		$users['fullname'] = $_POST['fullname'];
		$users['password'] = $_POST['password'];
		$users['usergroup'] = $_POST['usergroup'];
		$users['fullshell'] = $_POST['fullshell'] ? true : false;
		
		/* add the groupid for generate the password file */
		foreach ($a_group as $group) 		{
			if ($users['usergroup']==$group['name']) 		{
				$users['usergroupid']=$group['id'];
				break;
			}
		}
		
			
		if (isset($id) && $a_user[$id]) 		{
			$users['id'] = $_POST['userid'];
			$users['usergroupid'] = $_POST['usergroupid'];
			$a_user[$id] = $users;
		}
		else 		{
			$users['id'] = $config['access']['userid'];
			$config['access']['userid'] ++;
			$a_user[] = $users;
		}
	
		
		touch($d_userconfdirty_path);
		
		write_config();
		
		header("Location: access_users.php");
		exit;
	}
}
?>
<?php include("fbegin.inc"); ?>
<?php if ($nogroup_errors) print_input_errors($nogroup_errors); ?>
<?php if ($input_errors) print_input_errors($input_errors); ?>
            <form action="access_users_edit.php" method="post" name="iform" id="iform">
              <table width="100%" border="0" cellpadding="6" cellspacing="0">
                <tr> 
                  <td width="22%" valign="top" class="vncellreq"><?=_ACCESSUSERSEDIT_LOGIN ;?></td>
                  <td width="78%" class="vtable"> 
                    <input name="login" type="text" class="formfld" id="login" size="20" value="<?=htmlspecialchars($pconfig['login']);?>">
                    <br><?=_ACCESSUSERSEDIT_LOGINTEXT ;?></td>
				        </tr>
				        <tr> 
                  <td width="22%" valign="top" class="vncellreq"><?=_ACCESSUSERSEDIT_FULLNAME ;?></td>
                  <td width="78%" class="vtable"> 
                    <input name="fullname" type="text" class="formfld" id="fullname" size="20" value="<?=htmlspecialchars($pconfig['fullname']);?>">
                    <br><?=_ACCESSUSERSEDIT_FULLNAMETEXT ;?></td>
				      </tr>
              <tr> 
                  <td width="22%" valign="top" class="vncellreq"><?=_ACCESSUSERSEDIT_PASSWORD ;?></td>
                  <td width="78%" class="vtable">
                    <input name="password" type="password" class="formfld" id="password" size="20" value="<?=htmlspecialchars($pconfig['password']);?>"><br>
                    <input name="passwordconf" type="password" class="formfld" id="passwordconf" size="20" value="<?=htmlspecialchars($pconfig['passwordconf']);?>">&nbsp;(<?=_CONFIRMATION;?>)<br>
                    <?=_ACCESSUSERSEDIT_PASSWORDTEXT ;?>
                  </td>
                </tr>
                <tr>
                  <td width="22%" valign="top" class="vncellreq"><?=_ACCESSUSERSEDIT_GROUPMEMBER ;?></td>
                  <td width="78%" class="vtable">
		<select name="usergroup" class="formfld" id="usergroup">
		  <?php foreach ($a_group as $group): ?>
		  <option value="<?=$group['name'];?>" <?php if ($group['name'] == $pconfig['usergroup']) echo "selected";?>> 
		  <?php echo htmlspecialchars($group['name']);				  
		  ?>
		  </option>
		  <?php endforeach; ?>
			</tr>
			<tr> 
				  <td width="22%" valign="top" class="vncell"><?=_ACCESSUSERSEDIT_FULLSHELL ;?></td>
				  <td width="78%" class="vtable"> 
				  <input name="fullshell" type="checkbox" value="yes" <?php if ($pconfig['fullshell']) echo "checked"; ?> onClick="enable_change(false)"> <strong><?=_ENABLE;?></strong>
				  <br><?=_ACCESSUSERSEDIT_FULLSHELLTEXT;?></td>
                </tr>
			
		       <?php if (isset($id) && $a_user[$id]): ?>
               <input name="userid" type="hidden" class="formfld" id="userid" value="<?=$pconfig['userid'];?>">
               <input name="usergroupid" type="hidden" class="formfld" id="usergroupid" value="<?=$pconfig['usergroupid'];?>">
               <?php endif; ?>
                <tr> 
                  <td width="22%" valign="top">&nbsp;</td>
                  <td width="78%"> <input name="Submit" type="submit" class="formbtn" value="<?=(isset($id))?_SAVE:_ADD;?>"> 
                    <?php if (isset($id) && $a_user[$id]): ?>
                    <input name="id" type="hidden" value="<?=$id;?>"> 
                    <?php endif; ?>
                  </td>
                </tr>
              </table>
</form>
<?php include("fend.inc"); ?>
