#!/usr/local/bin/php
<?php
/*
	disks_manage_iscsi_edit.php
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

$pgtitle = array(gettext("Disks"),gettext("Management"),gettext("iSCSI Initiator"),isset($id)?gettext("Edit"):gettext("Add"));

if (!is_array($config['iscsiinit']['vdisk']))
	$config['iscsiinit']['vdisk'] = array();

array_sort_key($config['iscsiinit']['vdisk'], "name");

$a_iscsiinit = &$config['iscsiinit']['vdisk'];

if (isset($id) && $a_iscsiinit[$id]) {
	$pconfig['uuid'] = $a_iscsiinit[$id]['uuid'];
	$pconfig['name'] = $a_iscsiinit[$id]['name'];
	$pconfig['targetname'] = $a_iscsiinit[$id]['targetname'];
	$pconfig['targetaddress'] = $a_iscsiinit[$id]['targetaddress'];
	$pconfig['initiatorname'] = $a_iscsiinit[$id]['initiatorname'];
} else {
	$pconfig['uuid'] = uuid();
	$pconfig['name'] = "";
	$pconfig['targetname'] = "";
	$pconfig['targetaddress'] = "";
	$pconfig['initiatorname'] = "";
}

if ($_POST) {
	unset($input_errors);
	unset($errormsg);
	unset($do_crypt);

	$pconfig = $_POST;

	/* Check for duplicate disks */
	foreach ($a_iscsiinit as $iscsiinit) {
		if (isset($id) && ($a_iscsiinit[$id]) && ($a_iscsiinit[$id] === $iscsiinit))
			continue;

		if (($iscsiinit['targetname'] == $_POST['targetname']) && ($iscsiinit['targetaddress'] == $_POST['targetaddress'])) {
			$input_errors[] = gettext("This couple targetname/targetaddress already exists in the disk list.");
			break;
		}

		if ($iscsiinit['name'] == $_POST['name']) {
			$input_errors[] = gettext("This name already exists in the disk list.");
			break;
		}
	}

	// Input validation
	$reqdfields = explode(" ", "name targetname targetaddress initiatorname");
	$reqdfieldsn = array(gettext("Name"),gettext("Target name"),gettext("Target address"),gettext("Initiator name"));
	$reqdfieldst = explode(" ", "alias string string string");

	do_input_validation($_POST, $reqdfields, $reqdfieldsn, &$input_errors);
	do_input_validation_type($_POST, $reqdfields, $reqdfieldsn, $reqdfieldst, &$input_errors);

	if (!$input_errors) {
		$iscsiinit = array();
		$iscsiinit['uuid'] = $_POST['uuid'];
		$iscsiinit['name'] = $_POST['name'];
		$iscsiinit['targetname'] = $_POST['targetname'];
		$iscsiinit['targetaddress'] = $_POST['targetaddress'];
		$iscsiinit['initiatorname'] = $_POST['initiatorname'];

		if (isset($id) && $a_iscsiinit[$id]) {
			$a_iscsiinit[$id] = $iscsiinit;
			$mode = UPDATENOTIFY_MODE_MODIFIED;
		} else {
			$a_iscsiinit[] = $iscsiinit;
			$mode = UPDATENOTIFY_MODE_NEW;
		}

		updatenotify_set("iscsiinitiator", $mode, $iscsiinit['uuid']);
		write_config();

		header("Location: disks_manage_iscsi.php");
		exit;
	}
}
?>
<?php include("fbegin.inc"); ?>
<table width="100%" border="0" cellpadding="0" cellspacing="0">
<tr>
    <td class="tabnavtbl">
      <ul id="tabnav">
      	<li class="tabinact"><a href="disks_manage.php"><span><?=gettext("Management");?></span></a></li>
      	<li class="tabinact"><a href="disks_manage_smart.php"><span><?=gettext("S.M.A.R.T.");?></span></a></li>
				<li class="tabact"><a href="disks_manage_iscsi.php" title="<?=gettext("Reload page");?>"><span><?=gettext("iSCSI Initiator");?></span></a></li>
      </ul>
    </td>
  </tr>
  <tr>
    <td class="tabcont">
			<form action="disks_manage_iscsi_edit.php" method="post" name="iform" id="iform">
				<?php if ($input_errors) print_input_errors($input_errors); ?>
			  <table width="100%" border="0" cellpadding="6" cellspacing="0">
			     <tr>
			     <td width="22%" valign="top" class="vncellreq"><?=gettext("Name") ;?></td>
			      <td width="78%" class="vtable">
			        <input name="name" type="text" class="formfld" id="name" size="20" value="<?=htmlspecialchars($pconfig['name']);?>">
							<br><?=gettext("This is for information only (not using during iSCSI negociation)."); ?>
			      </td>
			    </tr>
					<tr>
			     <td width="22%" valign="top" class="vncellreq"><?=gettext("Initiator name") ;?></td>
			      <td width="78%" class="vtable">
			        <input name="initiatorname" type="text" class="formfld" id="initiatorname" size="40" value="<?=htmlspecialchars($pconfig['initiatorname']);?>">
							<br><?=gettext("This name is for example: iqn.2005-01.il.ac.huji.cs:somebody."); ?>
			      </td>
						</tr>
				    <tr>
			     <td width="22%" valign="top" class="vncellreq"><?=gettext("Target Name") ;?></td>
			      <td width="78%" class="vtable">
			        <input name="targetname" type="text" class="formfld" id="targetname" size="40" value="<?=htmlspecialchars($pconfig['targetname']);?>">
							<br><?=gettext("This name is for example: iqn.1994-04.org.netbsd.iscsi-target:target0."); ?>
			      </td>
			    </tr>
					<tr>
			     <td width="22%" valign="top" class="vncellreq"><?=gettext("Target address") ;?></td>
			      <td width="78%" class="vtable">
			        <input name="targetaddress" type="text" class="formfld" id="targetaddress" size="20" value="<?=htmlspecialchars($pconfig['targetaddress']);?>">
							<br><?=gettext("This the IP address or DNS name of the iSCSI target."); ?>
			      </td>
			    </tr>
			  </table>
				<div id="submit">
					<input name="Submit" type="submit" class="formbtn" value="<?=((isset($id) && $a_iscsiinit[$id])) ? gettext("Save") : gettext("Add");?>">
					<input name="uuid" type="hidden" value="<?=$pconfig['uuid'];?>">
					<?php if (isset($id) && $a_iscsiinit[$id]):?>
					<input name="id" type="hidden" value="<?=$id;?>">
					<?php endif;?>
				</div>
			</form>
		</td>
	</tr>
</table>
<?php include("fend.inc");?>
