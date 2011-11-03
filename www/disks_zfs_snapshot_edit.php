#!/usr/local/bin/php
<?php
/*
	disks_zfs_snapshot_edit.php
	Modified for XHTML by Daisuke Aoyama (aoyama@peach.ne.jp)
	Copyright (C) 2010-2011 Daisuke Aoyama <aoyama@peach.ne.jp>.
	All rights reserved.

	Copyright (c) 2008-2010 Volker Theile (votdev@gmx.de)
	All rights reserved.

	part of FreeNAS (http://www.freenas.org)
	Copyright (C) 2005-2011 Olivier Cochard-Labbe <olivier@freenas.org>.
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
require("auth.inc");
require("guiconfig.inc");
require("zfs.inc");

$uuid = $_GET['uuid'];
if (isset($_POST['uuid']))
	$uuid = $_POST['uuid'];

$pgtitle = array(gettext("Disks"), gettext("ZFS"), gettext("Snapshots"), gettext("Snapshot"), gettext("Edit"));

if (!isset($config['zfs']['pools']) || !is_array($config['zfs']['pools']['pool']))
	$config['zfs']['pools']['pool'] = array();

array_sort_key($config['zfs']['pools']['pool'], "name");

$a_pool = &$config['zfs']['pools']['pool'];

function get_zfs_paths() {
	$result = array();
	mwexec2("zfs list -H -o name -t filesystem,volume 2>&1", $rawdata);
	foreach ($rawdata as $line) {
		$a = preg_split("/\t/", $line);
		$r = array();
		$name = $a[0];
		$r['path'] = $name;
		if (preg_match('/^([^\/\@]+)(\/([^\@]+))?$/', $name, $m)) {
			$r['pool'] = $m[1];
		} else {
			$r['pool'] = 'unknown'; // XXX
		}
		$result[] = $r;
	}
	return $result;
}
$a_path = get_zfs_paths();

if (!isset($uuid) && (!sizeof($a_pool))) {
	$errormsg = sprintf(gettext("No configured pools. Please add new <a href='%s'>pools</a> first."), "disks_zfs_zpool.php");
}

$snapshot = $_GET['snapshot'];
if (isset($_POST['snapshot']))
	$snapshot = $_POST['snapshot'];
$cnid = FALSE;
if (isset($snapshot) && !empty($snapshot)) {
	$pconfig['uuid'] = uuid();
	$pconfig['snapshot'] = $snapshot;
	if (preg_match('/^([^\/\@]+)(\/([^\@]+))?\@(.*)$/', $pconfig['snapshot'], $m)) {
		$pconfig['pool'] = $m[1];
		$pconfig['path'] = $m[1].$m[2];
		$pconfig['name'] = $m[4];
	} else {
		$pconfig['pool'] = "";
		$pconfig['path'] = "";
		$pconfig['name'] = "";
	}
	$pconfig['newpath'] = "";
	$pconfig['newname'] = "";
	$pconfig['recursive'] = false;
} else {
	// not supported
	$pconfig = array();
}

if ($_POST) {
	unset($input_errors);
	$pconfig = $_POST;

	if ($_POST['Cancel']) {
		header("Location: disks_zfs_snapshot.php");
		exit;
	}

	$action = $_POST['action'];
	if (empty($action)) {
		$input_errors[] = sprintf(gettext("The attribute '%s' is required."), gettext("Action"));
	} else if ($action == 'clone') {
		// Input validation
		$reqdfields = explode(" ", "newpath");
		$reqdfieldsn = array(gettext("Path"));
		$reqdfieldst = explode(" ", "string");

		do_input_validation($_POST, $reqdfields, $reqdfieldsn, &$input_errors);
		do_input_validation_type($_POST, $reqdfields, $reqdfieldsn, $reqdfieldst, &$input_errors);

		if (preg_match("/(\\s|\\@|\\'|\\\")+/", $_POST['newpath'])) {
			$input_errors[] = sprintf(gettext("The attribute '%s' contains invalid characters."), gettext("Path"));
		}

		if (!$input_errors) {
			$snapshot = array();
			$snapshot['uuid'] = $_POST['uuid'];
			$snapshot['pool'] = $_POST['pool'];
			$snapshot['path'] = $_POST['newpath'];
			$snapshot['name'] = $_POST['newname'];
			$snapshot['snapshot'] =	$_POST['snapshot'];
			//$snapshot['recursive'] = $_POST['recursive'] ? true : false;

			//$mode = UPDATENOTIFY_MODE_MODIFIED;
			//updatenotify_set("zfssnapshot", $mode, serialize($snapshot));
			//header("Location: disks_zfs_snapshot.php");
			//exit;

			$ret = zfs_snapshot_clone($snapshot);
			if ($ret['retval'] == 0) {
				header("Location: disks_zfs_snapshot.php");
				exit;
			}
			$errormsg = implode("\n", $ret['output']);
		}
	} else if ($action == 'delete') {
		// Input validation
		// nothing

		if (!$input_errors) {
			$snapshot = array();
			$snapshot['uuid'] = $_POST['uuid'];
			//$snapshot['pool'] = $_POST['pool'];
			//$snapshot['path'] = $_POST['path'];
			//$snapshot['name'] = $_POST['name'];
			$snapshot['snapshot'] =	$_POST['snapshot'];
			$snapshot['recursive'] = $_POST['recursive'] ? true : false;

			//$mode = UPDATENOTIFY_MODE_DIRTY;
			//updatenotify_set("zfssnapshot", $mode, serialize($snapshot));
			//header("Location: disks_zfs_snapshot.php");
			//exit;

			$ret = zfs_snapshot_destroy($snapshot);
			if ($ret['retval'] == 0) {
				header("Location: disks_zfs_snapshot.php");
				exit;
			}
			$errormsg = implode("\n", $ret['output']);
		}
	} else {
		$input_errors[] = sprintf(gettext("The attribute '%s' is invalid."), "action");
	}
}
?>
<?php include("fbegin.inc");?>
<script type="text/javascript">//<![CDATA[
function enable_change(enable_change) {
	document.iform.name.disabled = !enable_change;
}
function action_change() {
	showElementById('newpath_tr','hide');
	showElementById('recursive_tr','hide');
	var action = document.iform.action.value;
	switch (action) {
		case "clone":
			showElementById('newpath_tr','show');
			showElementById('recursive_tr','hide');
			break;
		case "delete":
			showElementById('newpath_tr','hide');
			showElementById('recursive_tr','show');
			break;
		default:
			break;
	}
}
//]]>
</script>
<table width="100%" border="0" cellpadding="0" cellspacing="0">
	<tr>
		<td class="tabnavtbl">
			<ul id="tabnav">
				<li class="tabinact"><a href="disks_zfs_zpool.php"><span><?=gettext("Pools");?></span></a></li>
				<li class="tabinact"><a href="disks_zfs_dataset.php"><span><?=gettext("Datasets");?></span></a></li>
				<li class="tabinact"><a href="disks_zfs_volume.php"><span><?=gettext("Volumes");?></span></a></li>
				<li class="tabact"><a href="disks_zfs_snapshot.php" title="<?=gettext("Reload page");?>"><span><?=gettext("Snapshots");?></span></a></li>
				<li class="tabinact"><a href="disks_zfs_config.php"><span><?=gettext("Configuration");?></span></a></li>
			</ul>
		</td>
	</tr>
	<tr>
		<td class="tabnavtbl">
			<ul id="tabnav2">
				<li class="tabact"><a href="disks_zfs_snapshot.php" title="<?=gettext("Reload page");?>"><span><?=gettext("Snapshot");?></span></a></li>
				<li class="tabinact"><a href="disks_zfs_snapshot_clone.php"><span><?=gettext("Clone");?></span></a></li>
				<li class="tabinact"><a href="disks_zfs_snapshot_auto.php"><span><?=gettext("Auto Snapshot");?></span></a></li>
				<li class="tabinact"><a href="disks_zfs_snapshot_info.php"><span><?=gettext("Information");?></span></a></li>
			</ul>
		</td>
	</tr>
	<tr>
		<td class="tabcont">
			<form action="disks_zfs_snapshot_edit.php" method="post" name="iform" id="iform">
				<?php if ($errormsg) print_error_box($errormsg);?>
				<?php if ($input_errors) print_input_errors($input_errors);?>
				<?php if (file_exists($d_sysrebootreqd_path)) print_info_box(get_std_save_message(0));?>
				<table width="100%" border="0" cellpadding="6" cellspacing="0">
					<?php html_text("snapshot", gettext("Snapshot"), htmlspecialchars($pconfig['snapshot']));?>
					<?php $a_action = array("clone" => gettext("Clone"), "delete" => gettext("Delete"));?>
					<?php html_combobox("action", gettext("Action"), $pconfig['action'], $a_action, "", true, false, "action_change()");?>
					<?php html_inputbox("newpath", gettext("Path"), $pconfig['newpath'], "", true, 20);?>
					<?php html_checkbox("recursive", gettext("Recursive"), $pconfig['recursive'] ? true : false, gettext("Deletes the recursive snapshot."), "", false);?>
				</table>
				<div id="submit">
					<input name="Submit" type="submit" class="formbtn" value="<?=gettext("Execute");?>" onclick="enable_change(true)" />
					<input name="Cancel" type="submit" class="formbtn" value="<?=gettext("Cancel");?>" />
					<input name="uuid" type="hidden" value="<?=$pconfig['uuid'];?>" />
					<input name="snapshot" type="hidden" value="<?=$pconfig['snapshot'];?>" />
					<input name="pool" type="hidden" value="<?=$pconfig['pool'];?>" />
					<input name="path" type="hidden" value="<?=$pconfig['path'];?>" />
				</div>
				<?php include("formend.inc");?>
			</form>
		</td>
	</tr>
</table>
<script type="text/javascript">
<!--
enable_change(true);
action_change();
//-->
</script>
<?php include("fend.inc");?>
