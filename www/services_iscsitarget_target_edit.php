#!/usr/local/bin/php
<?php
/*
	services_iscsitarget_target_edit.php
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

$uuid = $_GET['uuid'];
if (isset($_POST['uuid']))
	$uuid = $_POST['uuid'];

$pgtitle = array(gettext("Services"), gettext("iSCSI Target"), gettext("Target"), isset($uuid) ? gettext("Edit") : gettext("Add"));

/* currently support LUN0 only */
$MAX_LUNS = 1;

if (!is_array($config['iscsitarget']['portalgroup']))
	$config['iscsitarget']['portalgroup'] = array();

if (!is_array($config['iscsitarget']['initiatorgroup']))
	$config['iscsitarget']['initiatorgroup'] = array();

if (!is_array($config['iscsitarget']['authgroup']))
	$config['iscsitarget']['authgroup'] = array();

function cmp_tag($a, $b) {
	if ($a['tag'] == $b['tag'])
		return 0;
	return ($a['tag'] > $b['tag']) ? 1 : -1;
}
usort($config['iscsitarget']['portalgroup'], "cmp_tag");
usort($config['iscsitarget']['initiatorgroup'], "cmp_tag");
usort($config['iscsitarget']['authgroup'], "cmp_tag");

if (!is_array($config['iscsitarget']['extent']))
	$config['iscsitarget']['extent'] = array();

if (!is_array($config['iscsitarget']['device']))
	$config['iscsitarget']['device'] = array();

if (!is_array($config['iscsitarget']['target']))
	$config['iscsitarget']['target'] = array();

array_sort_key($config['iscsitarget']['extent'], "name");
array_sort_key($config['iscsitarget']['device'], "name");
//array_sort_key($config['iscsitarget']['target'], "name");

function get_fulliqn($name) {
	global $config;
	$fullname = $name;
	$basename = $config['iscsitarget']['nodebase'];
	if (strncasecmp("iqn.", $name, 4) != 0
		&& strncasecmp("eui.", $name, 4) != 0
		&& strncasecmp("naa.", $name, 4) != 0) {
		if (strlen($basename) != 0) {
			$fullname = $basename.":".$name;
		}
	}
	return $fullname;
}

function cmp_target($a, $b) {
	$aname = get_fulliqn($a['name']);
	$bname = get_fulliqn($b['name']);
	return strcasecmp($aname, $bname);
}
usort($config['iscsitarget']['target'], "cmp_target");

$a_iscsitarget_extent = &$config['iscsitarget']['extent'];
$a_iscsitarget_device = &$config['iscsitarget']['device'];
$a_iscsitarget_target = &$config['iscsitarget']['target'];

$errormsg = "";
if (count($config['iscsitarget']['portalgroup']) == 0) {
	$errormsg .= sprintf(gettext("No configured Portal Group. Please add new <a href=%s>Portal Group</a> first."), "services_iscsitarget_pg.php")."<br/>\n";
}
if (count($config['iscsitarget']['initiatorgroup']) == 0) {
	$errormsg .= sprintf(gettext("No configured Initiator Group. Please add new <a href=%s>Initiator Group</a> first."), "services_iscsitarget_ig.php")."<br/>\n";
}
if (count($config['iscsitarget']['extent']) == 0) {
	$errormsg .= sprintf(gettext("No configured Extent. Please add new <a href=%s>Extent</a> first."), "services_iscsitarget_target.php")."<br/>\n";
}

