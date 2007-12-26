#!/usr/local/bin/php
<?php
/*
	services_iscsitarget_extent_edit.php
	Copyright � 2007 Volker Theile (votdev@gmx.de)
	All rights reserved.

	part of FreeNAS (http://www.freenas.org)
	Copyright (C) 2005-2007 Olivier Cochard-Labb� <olivier@freenas.org>.
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

$pgtitle = array(gettext("Services"),gettext("iSCSI Target"),gettext("Extent"),isset($id)?gettext("Edit"):gettext("Add"));

if (!is_array($config['iscsitarget']['extent']))
	$config['iscsitarget']['extent'] = array();

array_sort_key($config['iscsitarget']['extent'], "name");

$a_iscsitarget_extent = &$config['iscsitarget']['extent'];

if (isset($id) && $a_iscsitarget_extent[$id]) {
	$pconfig['name'] = $a_iscsitarget_extent[$id]['name'];
	$pconfig['path'] = $a_iscsitarget_extent[$id]['path'];
	$pconfig['size'] = $a_iscsitarget_extent[$id]['size'];
} else {
	// Find next unused ID.
	$extentid = 0;
	$a_id = array();
	foreach($a_iscsitarget_extent as $extent)
		$a_id[] = (int)str_replace("extent", "", $extent['name']); // Extract ID.
	asort($a_id); // Sort array.
	while (true === in_array($extentid, $a_id))
		$extentid += 1;

	$pconfig['name'] = "extent{$extentid}";
	$pconfig['path'] = "";
	$pconfig['size'] = "";
}

if ($_POST) {
	unset($input_errors);
	unset($errormsg);

	$pconfig = $_POST;

	// Input validation.
	$reqdfields = explode(" ", "path size");
	$reqdfieldsn = array(gettext("Path"),gettext("File size"));
	$reqdfieldst = explode(" ", "string numeric");
	
	do_input_validation($_POST, $reqdfields, $reqdfieldsn, &$input_errors);
	do_input_validation_type($_POST, $reqdfields, $reqdfieldsn, $reqdfieldst, &$input_errors);

	if (!$input_errors) {
		$iscsitarget_extent = array();
		$iscsitarget_extent['name'] = $_POST['name'];
		$iscsitarget_extent['path'] = $_POST['path'];
		$iscsitarget_extent['size'] = $_POST['size'];

		if (isset($id) && $a_iscsitarget_extent[$id])
			$a_iscsitarget_extent[$id] = $iscsitarget_extent;
		else
			$a_iscsitarget_extent[] = $iscsitarget_extent;

		touch($d_iscsitargetdirty_path);

		write_config();

		header("Location: services_iscsitarget.php");
		exit;
	}
}
?>
<?php include("fbegin.inc");?>
<form action="services_iscsitarget_extent_edit.php" method="post" name="iform" id="iform">
	<table width="100%" border="0" cellpadding="0" cellspacing="0">
		<tr>
			<td class="tabcont">
				<?php if ($input_errors) print_input_errors($input_errors);?>
				<table width="100%" border="0" cellpadding="6" cellspacing="0">
					<tr>
						<td width="22%" valign="top" class="vncellreq"><?=gettext("Extent name");?></td>
						<td width="78%" class="vtable">
							<input name="name" type="text" class="formfld" id="name" size="10" value="<?=htmlspecialchars($pconfig['name']);?>" readonly>
						</td>
					</tr>
					<tr>
						<td width="22%" valign="top" class="vncellreq"><?=gettext("Path");?></td>
						<td width="78%" class="vtable">
							<input name="path" type="text" class="formfld" id="path" size="60" value="<?=htmlspecialchars($pconfig['path']);?>">
							<input name="browse" type="button" class="formbtn" id="Browse" onClick='ifield = form.path; filechooser = window.open("filechooser.php?p="+escape(ifield.value)+"&sd=/mnt", "filechooser", "scrollbars=yes,toolbar=no,menubar=no,statusbar=no,width=550,height=300"); filechooser.ifield = ifield; window.ifield = ifield;' value="..." \>
							<br><?php echo sprintf(gettext("File path (e.g. /mnt/sharename/extent/%s) or device name (e.g. /dev/ad1) used as extent."), $pconfig['name']);?>
						</td>
					</tr>
					<tr>
						<td width="22%" valign="top" class="vncellreq"><?=gettext("File size");?></td>
						<td width="78%" class="vtable">
							<input name="size" type="text" class="formfld" id="size" size="10" value="<?=htmlspecialchars($pconfig['size']);?>"><br>
							<?=gettext("Size in MB.");?>
						</td>
					</tr>
					<tr>
						<td width="22%" valign="top">&nbsp;</td>
						<td width="78%"><input name="Submit" type="submit" class="formbtn" value="<?=((isset($id) && $a_iscsitarget_extent[$id]))?gettext("Save"):gettext("Add")?>">
						<?php if (isset($id) && $a_iscsitarget_extent[$id]):?>
							<input name="id" type="hidden" value="<?=$id;?>">
						<?php endif;?>
						</td>
					</tr>
				</table>
			</td>
		</tr>
	</table>
</form>
<?php include("fend.inc");?>
