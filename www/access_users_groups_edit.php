#!/usr/local/bin/php
<?php 
/*
	access_users_groups_edit.php
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

$id = $_GET['id'];
if (isset($_POST['id']))
	$id = $_POST['id'];

$pgtitle = array(gettext("Access"),gettext("Users"),gettext("Groups"),isset($id)?gettext("Edit"):gettext("Add"));
	
if (!is_array($config['access']['group']))
	$config['access']['group'] = array();

groups_sort();

$a_group = &$config['access']['group'];

if (isset($id) && $a_group[$id]) {
	$pconfig['name'] = $a_group[$id]['name'];
	$pconfig['desc'] = $a_group[$id]['desc'];
}

if ($_POST) {
	unset($input_errors);
	$pconfig = $_POST;

	/* input validation */
	$reqdfields = array();
	$reqdfieldsn = array();
	if ($_POST['enable']) {
		$reqdfields = array_merge($reqdfields, explode(" ", "name desc"));
		$reqdfieldsn = array_merge($reqdfieldsn, array(gettext("Name"),gettext("Description")));
	}
	
	do_input_validation($_POST, $reqdfields, $reqdfieldsn, &$input_errors);
	
	if (($_POST['name'] && !is_domain($_POST['name']))) {
		$input_errors[] = gettext("The Group name contains invalid characters.");
	}
	
	if (($_POST['desc'] && !is_validdesc($_POST['desc']))) {
		$input_errors[] = gettext("The Group desc contains invalid characters.");
	}

	/* check for name conflicts */
	foreach ($a_group as $group)
	{
		if (isset($id) && ($a_group[$id]) && ($a_group[$id] === $group))
			continue;

		if ($group['name'] == $_POST['name'])
		{
			$input_errors[] = gettext("This group already exists in the group list.");
			break;
		}
	}

	if (!$input_errors)
	{
		$groups = array();
		
		$groups['name'] = $_POST['name'];
		$groups['desc'] = $_POST['desc'];
		
		if (isset($id) && $a_group[$id])
			$a_group[$id] = $groups;
		else
		{
			$groups['id'] = $config['access']['groupid'];
			$config['access']['groupid'] ++;
			$a_group[] = $groups;
		}
		
		touch($d_groupconfdirty_path);
		
		//echo "Debug, a_disk:<br>";
		//print_r($a_disk);
		write_config();
		
		header("Location: access_users_groups.php");
		exit;
	}
}
?>
<?php include("fbegin.inc"); ?>
<?php if ($input_errors) print_input_errors($input_errors); ?>
<form action="access_users_groups_edit.php" method="post" name="iform" id="iform">
  <table width="100%" border="0" cellpadding="6" cellspacing="0">
      <tr> 
        <td width="22%" valign="top" class="vncellreq"><?=gettext("Name");?></td>
        <td width="78%" class="vtable"> 
          <input name="name" type="text" class="formfld" id="name" size="20" value="<?=htmlspecialchars($pconfig['name']);?>"><br>
					<?=gettext("Group name.");?>
				</td>
			</tr>
			<tr>
        <td width="22%" valign="top" class="vncellreq"><?=gettext("Description");?></td>
        <td width="78%" class="vtable"> 
          <input name="desc" type="text" class="formfld" id="desc" size="20" value="<?=htmlspecialchars($pconfig['desc']);?>"><br>
					<?=gettext("Group description.");?>
				</td>
			</tr>
			<tr> 
        <td width="22%" valign="top">&nbsp;</td>
        <td width="78%"> <input name="Submit" type="submit" class="formbtn" value="<?=(isset($id))?gettext("Save"):gettext("Add");?>"> 
          <?php if (isset($id) && $a_group[$id]): ?>
          <input name="id" type="hidden" value="<?=$id;?>"> 
          <?php endif; ?>
        </td>
      </tr>
  	</table>
</form>
<?php include("fend.inc"); ?>