if (isset($uuid) && (FALSE !== ($cnid = array_search_ex($uuid, $a_iscsitarget_target, "uuid")))) {
	$pconfig['uuid'] = $a_iscsitarget_target[$cnid]['uuid'];
	$pconfig['name'] = $a_iscsitarget_target[$cnid]['name'];
	$pconfig['alias'] = $a_iscsitarget_target[$cnid]['alias'];
	$pconfig['type'] = $a_iscsitarget_target[$cnid]['type'];
	$pconfig['flags'] = $a_iscsitarget_target[$cnid]['flags'];
	$pconfig['comment'] = $a_iscsitarget_target[$cnid]['comment'];
	$pconfig['storage'] = $a_iscsitarget_target[$cnid]['storage'];
	if (is_array($pconfig['storage']))
		$pconfig['storage'] = $pconfig['storage'][0];
	$pconfig['pgigmap'] = $a_iscsitarget_target[$cnid]['pgigmap'];
	$pconfig['agmap'] = $a_iscsitarget_target[$cnid]['agmap'];
	$pconfig['lunmap'] = $a_iscsitarget_target[$cnid]['lunmap'];
	$pconfig['portalgroup'] = $pconfig['pgigmap'][0]['pgtag'];
	$pconfig['initiatorgroup'] = $pconfig['pgigmap'][0]['igtag'];
	$pconfig['authgroup'] = $pconfig['agmap'][0]['agtag'];

	$pconfig['authmethod'] = $a_iscsitarget_target[$cnid]['authmethod'];
	$pconfig['digest'] = $a_iscsitarget_target[$cnid]['digest'];
	$pconfig['queuedepth'] = $a_iscsitarget_target[$cnid]['queuedepth'];
	$pconfig['inqvendor'] = $a_iscsitarget_target[$cnid]['inqvendor'];
	$pconfig['inqproduct'] = $a_iscsitarget_target[$cnid]['inqproduct'];
	$pconfig['inqrevision'] = $a_iscsitarget_target[$cnid]['inqrevision'];
	$pconfig['inqserial'] = $a_iscsitarget_target[$cnid]['inqserial'];

	/*
	if (!isset($pconfig['storage']))
		$pconfig['storage'] = $pconfig['lunmap'][0]['extentname'];
	*/
	if (!is_array($pconfig['lunmap'])) {
		$pconfig['lunmap'] = array();
		$pconfig['lunmap'][0]['lun'] = "0";
		$pconfig['lunmap'][0]['type'] = "Storage";
		$pconfig['lunmap'][0]['extentname'] = $pconfig['storage'];
		for ($i = 1; $i < $MAX_LUNS; $i++) {
			$pconfig['lunmap'][$i]['lun'] = "$i";
			$pconfig['lunmap'][$i]['type'] = "Storage";
			$pconfig['lunmap'][$i]['extentname'] = "-";
		}
	}
	if (!isset($pconfig['type']))
		$pconfig['type'] = "Disk";
	if (!isset($pconfig['queuedepth']))
		$pconfig['queuedepth'] = 0;
} else {
	$type = "Disk";
	// Find next unused ID.
	$targetid = 0;
	$a_id = array();
	foreach($a_iscsitarget_target as $target) {
		$tmpa = explode(":", $target['name']);
		$name = $tmpa[count($tmpa)-1];
		$tmp = str_replace(strtolower($type), "", $name); // Extract ID.
		if (is_numeric($tmp))
			$a_id[] = (int)$tmp;
	}
	while (true === in_array($targetid, $a_id))
		$targetid += 1;

	$pconfig['uuid'] = uuid();
	$pconfig['name'] = strtolower($type)."$targetid";
	$pconfig['alias'] = "";
	$pconfig['type'] = "$type";
	$pconfig['flags'] = "rw";
	$pconfig['comment'] = "";

	$pconfig['storage'] = "";

	$pconfig['pgigmap'] = array();
	$pconfig['pgigmap'][0]['pgtag'] = 0;
	$pconfig['pgigmap'][0]['igtag'] = 0;
	$pconfig['agmap'] = array();
	$pconfig['agmap'][0]['agtag'] = 0;
	$pconfig['lunmap'] = array();
	$pconfig['lunmap'][0]['lun'] = "0";
	$pconfig['lunmap'][0]['type'] = "Storage";
	$pconfig['lunmap'][0]['extentname'] = "";
	for ($i = 1; $i < $MAX_LUNS; $i++) {
		$pconfig['lunmap'][$i]['lun'] = "$i";
		$pconfig['lunmap'][$i]['type'] = "Storage";
		$pconfig['lunmap'][$i]['extentname'] = "-";
	}

	$pconfig['authmethod'] = "Auto";
	$pconfig['digest'] = "Auto";
	$pconfig['queuedepth'] = 0;
	$pconfig['inqvendor'] = "";
	$pconfig['inqproduct'] = "";
	$pconfig['inqrevision'] = "";
	$pconfig['inqserial'] = "";
}

