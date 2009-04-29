#!/usr/local/bin/php
<?php
/*
	disks_zfs_dataset_edit.php
	Copyright (c) 2008-2009 Volker Theile (votdev@gmx.de)
	All rights reserved.

	part of FreeNAS (http://www.freenas.org)
	Copyright (C) 2005-2009 Olivier Cochard-Labbe <olivier@freenas.org>.
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
require("zfs.inc");

$uuid = $_GET['uuid'];
if (isset($_POST['uuid']))
	$uuid = $_POST['uuid'];

$pgtitle = array(gettext("Disks"), gettext("ZFS"), gettext("Datasets"), gettext("Dataset"), isset($uuid) ? gettext("Edit") : gettext("Add"));

if (!isset($config['zfs']['pools']) || !is_array($config['zfs']['pools']['pool']))
	$config['zfs']['pools']['pool'] = array();

if (!isset($config['zfs']['datasets']) || !is_array($config['zfs']['datasets']['dataset']))
	$config['zfs']['datasets']['dataset'] = array();

array_sort_key($config['zfs']['pools']['pool'], "name");
array_sort_key($config['zfs']['datasets']['dataset'], "name");

$a_pool = &$config['zfs']['pools']['pool'];
$a_dataset = &$config['zfs']['datasets']['dataset'];

if (!isset($uuid) && (!sizeof($a_pool))) {
	$errormsg = sprintf(gettext("No configured pools. Please add new <a href=%s>pools</a> first."), "disks_zfs_zpool.php");
}

if (isset($uuid) && (FALSE !== ($cnid = array_search_ex($uuid, $a_dataset, "uuid")))) {
	$pconfig['uuid'] = $a_dataset[$cnid]['uuid'];
	$pconfig['name'] = $a_dataset[$cnid]['name'];
	$pconfig['pool'] = $a_dataset[$cnid]['pool'][0];
	$pconfig['compression'] = $a_dataset[$cnid]['compression'];
	$pconfig['canmount'] = isset($a_dataset[$cnid]['canmount']);
	$pconfig['readonly'] = isset($a_dataset[$cnid]['readonly']);
	$pconfig['xattr'] = isset($a_dataset[$cnid]['xattr']);
	$pconfig['quota'] = $a_dataset[$cnid]['quota'];
	$pconfig['desc'] = $a_dataset[$cnid]['desc'];
} else {
	$pconfig['uuid'] = uuid();
	$pconfig['name'] = "";
	$pconfig['pool'] = "";
	$pconfig['compression'] = "off";
	$pconfig['canmount'] = true;
	$pconfig['readonly'] = false;
	$pconfig['xattr'] = true;
	$pconfig['quota'] = "";
	$pconfig['desc'] = "";
}

if ($_POST) {
	unset($input_errors);
	$pconfig = $_POST;

	// Input validation
	$reqdfields = explode(" ", "name");
	$reqdfieldsn = array(gettext("Name"));
	$reqdfieldst = explode(" ", "alias");

	do_input_validation($_POST, $reqdfields, $reqdfieldsn, &$input_errors);
	do_input_validation_type($_POST, $reqdfields, $reqdfieldsn, $reqdfieldst, &$input_errors);

	if (!$input_errors) {
		$dataset = array();
		$dataset['uuid'] = $_POST['uuid'];
		$dataset['name'] = $_POST['name'];
		$dataset['pool'] = $_POST['pool'];
		$dataset['compression'] = $_POST['compression'];
		$dataset['canmount'] = $_POST['canmount'] ? true : false;
		$dataset['readonly'] = $_POST['readonly'] ? true : false;
		$dataset['quota'] = $_POST['quota'];
		$dataset['desc'] = $_POST['desc'];

		if (isset($uuid) && (FALSE !== $cnid)) {
			$mode = UPDATENOTIFY_MODE_MODIFIED;
			$a_dataset[$cnid] = $dataset;
		} else {
			$mode = UPDATENOTIFY_MODE_NEW;
			$a_dataset[] = $dataset;
		}

		updatenotify_set("zfsdataset", $mode, $dataset['uuid']);
		write_config();

		header("Location: disks_zfs_dataset.php");
		exit;
	}
}
?>
<?php include("fbegin.inc");?>
<script language="JavaScript">
<!--
function enable_change(enable_change) {
	document.iform.name.disabled = !enable_change;
	document.iform.pool.disabled = !enable_change;
}
// -->
</script>
<table width="100%" border="0" cellpadding="0" cellspacing="0">
	<tr>
		<td class="tabnavtbl">
			<ul id="tabnav">
				<li class="tabinact"><a href="disks_zfs_zpool.php"><span><?=gettext("Pools");?></span></a></li>
				<li class="tabact"><a href="disks_zfs_dataset.php" title="<?=gettext("Reload page");?>"><span><?=gettext("Datasets");?></span></a></li>
			</ul>
		</td>
	</tr>
	<tr>
		<td class="tabnavtbl">
			<ul id="tabnav">
				<li class="tabact"><a href="disks_zfs_dataset.php" title="<?=gettext("Reload page");?>"><span><?=gettext("Dataset");?></span></a></li>
				<li class="tabinact"><a href="disks_zfs_dataset_info.php"><span><?=gettext("Information");?></span></a></li>
			</ul>
		</td>
	</tr>
	<tr>
		<td class="tabcont">
			<form action="disks_zfs_dataset_edit.php" method="post" name="iform" id="iform">
				<?php if ($errormsg) print_error_box($errormsg);?>
				<?php if ($input_errors) print_input_errors($input_errors);?>
				<?php if (file_exists($d_sysrebootreqd_path)) print_info_box(get_std_save_message(0));?>
				<table width="100%" border="0" cellpadding="6" cellspacing="0">
					<?php html_inputbox("name", gettext("Name"), $pconfig['name'], "", true, 20);?>
					<?php $a_poollist = array(); foreach ($a_pool as $poolv) { $poolstatus = zfs_get_pool_list(); $poolstatus = $poolstatus[$poolv['name']]; $text = "{$poolv['name']}: {$poolstatus['size']}"; if (!empty($poolv['desc'])) { $text .= " ({$poolv['desc']})"; } $a_poollist[$poolv['name']] = htmlspecialchars($text); }?>
					<?php html_combobox("pool", gettext("Pool"), $pconfig['pool'], $a_poollist, "", true);?>
					<?php $a_compressionmode = array("on" => gettext("On"), "off" => gettext("Off"), "lzjb" => "lzjb", "gzip" => "gzip"); for ($n = 1; $n <= 9; $n++) { $mode = "gzip-{$n}"; $a_compressionmode[$mode] = $mode; }?>
					<?php html_combobox("compression", gettext("Compression"), $pconfig['compression'], $a_compressionmode, gettext("Controls the compression algorithm used	for this dataset. The 'lzjb' compression algorithm is optimized for performance while providing decent data compression. Setting compression to 'On' uses the 'lzjb' compression algorithm. You can specify the 'gzip' level by using the value 'gzip-N', where N is an integer from 1 (fastest) to 9 (best compression ratio). Currently, 'gzip' is equivalent to 'gzip-6'."), true);?>
					<?php html_checkbox("canmount", gettext("Canmount"), $pconfig['canmount'] ? true : false, gettext("If this property is disabled, the file system cannot be mounted."), "", false);?>
					<?php html_checkbox("readonly", gettext("Readonly"), $pconfig['readonly'] ? true : false, gettext("Controls whether this dataset can be modified."), "", false);?>
					<?php html_checkbox("xattr", gettext("Extended attributes"), $pconfig['xattr'] ? true : false, gettext("Enable extended attributes for this file system."), "", false);?>
					<?php html_inputbox("quota", gettext("Quota"), $pconfig['quota'], gettext("Limits the	amount of space a dataset and its descendants can consume. This property enforces a hard limit on the amount of space used. This	includes all space consumed by descendants, including file systems and snapshots. To specify the size use the following human-readable suffixes (for example, 'k', 'KB', 'M', 'Gb', etc.)."), false, 10);?>
					<?php html_inputbox("desc", gettext("Description"), $pconfig['desc'], gettext("You may enter a description here for your reference."), false, 40);?>
				</table>
				<div id="submit">
					<input name="Submit" type="submit" class="formbtn" value="<?=((isset($uuid) && (FALSE !== $cnid))) ? gettext("Save") : gettext("Add");?>" onClick="enable_change(true)">
					<input name="uuid" type="hidden" value="<?=$pconfig['uuid'];?>">
				</div>
			</form>
		</td>
	</tr>
</table>
<script language="JavaScript">
<!--
<?php if (isset($uuid) && (FALSE !== $cnid)):?>
<!-- Disable controls that should not be modified anymore in edit mode. -->
enable_change(false);
<?php endif;?>
//-->
</script>
<?php include("fend.inc");?>
