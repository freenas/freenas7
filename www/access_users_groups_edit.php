#!/usr/local/bin/php
<?php
/*
	access_users_groups_edit.php
	part of FreeNAS (http://www.freenas.org)
	Copyright (C) 2005-2008 Olivier Cochard-Labb� <olivier@freenas.org>.
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

$pgtitle = array(gettext("Access"),gettext("Groups"),isset($id)?gettext("Edit"):gettext("Add"));

if (!is_array($config['access']['group']))
	$config['access']['group'] = array();

array_sort_key($config['access']['group'], "name");

$a_group = &$config['access']['group'];
$a_group_system = system_get_group_list();

if (isset($id) && $a_group[$id]) {
	$pconfig['groupid'] = $a_group[$id]['id'];
	$pconfig['name'] = $a_group[$id]['name'];
	$pconfig['desc'] = $a_group[$id]['desc'];
} else {
	$pconfig['groupid'] = get_nextgroup_id();
	$pconfig['name'] = "";
	$pconfig['desc'] = "";
}

if ($_POST) {
	unset($input_errors);
	$pconfig = $_POST;

	/* input validation */
	$reqdfields = explode(" ", "name desc");
	$reqdfieldsn = array(gettext("Name"),gettext("Description"));
	do_input_validation($_POST, $reqdfields, $reqdfieldsn, &$input_errors);

	if (($_POST['name'] && !is_domain($_POST['name']))) {
		$input_errors[] = gettext("The group name contains invalid characters.");
	}

	if (($_POST['desc'] && !is_validdesc($_POST['desc']))) {
		$input_errors[] = gettext("The group description contains invalid characters.");
	}

	// Check for name conflicts. Only check if group is created.
	if ((is_array($a_group_system) && array_key_exists($_POST['name'], $a_group_system)) ||
		(false !== array_search_ex($_POST['name'], $a_group, "name"))) {
		$input_errors[] = gettext("This group already exists in the group list.");
	}

	// Validate if ID is unique. Only check if user is created.
	if (!isset($id) && (false !== array_search_ex($_POST['groupid'], $a_group, "id"))) {
		$input_errors[] = gettext("The unique group ID is already used.");
	}

	if (!$input_errors) {
		$groups = array();

		$groups['id'] = $_POST['groupid'];
		$groups['name'] = $_POST['name'];
		$groups['desc'] = $_POST['desc'];

		if (isset($id) && $a_group[$id]) {
			$a_group[$id] = $groups;
		} else {
			$a_group[] = $groups;
		}

		touch($d_groupconfdirty_path);

		write_config();

		header("Location: access_users_groups.php");
		exit;
	}
}

// Get next group id.
// Return next free user id.
function get_nextgroup_id() {
	global $config;

	// Get next free user id.
	exec("/usr/sbin/pw groupnext", $output);
	$output = explode(":", $output[0]);
	$id = intval($output[0]);

	// Check if id is already in usage. If the user did not press the 'Apply'
	// button 'pw' did not recognize that there are already several new users
	// configured because the user db is not updated until 'Apply' is pressed.
	$a_group = $config['access']['group'];
	if (false !== array_search_ex(strval($id), $a_group, "id")) {
		do {
			$id++; // Increase id until a unused one is found.
		} while (false !== array_search_ex(strval($id), $a_group, "id"));
	}

	return $id;
}
?>
<?php include("fbegin.inc");?>
<table width="100%" border="0" cellpadding="0" cellspacing="0">
  <tr>
		<td class="tabnavtbl">
  		<ul id="tabnav">
    		<li class="tabinact"><a href="access_users.php"><?=gettext("Users");?></a></li>
    		<li class="tabact"><a href="access_users_groups.php" style="color:black" title="<?=gettext("Reload page");?>"><?=gettext("Groups");?></a></li>
  		</ul>
  	</td>
	</tr>
  <tr>
    <td class="tabcont">
			<form action="access_users_groups_edit.php" method="post" name="iform" id="iform">
				<?php if ($input_errors) print_input_errors($input_errors); ?>
			  <table width="100%" border="0" cellpadding="6" cellspacing="0">
		      <tr>
		        <td width="22%" valign="top" class="vncellreq"><?=gettext("Name");?></td>
		        <td width="78%" class="vtable">
		          <input name="name" type="text" class="formfld" id="name" size="20" value="<?=htmlspecialchars($pconfig['name']);?>"><br/>
							<span class="vexpl"><?=gettext("Group name.");?></span>
						</td>
					</tr>
					<tr>
						<td width="22%" valign="top" class="vncellreq"><?=gettext("Group ID");?></td>
						<td width="78%" class="vtable">
							<input name="groupid" type="text" class="formfld" id="groupid" size="20" value="<?=htmlspecialchars($pconfig['groupid']);?>" <?php if (isset($id)) echo "readonly";?>><br/>
							<span class="vexpl"><?=gettext("Group numeric id.");?></span>
						</td>
					</tr>
					<tr>
		        <td width="22%" valign="top" class="vncellreq"><?=gettext("Description");?></td>
		        <td width="78%" class="vtable">
		          <input name="desc" type="text" class="formfld" id="desc" size="20" value="<?=htmlspecialchars($pconfig['desc']);?>"><br/>
							<span class="vexpl"><?=gettext("Group description.");?></span>
						</td>
					</tr>
					<tr>
		        <td width="22%" valign="top">&nbsp;</td>
		        <td width="78%"> <input name="Submit" type="submit" class="formbtn" value="<?=(isset($id))?gettext("Save"):gettext("Add");?>">
		          <?php if (isset($id) && $a_group[$id]):?>
		          <input name="id" type="hidden" value="<?=$id;?>">
		          <?php endif;?>
		        </td>
		      </tr>
		  	</table>
			</form>
		</td>
	</tr>
</table>
<?php include("fend.inc");?>