if ($_POST) {
	unset($input_errors);
	unset($errormsg);
	$pconfig = $_POST;

	if ($_POST['Cancel']) {
		header("Location: services_iscsitarget_target.php");
		exit;
	}

	$tgtname = $_POST['name'];
	$tgtname = preg_replace('/\s/', '', $tgtname);
	$pconfig['name'] = $tgtname;
	$pgigmap = array();
	$pgigmap[0]['pgtag'] = $_POST['portalgroup'];
	$pgigmap[0]['igtag'] = $_POST['initiatorgroup'];
	$pconfig['pgigmap'] = $pgigmap;
	$agmap = array();
	$agmap[0]['agtag'] = $_POST['authgroup'];
	$pconfig['agmap'] = $agmap;
	$lunmap = array();
	$lunmap[0]['lun'] = "0";
	$lunmap[0]['type'] = "Storage";
	$lunmap[0]['extentname'] = $_POST['storage'];
	for ($i = 1; $i < $MAX_LUNS; $i++) {
		if ($_POST['enable'.$i]
			&& $_POST['storage'.$i] !== "-") {
			$lunmap[$i]['lun'] = "$i";
			$lunmap[$i]['type'] = "Storage";
			$lunmap[$i]['extentname'] = $_POST['storage'.$i];
		}
	}
	$pconfig['lunmap'] = $lunmap;
	if ($_POST['queuedepth'] === "") {
		$queuedepth = 0;
	} else {
		$queuedepth = $_POST['queuedepth'];
	}

	// Input validation.
	$reqdfields = explode(" ", "name");
	$reqdfieldsn = array(gettext("Target name"));
	$reqdfieldst = explode(" ", "string");

	do_input_validation($_POST, $reqdfields, $reqdfieldsn, &$input_errors);
	do_input_validation_type($_POST, $reqdfields, $reqdfieldsn, $reqdfieldst, &$input_errors);

	$reqdfields = explode(" ", "type flags portalgroup initiatorgroup storage");
	$reqdfieldsn = array(gettext("Type"),
						 gettext("Flags"),
						 gettext("Portal Group"),
						 gettext("Initiator Group"),
						 gettext("Storage"));
	$reqdfieldst = explode(" ", "string string numericint numericint string");
	do_input_validation($_POST, $reqdfields, $reqdfieldsn, &$input_errors);
	do_input_validation_type($_POST, $reqdfields, $reqdfieldsn, $reqdfieldst, &$input_errors);

	$reqdfields = explode(" ", "authmethod authgroup digest queuedepth");
	$reqdfieldsn = array(gettext("Auth Method"),
						 gettext("Auth Group"),
						 gettext("Initial Digest"),
						 gettext("Queue Depth"));
	$reqdfieldst = explode(" ", "string numericint string numericint");
	//do_input_validation($_POST, $reqdfields, $reqdfieldsn, &$input_errors);
	do_input_validation_type($_POST, $reqdfields, $reqdfieldsn, $reqdfieldst, &$input_errors);

	if (strcasecmp("Auto", $pconfig['authmethod']) != 0
		&& $pconfig['authgroup'] == 0) {
		$input_errors[] = sprintf(gettext("The attribute '%s' is required."), gettext("Auth Group"));
	}

	if ($pconfig['queuedepth'] < 0 || $pconfig['queuedepth'] > 255) {
		$input_errors[] = gettext("The queuedepth range is invalid.");
	}
	if (strlen($pconfig['inqvendor']) > 8) {
		$input_errors[] = sprintf(gettext("%s is too long."), gettext("Inquiry Vendor"));
	}
	if (strlen($pconfig['inqproduct']) > 16) {
		$input_errors[] = sprintf(gettext("%s is too long."), gettext("Inquiry Product"));
	}
	if (strlen($pconfig['inqrevision']) > 4) {
		$input_errors[] = sprintf(gettext("%s is too long."), gettext("Inquiry Revision"));
	}
	if (strlen($pconfig['inqserial']) > 16) {
		$input_errors[] = sprintf(gettext("%s is too long."), gettext("Inquiry Serial"));
	}

	// Check for duplicates.
	if (!(isset($uuid) && (FALSE !== $cnid))) {
		$fullname = get_fulliqn($pconfig['name']);
		foreach ($a_iscsitarget_target as $target) {
			if (strcasecmp($fullname, get_fulliqn($target['name'])) == 0) {
				$input_errors[] = gettext("The target name already exists.");
				break;
			}
		}
	}

	// optional LUNs
	for ($i = 1; $i < $MAX_LUNS; $i++) {
		if (!isset($lunmap[$i]['extentname'])
			|| $lunmap[$i]['extentname'] === "-")
			continue;
		for ($j = 0; $j < $i; $j++) {
			if (!isset($lunmap[$j]['extentname'])
				|| $lunmap[$j]['extentname'] === "-")
				continue;
			if ($lunmap[$j]['extentname'] === $lunmap[$i]['extentname']) {
				$input_errors[] = sprintf(gettext("%s%d %s is already used by %s%d."), gettext("LUN"), $i, $lunmap[$i]['extentname'], gettext("LUN"), $j);
			}
		}
	}

	if (!$input_errors) {
		$iscsitarget_target = array();
		$iscsitarget_target['uuid'] = $_POST['uuid'];
		$iscsitarget_target['name'] = $tgtname;
		$iscsitarget_target['alias'] = $_POST['alias'];
		$iscsitarget_target['type'] = $_POST['type'];
		$iscsitarget_target['flags'] = $_POST['flags'];
		$iscsitarget_target['comment'] = $_POST['comment'];

		//$iscsitarget_target['storage'] = $_POST['storage'];

		$iscsitarget_target['authmethod'] = $_POST['authmethod'];
		$iscsitarget_target['digest'] = $_POST['digest'];
		$iscsitarget_target['queuedepth'] = $queuedepth;
		$iscsitarget_target['inqvendor'] = $_POST['inqvendor'];
		$iscsitarget_target['inqproduct'] = $_POST['inqproduct'];
		$iscsitarget_target['inqrevision'] = $_POST['inqrevision'];
		$iscsitarget_target['inqserial'] = $_POST['inqserial'];

		$iscsitarget_target['pgigmap'] = $pgigmap;
		$iscsitarget_target['agmap'] = $agmap;
		$iscsitarget_target['lunmap'] = $lunmap;

		if (isset($uuid) && (FALSE !== $cnid)) {
			$a_iscsitarget_target[$cnid] = $iscsitarget_target;
			$mode = UPDATENOTIFY_MODE_MODIFIED;
		} else {
			$a_iscsitarget_target[] = $iscsitarget_target;
			$mode = UPDATENOTIFY_MODE_NEW;
		}

		updatenotify_set("iscsitarget_target", $mode, $iscsitarget_target['uuid']);
		write_config();

		header("Location: services_iscsitarget_target.php");
		exit;
	}
}
?>
<?php include("fbegin.inc");?>
<script language="JavaScript">
<!--
function lun_change(idx) {
	var sw_name = "enable" + idx;
	var tr_name = "storage" + idx + "_tr";
	var endis = eval("document.iform." + sw_name + ".checked");

	if (endis) {
		showElementById(tr_name, 'show');
	} else {
		showElementById(tr_name, 'hide');
	}
}
//-->
</script>
<form action="services_iscsitarget_target_edit.php" method="post" name="iform" id="iform">
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
      <?php if ($errormsg) print_error_box($errormsg);?>
      <?php if ($input_errors) print_input_errors($input_errors);?>
      <table width="100%" border="0" cellpadding="6" cellspacing="0">
      <?php html_inputbox("name", gettext("Target Name"), $pconfig['name'], gettext("Base Name will be appended automatically when starting without 'iqn.'."), true, 60, false);?>
      <?php html_inputbox("alias", gettext("Target Alias"), $pconfig['alias'], gettext("Optional user-friendly string of the target."), false, 60, false);?>
      <?php html_combobox("type", gettext("Type"), $pconfig['type'], array("Disk" => gettext("Disk")), gettext("Logical Unit Type mapped to LUN."), true);?>
      <?php html_combobox("flags", gettext("Flags"), $pconfig['flags'], array("rw" => gettext("Read/Write (rw)"), "ro" => gettext("Read Only (ro)")), "", true);?>
      <?php
		$pg_list = array();
		//$pg_list['0'] = gettext("None");
		foreach($config['iscsitarget']['portalgroup'] as $pg) {
		  if ($pg['comment']) {
			  $l = sprintf(gettext("Tag%d (%s)"), $pg['tag'], $pg['comment']);
		  } else {
			  $l = sprintf(gettext("Tag%d"), $pg['tag']);
		  }
		  $pg_list[$pg['tag']] = htmlspecialchars($l);
		}
		html_combobox("portalgroup", gettext("Portal Group"), $pconfig['portalgroup'], $pg_list, gettext("The initiator can connect to the portals in specific Portal Group."), true);
      ?>
      <?php
			$ig_list = array();
			//$ig_list['0'] = gettext("None");
			foreach($config['iscsitarget']['initiatorgroup'] as $ig) {
			  if ($ig['comment']) {
				  $l = sprintf(gettext("Tag%d (%s)"), $ig['tag'], $ig['comment']);
			  } else {
				  $l = sprintf(gettext("Tag%d"), $ig['tag']);
			  }
			  $ig_list[$ig['tag']] = htmlspecialchars($l);
			}
			html_combobox("initiatorgroup", gettext("Initiator Group"), $pconfig['initiatorgroup'], $ig_list, gettext("The initiator can access to the target via the portals by authorised initiator names and networks in specific Initiator Group."), true);
      ?>
      <?php html_inputbox("comment", gettext("Comment"), $pconfig['comment'], gettext("You may enter a description here for your reference."), false, 40);?>
      <?php
			$a_storage_add = array();
			$a_storage_edit = array();
			foreach ($a_iscsitarget_extent as $extent) {
				$index = array_search_ex($extent['name'], $a_iscsitarget_target, "storage");
				if (false !== $index)
					continue;
				$index = array_search_ex($extent['name'], $a_iscsitarget_device, "storage");
				if (false !== $index)
					continue;
				foreach ($a_iscsitarget_target as $target) {
					if (isset($target['lunmap'])) {
						$index = array_search_ex($extent['name'], $target['lunmap'], "extentname");
						if (false !== $index)
							continue 2;
					}
				}
				$a_storage_add[$extent['name']] = htmlspecialchars(sprintf("%s (%s)", $extent['name'], $extent['path']));
			}
			if (isset($uuid) && (FALSE !== $cnid)) {
				// reload lunmap
				$pconfig['lunmap'] = $a_iscsitarget_target[$cnid]['lunmap'];
			}
			foreach ($pconfig['lunmap'] as $lunmap) {
				$index = array_search_ex($lunmap['extentname'], $a_iscsitarget_extent, "name");
				if (false !== $index) {
					$extent = $a_iscsitarget_extent[$index];
					$a_storage_edit[$extent['name']] = htmlspecialchars(sprintf("%s (%s)", $extent['name'], $extent['path']));
				}
			}
			if (!(isset($uuid) && (FALSE !== $cnid))) {
				// Add
				$a_storage = &$a_storage_add;
			} else {
				// Edit
				$a_storage = &$a_storage_edit;
			}
      ?>
      <?php html_separator();?>
      <?php html_titleline(sprintf("%s%d", gettext("LUN"), 0));?>
      <?php
			$index = array_search_ex("0", $pconfig['lunmap'], "lun");
			if (false !== $index) {
				html_combobox("storage", gettext("Storage"), $pconfig['lunmap'][$index]['extentname'], $a_storage, sprintf(gettext("The storage area mapped to LUN%d."), 0), true);
			}
      ?>
      <?php for ($i = 1; $i < $MAX_LUNS; $i++): ?>
      <?php $lenable=sprintf("enable%d", $i); ?>
      <?php $lstorage=sprintf("storage%d", $i); ?>
      <?php $a_storage_opt_add=array_merge(array("-" => gettext("None")), $a_storage_add); ?>
			<?php
			$enabled = 0;
			$index = array_search_ex("$i", $pconfig['lunmap'], "lun");
			if (false !== $index) {
				if ($pconfig['lunmap'][$index]['extentname'] !== "-") {
					$enabled = 1;
				}
			}
			?>
      <?php
			if (!(isset($uuid) && (FALSE !== $cnid))) {
				$a_storage_opt=array_merge(array("-" => gettext("None")), $a_storage_add);
			} else {
				$a_storage_opt=array_merge(array("-" => gettext("None")), $a_storage_edit);
			}
			?>
      <?php html_separator();?>
			<?php html_titleline_checkbox("$lenable", sprintf("%s%d", gettext("LUN"), $i), $enabled ? true : false, gettext("Enable"), "lun_change($i)");?>
      <?php
			$index = array_search_ex("$i", $pconfig['lunmap'], "lun");
			if (false !== $index) {
				html_combobox("$lstorage", gettext("Storage"), $pconfig['lunmap'][$index]['extentname'], $a_storage_opt, sprintf(gettext("The storage area mapped to LUN%d."), $i), true);
			} else {
				html_combobox("$lstorage", gettext("Storage"), "-", $a_storage_opt_add, sprintf(gettext("The storage area mapped to LUN%d."), $i), true);
			}
      ?>
      <?php endfor;?>
      <?php html_separator();?>
      <?php html_titleline(gettext("Advanced settings"));?>
      <?php html_combobox("authmethod", gettext("Auth Method"), $pconfig['authmethod'], array("Auto" => gettext("Auto"), "CHAP" => gettext("CHAP"), "CHAP mutual" => gettext("mutual CHAP")), gettext("The method can be accepted by the target. Auto means both none and authentication."), false);?>
      <?php
			$ag_list = array();
			$ag_list['0'] = gettext("None");
			foreach($config['iscsitarget']['authgroup'] as $ag) {
			  if ($ag['comment']) {
				  $l = sprintf(gettext("Tag%d (%s)"), $ag['tag'], $ag['comment']);
			  } else {
				  $l = sprintf(gettext("Tag%d"), $ag['tag']);
			  }
			  $ag_list[$ag['tag']] = htmlspecialchars($l);
			}
			html_combobox("authgroup", gettext("Auth Group"), $pconfig['authgroup'], $ag_list, gettext("The initiator can access to the target with correct user and secret in specific Auth Group."), false);
      ?>
      <?php html_combobox("digest", gettext("Initial Digest"), $pconfig['digest'], array("Auto" => gettext("Auto"), "Header" => gettext("Header digest"), "Data" => gettext("Data digest"), "Header Data" => gettext("Header and Data digest")), gettext("The initial digest mode negotiated with the initiator."), false);?>
      <?php html_inputbox("queuedepth", gettext("Queue Depth"), $pconfig['queuedepth'], gettext("0=disabled, 1-255=enabled command queuing with specified depth."), false, 10);?>
      <?php html_inputbox("inqvendor", gettext("Inquiry Vendor"), $pconfig['inqvendor'], sprintf(gettext("You may specify as SCSI INQUIRY data. Empty as defalut. (up to %d ASCII chars)"), 8), false, 20);?>
      <?php html_inputbox("inqproduct", gettext("Inquiry Product"), $pconfig['inqproduct'], sprintf(gettext("You may specify as SCSI INQUIRY data. Empty as defalut. (up to %d ASCII chars)"), 16), false, 20);?>
      <?php html_inputbox("inqrevision", gettext("Inquiry Revision"), $pconfig['inqrevision'], sprintf(gettext("You may specify as SCSI INQUIRY data. Empty as defalut. (up to %d ASCII chars)"), 4), false, 20);?>
      <?php html_inputbox("inqserial", gettext("Inquiry Serial"), $pconfig['inqserial'], sprintf(gettext("You may specify as SCSI INQUIRY data. Empty as defalut. (up to %d ASCII chars)"), 16), false, 20);?>
      </table>
      <div id="submit">
	      <input name="Submit" type="submit" class="formbtn" value="<?=(isset($uuid) && (FALSE !== $cnid)) ? gettext("Save") : gettext("Add")?>">
	      <input name="Cancel" type="submit" class="formbtn" value="<?=gettext("Cancel");?>">
	      <input name="uuid" type="hidden" value="<?=$pconfig['uuid'];?>">
      </div>
    </td>
  </tr>
</table>
</form>
<script language="JavaScript">
<!--
<?php
	for ($i = 1; $i < $MAX_LUNS; $i++) {
		echo "lun_change($i)\n";
	}
?>
//-->
</script>
<?php include("fend.inc");?>
