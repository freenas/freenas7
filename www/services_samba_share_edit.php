#!/usr/local/bin/php
<?php
/*
	services_samba_share_edit.php
	Copyright © 2006-2008 Volker Theile (votdev@gmx.de)
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

$id = $_GET['id'];
if(isset($_POST['id']))
	$id = $_POST['id'];

$pgtitle = array(gettext("Services"),gettext("CIFS/SMB"),gettext("Share"),isset($id)?gettext("Edit"):gettext("Add"));

if (!is_array($config['mounts']['mount']))
	$config['mounts']['mount'] = array();

if(!is_array($config['samba']['share']))
	$config['samba']['share'] = array();

array_sort_key($config['mounts']['mount'], "devicespecialfile");
array_sort_key($config['samba']['share'], "name");

$a_mount = &$config['mounts']['mount'];
$a_share = &$config['samba']['share'];

if (isset($id) && $a_share[$id]) {
	$pconfig['name'] = $a_share[$id]['name'];
	$pconfig['path'] = $a_share[$id]['path'];
	$pconfig['comment'] = $a_share[$id]['comment'];
	$pconfig['browseable'] = isset($a_share[$id]['browseable']);
	$pconfig['inheritpermissions'] = isset($a_share[$id]['inheritpermissions']);
	$pconfig['recyclebin'] = isset($a_share[$id]['recyclebin']);
	$pconfig['hostsallow'] = $a_share[$id]['hostsallow'];
	$pconfig['hostsdeny'] = $a_share[$id]['hostsdeny'];
} else {
	$pconfig['name'] = "";
	$pconfig['path'] = "";
	$pconfig['comment'] = "";
	$pconfig['browseable'] = true;
	$pconfig['inheritpermissions'] = true;
	$pconfig['recyclebin'] = false;
	$pconfig['hostsallow'] = "";
	$pconfig['hostsdeny'] = "";
}

if($_POST) {
	unset($input_errors);

	$pconfig = $_POST;

	// Input validation.
	$reqdfields = explode(" ", "name comment");
	$reqdfieldsn = array(gettext("Name"), gettext("Comment"));
	do_input_validation($_POST, $reqdfields, $reqdfieldsn, &$input_errors);

	$reqdfieldst = explode(" ", "string string");
	do_input_validation_type($_POST, $reqdfields, $reqdfieldsn, $reqdfieldst, &$input_errors);

	if(!$input_errors) {
		$share = array();

		$share['name'] = $_POST['name'];
		$share['path'] = $_POST['path'];
		$share['comment'] = $_POST['comment'];
		$share['browseable'] = $_POST['browseable'] ? true : false;
		$share['inheritpermissions'] = $_POST['inheritpermissions'] ? true : false;
		$share['recyclebin'] = $_POST['recyclebin'] ? true : false;
		$share['hostsallow'] = $_POST['hostsallow'];
		$share['hostsdeny'] = $_POST['hostsdeny'];

		if (isset($id) && $a_share[$id])
			$a_share[$id] = $share;
		else
			$a_share[] = $share;

		touch($d_smbshareconfdirty_path);
		write_config();

    header("Location: services_samba_share.php");
		exit;
	}
}
?>
<?php include("fbegin.inc"); ?>
<table width="100%" border="0" cellpadding="0" cellspacing="0">
  <tr>
    <td class="tabnavtbl">
      <ul id="tabnav">
				<li class="tabinact"><a href="services_samba.php"><?=gettext("Settings");?></a></li>
				<li class="tabact"><a href="services_samba_share.php" style="color:black" title="<?=gettext("Reload page");?>"><?=gettext("Shares");?></a></li>
      </ul>
    </td>
  </tr>
  <tr>
    <td class="tabcont">
			<form action="services_samba_share_edit.php" method="post" name="iform" id="iform">
				<?php if ($input_errors) print_input_errors($input_errors); ?>
			  <table width="100%" border="0" cellpadding="6" cellspacing="0">
			  	<tr>
			      <td width="22%" valign="top" class="vncellreq"><?=gettext("Name");?></td>
			      <td width="78%" class="vtable">
			        <input name="name" type="text" class="formfld" id="name" size="30" value="<?=htmlspecialchars($pconfig['name']);?>">
			      </td>
			    </tr>
			    <tr>
			      <td width="22%" valign="top" class="vncellreq"><?=gettext("Comment");?></td>
			      <td width="78%" class="vtable">
			        <input name="comment" type="text" class="formfld" id="comment" size="30" value="<?=htmlspecialchars($pconfig['comment']);?>">
			      </td>
			    </tr>
			    <tr>
						<td width="22%" valign="top" class="vncellreq"><?=gettext("Path"); ?></td>
						<td width="78%" class="vtable">
							<input name="path" type="text" class="formfld" id="path" size="60" value="<?=htmlspecialchars($pconfig['path']);?>">
							<input name="browse" type="button" class="formbtn" id="Browse" onClick='ifield = form.path; filechooser = window.open("filechooser.php?p="+escape(ifield.value)+"&sd=/mnt", "filechooser", "scrollbars=yes,toolbar=no,menubar=no,statusbar=no,width=550,height=300"); filechooser.ifield = ifield; window.ifield = ifield;' value="..." \><br/>
							<span class="vexpl"><?=gettext("Path to be shared.");?></span>
					  </td>
					</tr>
			    <tr>
			      <td width="22%" valign="top" class="vncell"><?=gettext("Browseable");?></td>
			      <td width="78%" class="vtable">
			      	<input name="browseable" type="checkbox" id="browseable" value="yes" <?php if ($pconfig['browseable']) echo "checked"; ?>>
			      	<?=gettext("Set browseable");?><br>
			        <span class="vexpl"><?=gettext("This controls whether this share is seen in the list of available shares in a net view and in the browse list.");?></span>
			      </td>
			    </tr>
			    <tr>
			      <td width="22%" valign="top" class="vncell"><?=gettext("Inherit permissions");?></td>
			      <td width="78%" class="vtable">
			        <input name="inheritpermissions" type="checkbox" id="inheritpermissions" value="yes" <?php if ($pconfig['inheritpermissions']) echo "checked"; ?>>
			        <?=gettext("Enable permission inheritance");?><br/>
							<span class="vexpl"><?=gettext("The permissions on new files and directories are normally governed by create mask and directory mask but the inherit permissions parameter overrides this. This can be particularly useful on systems with many users to allow a single share to be used flexibly by each user.");?></span>
			      </td>
			    </tr>
			    <tr>
			      <td width="22%" valign="top" class="vncell"><?=gettext("Recycle bin");?></td>
			      <td width="78%" class="vtable">
			        <input name="recyclebin" type="checkbox" id="recyclebin" value="yes" <?php if ($pconfig['recyclebin']) echo "checked"; ?>>
			        <?=gettext("Enable recycle bin");?><br>
			        <span class="vexpl"><?=gettext("This will create a recycle bin on the share.");?></span>
			      </td>
			    </tr>
			    <tr>
			      <td width="22%" valign="top" class="vncell"><?=gettext("Hosts allow");?></td>
			      <td width="78%" class="vtable">
			        <input name="hostsallow" type="text" class="formfld" id="hostsallow" size="60" value="<?=htmlspecialchars($pconfig['hostsallow']);?>"><br/>
			        <span class="vexpl"><?=gettext("This option is a comma, space, or tab delimited set of hosts which are permitted to access this share. You can specify the hosts by name or IP number. Leave this field empty to use default settings.");?></span>
			      </td>
			    </tr>
			    <tr>
			      <td width="22%" valign="top" class="vncell"><?=gettext("Hosts deny");?></td>
			      <td width="78%" class="vtable">
			        <input name="hostsdeny" type="text" class="formfld" id="hostsdeny" size="60" value="<?=htmlspecialchars($pconfig['hostsdeny']);?>"><br/>
			        <span class="vexpl"><?=gettext("This option is a comma, space, or tab delimited set of host which are NOT permitted to access this share. Where the lists conflict, the allow list takes precedence. In the event that it is necessary to deny all by default, use the keyword ALL (or the netmask 0.0.0.0/0) and then explicitly specify to the hosts allow parameter those hosts that should be permitted access. Leave this field empty to use default settings.");?></span>
			      </td>
			    </tr>
			    <tr>
			      <td width="22%" valign="top">&nbsp;</td>
			      <td width="78%"> <input name="Submit" type="submit" class="formbtn" value="<?=((isset($id) && $a_share[$id]))?gettext("Save"):gettext("Add")?>">
			        <?php if (isset($id) && $a_share[$id]): ?>
			        <input name="id" type="hidden" value="<?=$id;?>">
			        <?php endif; ?>
			      </td>
			    </tr>
			  </table>
			</form>
		</td>
	</tr>
</table>
<?php include("fend.inc");?>
