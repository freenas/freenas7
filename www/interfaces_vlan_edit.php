#!/usr/local/bin/php
<?php
/*
	interfaces_vlan_edit.php
	part of m0n0wall (http://m0n0.ch/wall)

	Copyright (C) 2003-2005 Manuel Kasper <mk@neon1.net>.
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

$pgtitle = array(gettext("Interfaces"), gettext("Management"), gettext("VLAN"), gettext("Edit"));

if (!is_array($config['vlans']['vlan']))
	$config['vlans']['vlan'] = array();

$a_vlans = &$config['vlans']['vlan'];

if (isset($id) && $a_vlans[$id]) {
	$pconfig['if'] = $a_vlans[$id]['if'];
	$pconfig['tag'] = $a_vlans[$id]['tag'];
	$pconfig['desc'] = $a_vlans[$id]['desc'];
}

if ($_POST) {
	unset($input_errors);
	$pconfig = $_POST;

	// Input validation.
	$reqdfields = explode(" ", "if tag");
	$reqdfieldsn = array(gettext("Parent interface"), gettext("VLAN tag"));
	$reqdfieldst = explode(" ", "string numeric");

	do_input_validation($_POST, $reqdfields, $reqdfieldsn, &$input_errors);
	do_input_validation_type($_POST, $reqdfields, $reqdfieldsn, $reqdfieldst, &$input_errors);

	if (($_POST['tag'] < '1') || ($_POST['tag'] > '4094')) {
		$input_errors[] = gettext("The VLAN tag must be between 1 and 4094.");
	}

	foreach ($a_vlans as $vlan) {
		if (isset($id) && ($a_vlans[$id]) && ($a_vlans[$id] === $vlan))
			continue;

		if (($vlan['if'] == $_POST['if']) && ($vlan['tag'] == $_POST['tag'])) {
			$input_errors[] = sprintf(gettext("A VLAN with the tag %s is already defined on this interface."), $vlan['tag']);
			break;
		}
	}

	if (!$input_errors) {
		$vlan = array();
		$vlan['if'] = $_POST['if'];
		$vlan['tag'] = $_POST['tag'];
		$vlan['desc'] = $_POST['desc'];

		if (isset($id) && $a_vlans[$id])
			$a_vlans[$id] = $vlan;
		else
			$a_vlans[] = $vlan;

		write_config();
		touch($d_sysrebootreqd_path);

		header("Location: interfaces_vlan.php");
		exit;
	}
}
?>
<?php include("fbegin.inc");?>
<?php if ($input_errors) print_input_errors($input_errors);?>
<table width="100%" border="0" cellpadding="0" cellspacing="0">
	<tr>
		<td class="tabnavtbl">
		  <ul id="tabnav">
				<li class="tabinact"><a href="interfaces_assign.php"><?=gettext("Management"); ?> </a></li>
				<li class="tabact"><a href="interfaces_vlan.php" title="<?=gettext("Reload page");?>" ><?=gettext("VLAN");?></a></li>
			</ul>
		</td>
	</tr>
  <tr>
    <td class="tabcont">
			<form action="interfaces_vlan_edit.php" method="post" name="iform" id="iform">
			  <table width="100%" border="0" cellpadding="6" cellspacing="0">
					<?php $a_if = array(); foreach (get_interface_list() as $ifk => $ifv) { $a_if[$ifk] = "{$ifk} ({$ifv['mac']})"; };?>
					<?php html_combobox("if", gettext("Parent interface"), $pconfig['if'], $a_if, gettext(""), true);?>
					<?php html_inputbox("tag", gettext("VLAN tag"), $pconfig['tag'], gettext("802.1Q VLAN tag (between 1 and 4094)."), true, 6);?>
					<?php html_inputbox("desc", gettext("Description"), $pconfig['desc'], gettext("You may enter a description here for your reference."), false, 40);?>
			    <tr>
			      <td width="22%" valign="top">&nbsp;</td>
			      <td width="78%">
			        <input name="Submit" type="submit" class="formbtn" value="<?=((isset($id) && $a_vlans[$id])) ? gettext("Save") : gettext("Add");?>">
			        <?php if (isset($id) && $a_vlans[$id]): ?>
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
