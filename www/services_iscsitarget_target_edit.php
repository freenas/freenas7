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

$id = $_GET['id'];
if (isset($_POST['id']))
	$id = $_POST['id'];

$pgtitle = array(gettext("Services"), gettext("iSCSI Target"), gettext("Target"), isset($id) ? gettext("Edit") : gettext("Add"));

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

if (isset($id) && $a_iscsitarget_target[$id]) {
	$pconfig['uuid'] = $a_iscsitarget_target[$id]['uuid'];
	$pconfig['name'] = $a_iscsitarget_target[$id]['name'];
	$pconfig['alias'] = $a_iscsitarget_target[$id]['alias'];
	$pconfig['type'] = $a_iscsitarget_target[$id]['type'];
	$pconfig['flags'] = $a_iscsitarget_target[$id]['flags'];
	$pconfig['comment'] = $a_iscsitarget_target[$id]['comment'];

	$pconfig['storage'] = $a_iscsitarget_target[$id]['storage'];
	if (is_array($pconfig['storage']))
		$pconfig['storage'] = $pconfig['storage'][0];

	$pconfig['pgigmap'] = $a_iscsitarget_target[$id]['pgigmap'];
	$pconfig['agmap'] = $a_iscsitarget_target[$id]['agmap'];
	$pconfig['lunmap'] = $a_iscsitarget_target[$id]['lunmap'];
	$pconfig['portalgroup'] = $pconfig['pgigmap'][0]['pgtag'];
	$pconfig['initiatorgroup'] = $pconfig['pgigmap'][0]['igtag'];
	$pconfig['authgroup'] = $pconfig['agmap'][0]['agtag'];

	$pconfig['authmethod'] = $a_iscsitarget_target[$id]['authmethod'];
	$pconfig['digest'] = $a_iscsitarget_target[$id]['digest'];
	$pconfig['queuedepth'] = $a_iscsitarget_target[$id]['queuedepth'];
	$pconfig['inqvendor'] = $a_iscsitarget_target[$id]['inqvendor'];
	$pconfig['inqproduct'] = $a_iscsitarget_target[$id]['inqproduct'];
	$pconfig['inqrevision'] = $a_iscsitarget_target[$id]['inqrevision'];
	$pconfig['inqserial'] = $a_iscsitarget_target[$id]['inqserial'];

	/*
	if (!isset($pconfig['storage']))
		$pconfig['storage'] = $pconfig['lunmap'][0]['extentname'];
	*/
	if (!is_array($pconfig['lunmap'])) {
		$pconfig['lunmap'] = array();
		$pconfig['lunmap'][0]['lun'] = "0";
		$pconfig['lunmap'][0]['type'] = "Storage";
		$pconfig['lunmap'][0]['extentname'] = $pconfig['storage'];
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
	$pconfig['lunmap'] = $lunmap;

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

	if (!isset($id)) {
		foreach ($a_iscsitarget_target as $target) {
			$fullname = get_fulliqn($pconfig['name']);
			if (strcasecmp($fullname, get_fulliqn($target['name'])) == 0) {
				$input_errors[] = gettext("This name already exists.");
				break;
			}
		}
	}

	if (!$input_errors) {
		$iscsitarget_target = array();
		$iscsitarget_target['uuid'] = $_POST['uuid'];
		$iscsitarget_target['name'] = $_POST['name'];
		$iscsitarget_target['alias'] = $_POST['alias'];
		$iscsitarget_target['type'] = $_POST['type'];
		$iscsitarget_target['flags'] = $_POST['flags'];
		$iscsitarget_target['comment'] = $_POST['comment'];

		//$iscsitarget_target['storage'] = $_POST['storage'];

		$iscsitarget_target['authmethod'] = $_POST['authmethod'];
		$iscsitarget_target['digest'] = $_POST['digest'];
		$iscsitarget_target['queuedepth'] = $_POST['queuedepth'];
		$iscsitarget_target['inqvendor'] = $_POST['inqvendor'];
		$iscsitarget_target['inqproduct'] = $_POST['inqproduct'];
		$iscsitarget_target['inqrevision'] = $_POST['inqrevision'];
		$iscsitarget_target['inqserial'] = $_POST['inqserial'];

		$pgigmap = array();
		$pgigmap[0]['pgtag'] = $_POST['portalgroup'];
		$pgigmap[0]['igtag'] = $_POST['initiatorgroup'];
		$iscsitarget_target['pgigmap'] = $pgigmap;

		$agmap = array();
		$agmap[0]['agtag'] = $_POST['authgroup'];
		$iscsitarget_target['agmap'] = $agmap;

		$lunmap = array();
		$lunmap[0]['lun'] = "0";
		$lunmap[0]['type'] = "Storage";
		$lunmap[0]['extentname'] = $_POST['storage'];
		$iscsitarget_target['lunmap'] = $lunmap;

		if (isset($id) && $a_iscsitarget_target[$id]) {
			$a_iscsitarget_target[$id] = $iscsitarget_target;
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
      <?php if ($input_errors) print_input_errors($input_errors);?>
      <table width="100%" border="0" cellpadding="6" cellspacing="0">
      <?php html_inputbox("name", gettext("Target Name"), $pconfig['name'], gettext("Base Name will be appended automatically when starting without 'iqn.'."), true, 60, false);?>
      <?php html_inputbox("alias", gettext("Target Alias"), $pconfig['alias'], "", false, 60,false);?>
      <?php html_combobox("type", gettext("Type"), $pconfig['type'], array("Disk" => gettext("Disk")), "", true);?>
      <?php html_combobox("flags", gettext("Flags"), $pconfig['flags'], array("rw" => "rw", "ro" => "ro"), "", true);?>
      <?php
		$pg_list = array();
		//$pg_list['0'] = 'None';
		foreach($config['iscsitarget']['portalgroup'] as $pg) {
		  if ($pg['comment']) {
			  $l = sprintf("Tag%d (%s)", $pg['tag'], $pg['comment']);
		  } else {
			  $l = sprintf("Tag%d", $pg['tag']);
		  }
		  $pg_list[$pg['tag']] = htmlspecialchars($l);
		}
		html_combobox("portalgroup", gettext("Portal Group"), $pconfig['portalgroup'], $pg_list, "", true);
      ?>
      <?php
		$ig_list = array();
		//$ig_list['0'] = 'None';
		foreach($config['iscsitarget']['initiatorgroup'] as $ig) {
		  if ($ig['comment']) {
			  $l = sprintf("Tag%d (%s)", $ig['tag'], $ig['comment']);
		  } else {
			  $l = sprintf("Tag%d", $ig['tag']);
		  }
		  $ig_list[$ig['tag']] = htmlspecialchars($l);
		}
		html_combobox("initiatorgroup", gettext("Initiator Group"), $pconfig['initiatorgroup'], $ig_list, "", true);
      ?>
      <?php html_inputbox("comment", gettext("Comment"), $pconfig['comment'], gettext("You may enter a description here for your reference."), false, 40);?>

      <?php
			$a_storage = array();
			if (!isset($id)) {
				// Add
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
					$a_storage[$extent['name']] = htmlspecialchars(sprintf("%s (%s)", $extent['name'], $extent['path']));
				}
			} else {
				// Edit
				foreach ($pconfig['lunmap'] as $lunmap) {
					$index = array_search_ex($lunmap['extentname'], $a_iscsitarget_extent, "name");
					if (false !== $index) {
						$extent = $a_iscsitarget_extent[$index];
						$a_storage[$extent['name']] = htmlspecialchars(sprintf("%s (%s)", $extent['name'], $extent['path']));
					}
				}
			}
      ?>

      <tr>
        <td colspan="2" class="list" height="12"></td>
      </tr>
      <tr>
        <td colspan="2" valign="top" class="listtopic"><?=gettext("LUN")."0";?></td>
      </tr>
      <?php
			$index = array_search_ex("0", $pconfig['lunmap'], "lun");
			if (false !== $index) {
				html_combobox("storage", gettext("Storage"), $pconfig['lunmap'][$index]['extentname'], $a_storage, "", true);
			}
      ?>

      <tr>
        <td colspan="2" class="list" height="12"></td>
      </tr>
      <tr>
        <td colspan="2" valign="top" class="listtopic"><?=gettext("Advanced settings");?></td>
      </tr>
      <?php html_combobox("authmethod", gettext("Auth Method"), $pconfig['authmethod'], array("Auto" => gettext("Auto"), "CHAP" => gettext("CHAP"), "CHAP mutual" => gettext("mutual CHAP")), "", false);?>
      <?php
		$ag_list = array();
		$ag_list['0'] = 'None';
		foreach($config['iscsitarget']['authgroup'] as $ag) {
		  if ($ag['comment']) {
			  $l = sprintf("Tag%d (%s)", $ag['tag'], $ag['comment']);
		  } else {
			  $l = sprintf("Tag%d", $ag['tag']);
		  }
		  $ag_list[$ag['tag']] = htmlspecialchars($l);
		}
		html_combobox("authgroup", gettext("Auth Group"), $pconfig['authgroup'], $ag_list, "", false);
      ?>
      <?php html_combobox("digest", gettext("Initial Digest"), $pconfig['digest'], array("Auto" => gettext("Auto"), "Header" => gettext("Header digest"), "Data" => gettext("Data digest"), "Header Data" => gettext("Header and Data digest")), gettext("The initial digest mode negotiated with the initiator."), false);?>
      <?php html_inputbox("queuedepth", gettext("Queue Depth"), $pconfig['queuedepth'], gettext("0=disabled, 1-255=enabled command queuing with specified depth."), false, 10);?>
      <?php html_inputbox("inqvendor", gettext("Inquiry Vendor"), $pconfig['inqvendor'], sprintf(gettext("You may specify as SCSI INQUIRY data. Empty as defalut. (up to %d ASCII chars)"), 8), false, 20);?>
      <?php html_inputbox("inqproduct", gettext("Inquiry Product"), $pconfig['inqproduct'], sprintf(gettext("You may specify as SCSI INQUIRY data. Empty as defalut. (up to %d ASCII chars)"), 16), false, 20);?>
      <?php html_inputbox("inqrevision", gettext("Inquiry Revision"), $pconfig['inqrevision'], sprintf(gettext("You may specify as SCSI INQUIRY data. Empty as defalut. (up to %d ASCII chars)"), 4), false, 20);?>
      <?php html_inputbox("inqserial", gettext("Inquiry Serial"), $pconfig['inqserial'], sprintf(gettext("You may specify as SCSI INQUIRY data. Empty as defalut. (up to %d ASCII chars)"), 16), false, 20);?>
      <tr>
        <td colspan="2" class="list" height="12"></td>
      </tr>
      </table>
      <div id="submit">
      <input name="Submit" type="submit" class="formbtn" value="<?=((isset($id) && $a_iscsitarget_target[$id])) ? gettext("Save") : gettext("Add");?>">
      <input name="uuid" type="hidden" value="<?=$pconfig['uuid'];?>">
      <?php if (isset($id) && $a_iscsitarget_target[$id]):?>
      <input name="id" type="hidden" value="<?=$id;?>">
      <?php endif;?>
      </div>
    </td>
  </tr>
</table>
</form>
<?php include("fend.inc");?>
