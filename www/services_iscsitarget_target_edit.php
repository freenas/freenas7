#!/usr/local/bin/php
<?php
/*
	services_iscsitarget_extent_edit.php
	Copyright © 2007 Volker Theile (votdev@gmx.de)
  All rights reserved.

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

$pgtitle = array(gettext("Services"),gettext("iSCSI Target"),gettext("Target"),isset($id)?gettext("Edit"):gettext("Add"));

if (!is_array($config['iscsitarget']['extent']))
	$config['iscsitarget']['extent'] = array();

if (!is_array($config['iscsitarget']['device']))
	$config['iscsitarget']['device'] = array();

if (!is_array($config['iscsitarget']['target']))
	$config['iscsitarget']['target'] = array();

array_sort_key($config['iscsitarget']['extent'], "name");
array_sort_key($config['iscsitarget']['device'], "name");
array_sort_key($config['iscsitarget']['extent'], "name");

$a_iscsitarget_extent = &$config['iscsitarget']['extent'];
$a_iscsitarget_device = &$config['iscsitarget']['device'];
$a_iscsitarget_target = &$config['iscsitarget']['target'];

if (!sizeof($a_iscsitarget_extent)) {
	$errormsg = gettext("You have to define some 'Extent' objects first.");
}

if (isset($id) && $a_iscsitarget_target[$id]) {
	$pconfig['name'] = $a_iscsitarget_target[$id]['name'];
	$pconfig['flags'] = $a_iscsitarget_target[$id]['flags'];
	$pconfig['storage'] = $a_iscsitarget_target[$id]['storage'];
	$pconfig['ipaddr'] = $a_iscsitarget_target[$id]['ipaddr'];
	$pconfig['subnet'] = $a_iscsitarget_target[$id]['subnet'];
} else {
	// Find next unused ID.
	$targetid = 0;
	foreach($a_iscsitarget_target as $target) {
		if (str_replace("target","",$target['name']) == $targetid)
			$targetid += 1;
		else
			break;
	}

	$pconfig['name'] = "target{$targetid}";
	$pconfig['flags'] = "rw";
	$pconfig['storage'] = "";
	$pconfig['network'] = "";
	$pconfig['ipaddr'] = "";
	$pconfig['subnet'] = "";
}

if ($_POST) {
	unset($input_errors);
	unset($errormsg);

	$pconfig = $_POST;

	/* input validation */
  $reqdfields = explode(" ", "ipaddr subnet");
  $reqdfieldsn = array(gettext("Authorised network"),gettext("Subnet bit count"));
  do_input_validation($_POST, $reqdfields, $reqdfieldsn, &$input_errors);

  if (($_POST['ipaddr'] && !is_ipaddr($_POST['ipaddr']))) {
		$input_errors[] = gettext("A valid network must be specified.");
	}

	if (($_POST['subnet'] && !is_numeric($_POST['subnet']))) {
		$input_errors[] = gettext("A valid network bit count must be specified.");
	}

	if (!$input_errors) {
		$iscsitarget_target = array();
		$iscsitarget_target['name'] = $_POST['name'];
		$iscsitarget_target['flags'] = $_POST['flags'];
		$iscsitarget_target['storage'] = $_POST['storage'];
		$iscsitarget_target['ipaddr'] = gen_subnet($_POST['ipaddr'], $_POST['subnet']);
		$iscsitarget_target['subnet'] = $_POST['subnet'];

		if (isset($id) && $a_iscsitarget_target[$id])
			$a_iscsitarget_target[$id] = $iscsitarget_target;
		else
			$a_iscsitarget_target[] = $iscsitarget_target;

		touch($d_iscsitargetdirty_path);

		write_config();

		header("Location: services_iscsitarget.php");
		exit;
	}
}
?>
<?php include("fbegin.inc");?>
<table width="100%" border="0" cellpadding="0" cellspacing="0">
  <tr>
		<td class="tabnavtbl">
  		<ul id="tabnav">
				<li class="tabact"><a href="services_iscsitarget.php" style="color:black" title="<?=gettext("Reload page");?>"><?=gettext("iSCSI Target");?></a></li>
  		</ul>
  	</td>
	</tr>
  <tr>
    <td class="tabcont">
			<form action="services_iscsitarget_target_edit.php" method="post" name="iform" id="iform">
				<?php if ($errormsg) print_error_box($errormsg);?>
				<?php if ($input_errors) print_input_errors($input_errors);?>
			  <table width="100%" border="0" cellpadding="6" cellspacing="0">
			  	<tr>
						<td width="22%" valign="top" class="vncellreq"><?=gettext("Device name");?></td>
						<td width="78%" class="vtable">
							<input name="name" type="text" class="formfld" id="name" size="10" value="<?=htmlspecialchars($pconfig['name']);?>" readonly>
					  </td>
					</tr>
					<tr>
			    	<td width="22%" valign="top" class="vncellreq"><?=gettext("Flags"); ?></td>
			      <td width="78%" class="vtable">
			  			<select name="flags" class="formfld" id="flags">
			          <?php $opts = array(gettext("rw")); $vals = explode(" ", "rw"); $i = 0;
								foreach ($opts as $opt): ?>
			          <option <?php if ($vals[$i] === $pconfig['flags']) echo "selected";?> value="<?=$vals[$i++];?>"><?=htmlspecialchars($opt);?></option>
			          <?php endforeach; ?>
			        </select>
			      </td>
			    </tr>
					<tr>
			      <td width="22%" valign="top" class="vncellreq"><?=gettext("Storage");?></td>
			      <td width="78%" class="vtable">
				      <?php $i = 0; foreach ($a_iscsitarget_extent as $extent):?>
							<input name="storage[]" id="<?=$i;?>" type="checkbox" value="<?=$extent['name'];?>" <?php if (is_array($pconfig['storage']) && in_array($extent['name'],$pconfig['storage'])) echo "checked";?>><?=htmlspecialchars($extent['name']);?><br>
				      <?php $i++; endforeach;?>
				      <?php $k = 0; foreach ($a_iscsitarget_device as $device):?>
							<input name="storage[]" id="<?=$k;?>" type="checkbox" value="<?=$device['name'];?>" <?php if (is_array($pconfig['storage']) && in_array($device['name'],$pconfig['storage'])) echo "checked";?>><?=htmlspecialchars($device['name']);?><br>
				      <?php $k++; endforeach;?>
				      <?php if (0 == $i && 0 == $k):?>&nbsp;<?php endif;?>
				    </td>
			    </tr>
					<tr>
			      <td width="22%" valign="top" class="vncellreq"><?=gettext("Authorised network") ; ?></td>
			      <td width="78%" class="vtable">
							<input name="ipaddr" type="text" class="formfld" id="ipaddr" size="20" value="<?=htmlspecialchars($pconfig['ipaddr']);?>"> /
			        <select name="subnet" class="formfld" id="subnet">
			          <?php for ($i = 32; $i >= 1; $i--): ?>
			          <option value="<?=$i;?>" <?php if ($i == $pconfig['subnet']) echo "selected"; ?>>
			          <?=$i;?>
			          </option>
			          <?php endfor; ?>
			        </select><br>
			        <span class="vexpl"><?=gettext("Network that is authorised to access to this iSCSI target.") ;?></span>
			      </td>
			    </tr>
			    <tr>
						<td width="22%" valign="top">&nbsp;</td>
						<td width="78%"><input name="Submit" type="submit" class="formbtn" value="<?=((isset($id) && $a_iscsitarget_target[$id]))?gettext("Save"):gettext("Add")?>">
						<?php if (isset($id) && $a_iscsitarget_target[$id]):?>
							<input name="id" type="hidden" value="<?=$id;?>">
						<?php endif;?>
						</td>
					</tr>
					<tr>
						<td width="22%" valign="top">&nbsp;</td>
						<td width="78%">
							<span class="vexpl">
								<span class="red"><strong><?=gettext("Information"); ?>:</strong></span><br>
								<?php echo gettext("At the highest level, a target is what is presented to the initiator, and is made up of one or more devices, and/or one or more extents.");?>
							</span>
						</td>
			    </tr>
			  </table>
			</form>
		</td>
	</tr>
</table>
<?php include("fend.inc");?>
