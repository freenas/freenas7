#!/usr/local/bin/php
<?php
/*
	access_users_edit.php
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

$id = $_GET['id'];
if (isset($_POST['id']))
	$id = $_POST['id'];

$pgtitle = array(gettext("Access"), gettext("Users"), isset($id) ? gettext("Edit") : gettext("Add"));

if (!is_array($config['access']['user']))
	$config['access']['user'] = array();

array_sort_key($config['access']['user'], "login");

$a_user = &$config['access']['user'];
$a_user_system = system_get_user_list();
$a_group = system_get_group_list();

if (isset($id) && $a_user[$id]) {
	$pconfig['uuid'] = $a_user[$id]['uuid'];
	$pconfig['login'] = $a_user[$id]['login'];
	$pconfig['fullname'] = $a_user[$id]['fullname'];
	$pconfig['password'] = $a_user[$id]['password'];
	$pconfig['passwordconf'] = $pconfig['password'];
	$pconfig['userid'] = $a_user[$id]['id'];
	$pconfig['primarygroup'] = $a_user[$id]['primarygroup'];
	$pconfig['group'] = $a_user[$id]['group'];
	$pconfig['fullshell'] = isset($a_user[$id]['fullshell']);
	$pconfig['homedir'] = $a_user[$id]['homedir'];
} else {
	$pconfig['uuid'] = uuid();
	$pconfig['primarygroup'] = $a_group['guest'];
	$pconfig['userid'] = get_nextuser_id();
}

if ($_POST) {
	unset($input_errors);
	$pconfig = $_POST;

	$reqdfields = explode(" ", "login fullname primarygroup userid");
	$reqdfieldsn = array(gettext("Login"),gettext("Full Name"),gettext("Primary Group"),gettext("User ID"));
	$reqdfieldst = explode(" ", "string string numeric numeric");

	do_input_validation($_POST, $reqdfields, $reqdfieldsn, &$input_errors);
	do_input_validation_type($_POST, $reqdfields, $reqdfieldsn, $reqdfieldst, &$input_errors);

	// Check for valid login name.
	if (($_POST['login'] && !is_validlogin($_POST['login']))) {
		$input_errors[] = gettext("The login name contains invalid characters.");
	}

	if (($_POST['login'] && in_array($_POST['login'], $reservedlogin))) {
		$input_errors[] = gettext("The login name is a reserved login name.");
	}

	// Check for valid Full name.
	if (($_POST['fullname'] && !is_validdesc($_POST['fullname']))) {
		$input_errors[] = gettext("The full name contains invalid characters.");
	}

	// Check for name conflicts. Only check if user is created.
	if (!isset($id) && ((is_array($a_user_system) && array_key_exists($_POST['login'], $a_user_system)) ||
		(false !== array_search_ex($_POST['login'], $a_user, "login")))) {
		$input_errors[] = gettext("This user already exists in the user list.");
	}

	// Check for a password mismatch.
	if ($_POST['password'] != $_POST['passwordconf']) {
		$input_errors[] = gettext("Password don't match.");
	}

	// Check if primary group is also selected in additional group.
	if (is_array($_POST['group']) && in_array($_POST['primarygroup'], $_POST['group'])) {
		$input_errors[] = gettext("Primary group is also selected in additional group.");
	}

	// Validate if ID is unique. Only check if user is created.
	if (!isset($id) && (false !== array_search_ex($_POST['userid'], $a_user, "id"))) {
		$input_errors[] = gettext("The unique user ID is already used.");
	}

	if (!$input_errors) {
		$users = array();
		$users['uuid'] = $_POST['uuid'];
		$users['login'] = $_POST['login'];
		$users['fullname'] = $_POST['fullname'];
		$users['password'] = $_POST['password'];
		$users['fullshell'] = $_POST['fullshell'] ? true : false;
		$users['primarygroup'] = $_POST['primarygroup'];
		if (is_array($_POST['group']))
			$users['group'] = $_POST['group'];
		$users['homedir'] = $_POST['homedir'];
		$users['id'] = $_POST['userid'];

		if (isset($id) && $a_user[$id]) {
			$a_user[$id] = $users;
			$mode = UPDATENOTIFY_MODE_MODIFIED;
		} else {
			$a_user[] = $users;
			$mode = UPDATENOTIFY_MODE_NEW;
		}

		updatenotify_set("userdb_user", $mode, $users['uuid']);
		write_config();

		header("Location: access_users.php");
		exit;
	}
}

// Get next user id.
// Return next free user id.
function get_nextuser_id() {
	global $config;

	// Get next free user id.
	exec("/usr/sbin/pw nextuser", $output);
	$output = explode(":", $output[0]);
	$id = intval($output[0]);

	// Check if id is already in usage. If the user did not press the 'Apply'
	// button 'pw' did not recognize that there are already several new users
	// configured because the user db is not updated until 'Apply' is pressed.
	$a_user = $config['access']['user'];
	if (false !== array_search_ex(strval($id), $a_user, "id")) {
		do {
			$id++; // Increase id until a unused one is found.
		} while (false !== array_search_ex(strval($id), $a_user, "id"));
	}

	return $id;
}
?>
<?php include("fbegin.inc");?>
<table width="100%" border="0" cellpadding="0" cellspacing="0">
  <tr>
		<td class="tabnavtbl">
  		<ul id="tabnav">
			<li class="tabact"><a href="access_users.php" title="<?=gettext("Reload page");?>"><span><?=gettext("Users");?></span></a></li>
    		<li class="tabinact"><a href="access_users_groups.php"><span><?=gettext("Groups");?></span></a></li>
  		</ul>
  	</td>
	</tr>
  <tr>
    <td class="tabcont">
      <form action="access_users_edit.php" method="post" name="iform" id="iform">
      	<?php if ($nogroup_errors) print_input_errors($nogroup_errors); ?>
				<?php if ($input_errors) print_input_errors($input_errors); ?>
        <table width="100%" border="0" cellpadding="6" cellspacing="0">
          <tr>
            <td width="22%" valign="top" class="vncellreq"><?=gettext("Login");?></td>
            <td width="78%" class="vtable">
              <input name="login" type="text" class="formfld" id="login" size="20" value="<?=htmlspecialchars($pconfig['login']);?>" <?php if (isset($id)) echo "readonly";?>><br/>
							<span class="vexpl"><?=gettext("Login name of user.");?></span>
            </td>
	       </tr>
	       <tr>
            <td width="22%" valign="top" class="vncellreq"><?=gettext("Full Name");?></td>
            <td width="78%" class="vtable">
              <input name="fullname" type="text" class="formfld" id="fullname" size="20" value="<?=htmlspecialchars($pconfig['fullname']);?>"><br/>
							<span class="vexpl"><?=gettext("User full name.");?></span>
            </td>
          </tr>
          <tr>
            <td width="22%" valign="top" class="vncellreq"><?=gettext("Password");?></td>
            <td width="78%" class="vtable">
              <input name="password" type="password" class="formfld" id="password" size="20" value="<?=htmlspecialchars($pconfig['password']);?>"><br/>
              <input name="passwordconf" type="password" class="formfld" id="passwordconf" size="20" value="<?=htmlspecialchars($pconfig['passwordconf']);?>">&nbsp;(<?=gettext("Confirmation");?>)<br/>
              <span class="vexpl"><?=gettext("User password.");?></span>
            </td>
          </tr>
					<tr>
						<td width="22%" valign="top" class="vncellreq"><?=gettext("User ID");?></td>
						<td width="78%" class="vtable">
							<input name="userid" type="text" class="formfld" id="userid" size="20" value="<?=htmlspecialchars($pconfig['userid']);?>" <?php if (isset($id)) echo "readonly";?>><br/>
							<span class="vexpl"><?=gettext("User numeric id.");?></span>
						</td>
					</tr>
          <tr>
            <td width="22%" valign="top" class="vncellreq"><?=gettext("Primary group");?></td>
            <td width="78%" class="vtable">
							<select name="primarygroup" class="formfld" id="primarygroup">
								<?php foreach ($a_group as $groupk => $groupv):?>
								<option value="<?=$groupv;?>" <?php if ("{$groupv}" === $pconfig['primarygroup']) echo "selected";?>><?=htmlspecialchars($groupk);?></option>
								<?php endforeach;?>
							</select><br/>
							<span class="vexpl"><?=gettext("Set the account's primary group to the given group.");?></span>
						</td>
					</tr>
					<tr>
            <td width="22%" valign="top" class="vncell"><?=gettext("Additional group");?></td>
            <td width="78%" class="vtable">
							<select multiple size="12" name="group[]" id="group">
								<?php foreach ($a_group as $groupk => $groupv):?>
								<option value="<?=$groupv;?>" <?php if (is_array($pconfig['group']) && in_array("{$groupv}", $pconfig['group'])) echo "selected";?>><?=htmlspecialchars($groupk);?></option>
								<?php endforeach;?>
							</select><br/>
							<span class="vexpl"><?=gettext("Set additional group memberships for this account.");?><br/>
							<?=gettext("Note: Ctrl-click (or command-click on the Mac) to select and deselect groups.");?></span>
						</td>
					</tr>
					<tr>
            <td width="22%" valign="top" class="vncell"><?=gettext("Home directory");?></td>
            <td width="78%" class="vtable">
              <input name="homedir" type="text" class="formfld" id="homedir" size="60" value="<?=htmlspecialchars($pconfig['homedir']);?>"><br>
              <span class="vexpl"><?=gettext("Enter the path to the home directory of that user. Leave this field empty to use default path /mnt.");?></span>
            </td>
          </tr>
					<tr>
					  <td width="22%" valign="top" class="vncell"><?=gettext("Shell access");?></td>
					  <td width="78%" class="vtable">
					  	<input name="fullshell" type="checkbox" value="yes" <?php if ($pconfig['fullshell']) echo "checked"; ?> onClick="enable_change(false)">
							<span class="vexpl"><?=gettext("Give full shell access to user.");?></span>
						</td>
				  </tr>
        </table>
				<div id="submit">
					<input name="Submit" type="submit" class="formbtn" value="<?=(isset($id)) ? gettext("Save") : gettext("Add");?>">
					<input name="uuid" type="hidden" value="<?=$pconfig['uuid'];?>">
					<?php if (isset($id) && $a_user[$id]):?>
					<input name="id" type="hidden" value="<?=$id;?>">
					<?php endif;?>
				</div>
			</form>
		</td>
	</tr>
</table>
<?php include("fend.inc");?>
