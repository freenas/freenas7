#!/usr/local/bin/php
<?php
/*
	services_iscsitarget_extent_edit.php
	Copyright (C) 2007-2009 Volker Theile (votdev@gmx.de)
	Copyright (C) 2009 Daisuke Aoyama (aoyama@peach.ne.jp)
	All rights reserved.

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

$pgtitle = array(gettext("Services"), gettext("iSCSI Target"), gettext("Extent"), isset($id) ? gettext("Edit") : gettext("Add"));

if (!is_array($config['iscsitarget']['extent']))
	$config['iscsitarget']['extent'] = array();

array_sort_key($config['iscsitarget']['extent'], "name");
$a_iscsitarget_extent = &$config['iscsitarget']['extent'];

if (isset($id) && $a_iscsitarget_extent[$id]) {
	$pconfig['uuid'] = $a_iscsitarget_extent[$id]['uuid'];
	$pconfig['name'] = $a_iscsitarget_extent[$id]['name'];
	$pconfig['path'] = $a_iscsitarget_extent[$id]['path'];
	$pconfig['size'] = $a_iscsitarget_extent[$id]['size'];
	$pconfig['sizeunit'] = $a_iscsitarget_extent[$id]['sizeunit'];
	$pconfig['type'] = $a_iscsitarget_extent[$id]['type'];
	$pconfig['comment'] = $a_iscsitarget_extent[$id]['comment'];

	if (!isset($pconfig['sizeunit']))
		$pconfig['sizeunit'] = "MB";
} else {
	// Find next unused ID.
	$extentid = 0;
	$a_id = array();
	foreach($a_iscsitarget_extent as $extent)
		$a_id[] = (int)str_replace("extent", "", $extent['name']); // Extract ID.
	while (true === in_array($extentid, $a_id))
		$extentid += 1;

	$pconfig['uuid'] = uuid();
	$pconfig['name'] = "extent{$extentid}";
	$pconfig['path'] = "";
	$pconfig['size'] = "";
	$pconfig['sizeunit'] = "MB";
	$pconfig['type'] = "file";
	$pconfig['comment'] = "";
}

if ($_POST) {
	unset($input_errors);
	unset($errormsg);

	$pconfig = $_POST;

	// Input validation.
	$reqdfields = explode(" ", "name path size");
	$reqdfieldsn = array(gettext("Extent name"), gettext("Path"), gettext("File size"));
	$reqdfieldst = explode(" ", "string string numericint");

	do_input_validation($_POST, $reqdfields, $reqdfieldsn, &$input_errors);
	do_input_validation_type($_POST, $reqdfields, $reqdfieldsn, $reqdfieldst, &$input_errors);

	if (!isset($id)) {
		$index = array_search_ex($pconfig['name'], $a_iscsitarget_extent, "name");
		if ($index !== false) {
			$input_errors[] = gettext("This name already exists.");
		}
	}

	// Check if path exists.
	if ("device" !== $_POST['type']) {
		$dirname = dirname($_POST['path']);
		if (!file_exists($dirname)) {
			$input_errors[] = gettext("The path '{$dirname}' does not exist.");
		}
	}

	if (!$input_errors) {
		$iscsitarget_extent = array();
		$iscsitarget_extent['uuid'] = $_POST['uuid'];
		$iscsitarget_extent['name'] = $_POST['name'];
		$iscsitarget_extent['path'] = $_POST['path'];
		$iscsitarget_extent['size'] = $_POST['size'];
		$iscsitarget_extent['sizeunit'] = $_POST['sizeunit'];
		$iscsitarget_extent['type'] = $_POST['type'];
		$iscsitarget_extent['comment'] = $_POST['comment'];

		if (isset($id) && $a_iscsitarget_extent[$id]) {
			$a_iscsitarget_extent[$id] = $iscsitarget_extent;
			$mode = UPDATENOTIFY_MODE_MODIFIED;
		} else {
			$a_iscsitarget_extent[] = $iscsitarget_extent;
			$mode = UPDATENOTIFY_MODE_NEW;
		}

		updatenotify_set("iscsitarget_extent", $mode, $iscsitarget_extent['uuid']);
		write_config();

		header("Location: services_iscsitarget_target.php");
		exit;
	}
}
?>
<?php include("fbegin.inc");?>
<form action="services_iscsitarget_extent_edit.php" method="post" name="iform" id="iform">
	<table width="100%" border="0" cellpadding="0" cellspacing="0">
	  <tr>
	    <td class="tabnavtbl">
	      <ul id="tabnav">
					<li class="tabinact"><a href="services_iscsitarget.php"><span><?=gettext("Settings");?></span></a></li>
					<li class="tabact"><a href="services_iscsitarget_target.php" title="<?=gettext("Reload page");?>"><span><?=gettext("Targets");?></span></a></li>
					<li class="tabinact"><a href="services_iscsitarget_pg.php"><span><?=gettext("Portals");?></span></a></li>
					<li class="tabinact"><a href="services_iscsitarget_ig.php"><span><?=gettext("Initiators");?></span></a></li>
					<li class="tabinact"><a href="services_iscsitarget_ag.php"><span><?=gettext("Auths");?></span></a></li>
	      </ul>
	    </td>
	  </tr>
	  <tr>
	    <td class="tabcont">
	      <?php if ($input_errors) print_input_errors($input_errors);?>
	      <table width="100%" border="0" cellpadding="6" cellspacing="0">
	      <?php html_inputbox("name", gettext("Extent Name"), $pconfig['name'], gettext("String identifier of the extent."), true, 10, isset($id));?>
	      <?php html_combobox("type", gettext("Type"), $pconfig['type'], array("file" => gettext("File")), gettext("Type used as extent. (File includes an emulated volume of ZFS)"), true);?>
	      <?php html_filechooser("path", "Path", $pconfig['path'], sprintf(gettext("File path (e.g. /mnt/sharename/extent/%s) used as extent."), $pconfig['name']), $g['media_path'], true);?>
	      <tr>
	        <td width="22%" valign="top" class="vncellreq"><?=gettext("File size");?></td>
	        <td width="78%" class="vtable">
	          <input name="size" type="text" class="formfld" id="size" size="10" value="<?=htmlspecialchars($pconfig['size']);?>">
	          <select name="sizeunit">
	            <option value="MB" <?php if ($pconfig['sizeunit'] === "MB") echo "selected";?>><?=htmlspecialchars(gettext("MiB"));?></option>
	            <option value="GB" <?php if ($pconfig['sizeunit'] === "GB") echo "selected";?>><?=htmlspecialchars(gettext("GiB"));?></option>
	            <option value="TB" <?php if ($pconfig['sizeunit'] === "TB") echo "selected";?>><?=htmlspecialchars(gettext("TiB"));?></option>
	          </select><br/>
	          <span class="vexpl"><?=gettext("Size offered to the initiator. (up to 8EiB=8388608TiB. actual size is depend on your disks.)");?></span>
	        </td>
	      </tr>
	      <?php html_inputbox("comment", gettext("Comment"), $pconfig['comment'], gettext("You may enter a description here for your reference."), false, 40);?>
	      </table>
	      <div id="submit">
	      <input name="Submit" type="submit" class="formbtn" value="<?=((isset($id) && $a_iscsitarget_extent[$id])) ? gettext("Save") : gettext("Add");?>">
	      <input name="uuid" type="hidden" value="<?=$pconfig['uuid'];?>">
	      <?php if (isset($id) && $a_iscsitarget_extent[$id]):?>
	      <input name="id" type="hidden" value="<?=$id;?>">
	      <?php endif;?>
	      </div>
	    </td>
	  </tr>
	</table>
</form>
<?php include("fend.inc");?>
