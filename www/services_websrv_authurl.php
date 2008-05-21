#!/usr/local/bin/php
<?php
/*
	services_websrv_authurl.php
	Copyright © 2007-2008 Volker Theile (votdev@gmx.de)
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
if (isset($_POST['id']))
	$id = $_POST['id'];

$pgtitle = array(gettext("Services"),gettext("Webserver"),gettext("Authenticate URL"),isset($id)?gettext("Edit"):gettext("Add"));

if (!is_array($config['websrv']['authentication']['url']))
	$config['websrv']['authentication']['url'] = array();

array_sort_key($config['websrv']['authentication']['url'], "path");

$a_authurl = &$config['websrv']['authentication']['url'];

if (isset($id) && $a_authurl[$id]) {
	$pconfig['path'] = $a_authurl[$id]['path'];
	$pconfig['realm'] = $a_authurl[$id]['realm'];
} else {
	$pconfig['path'] = "";
	$pconfig['realm'] = "";
}

if ($_POST) {
	unset($input_errors);
	$pconfig = $_POST;

	// Input validation.
	$reqdfields = explode(" ", "path realm");
	$reqdfieldsn = array(gettext("URL"), gettext("Realm"));
	do_input_validation($_POST, $reqdfields, $reqdfieldsn, &$input_errors);

	// Check if URL is already configured.
	if (!isset($id) && FALSE !== array_search_ex($_POST['path'], $a_authurl, "path")) {
		$input_errors[] = gettext("This URL is already configured.");
	}

	if (!$input_errors) {
		$url = array();

		$url['path'] = $_POST['path'];
		$url['realm'] = $_POST['realm'];

		if (isset($id) && $a_authurl[$id])
			$a_authurl[$id] = $url;
		else
			$a_authurl[] = $url;

		touch($d_websrvconfdirty_path);
		write_config();

		header("Location: services_websrv.php");
		exit;
	}
}
?>
<?php include("fbegin.inc");?>
<table width="100%" border="0" cellpadding="0" cellspacing="0">
	<td class="tabcont">
		<form action="services_websrv_authurl.php" method="post" name="iform" id="iform">
			<?php if ($input_errors) print_input_errors($input_errors);?>
			<table width="100%" border="0" cellpadding="6" cellspacing="0">
				<tr>
					<td width="22%" valign="top" class="vncellreq"><?=gettext("Path");?></td>
					<td width="78%" class="vtable">
						<input name="path" type="text" class="formfld" id="path" size="60" value="<?=htmlspecialchars($pconfig['path']);?>"><br/>
						<span class="vexpl"><?=gettext("Path of the URL relative to document root.");?></span>
					</td>
				</tr>
				<tr>
					<td width="22%" valign="top" class="vncellreq"><?=gettext("Realm");?></td>
					<td width="78%" class="vtable">
						<input name="realm" type="text" class="formfld" id="realm" size="60" value="<?=htmlspecialchars($pconfig['realm']);?>"><br/>
						<span class="vexpl"><?=gettext("String displayed in the dialog presented to the user when accessing the URL.");?></span>
					</td>
				</tr>
				<tr>
					<td width="22%" valign="top">&nbsp;</td>
					<td width="78%"> <input name="Submit" type="submit" class="formbtn" value="<?=isset($id)?gettext("Save"):gettext("Add")?>">
						<?php if(isset($id)):?>
						<input name="id" type="hidden" value="<?=$id;?>">
						<?php endif;?>
					</td>
				</tr>
			</table>
		</form>
	</td>
</table>
<?php include("fend.inc");?>
