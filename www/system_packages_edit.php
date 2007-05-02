#!/usr/local/bin/php
<?php 
/*
	system_packages_edit.php
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
require("packages.inc");

$id = $_GET['id'];

if (isset($_POST['id']))
	$id = $_POST['id'];

$pgtitle = array(gettext("System"),gettext("Packages"),isset($id)?gettext("Edit"):gettext("Install"));

if (!is_array($config['packages']['package']))
	$config['packages']['package'] = array();

$a_package = &$config['packages']['package'];

if ($_POST) {
	unset($input_errors);

	if (is_uploaded_file($_FILES['ulfile']['tmp_name'])) {
		if (!file_exists($_FILES['ulfile']['tmp_name'])) {
			// Probably out of memory for the MFS.
			$input_errors[] = gettext("Package upload failed (out of memory?)");
		} else if (!file_exists("{$config['packages']['path']}/packages")) {
			// Packages directory does not exists.
			$input_errors[] = gettext("Package path does not exist.");
		} else {
			// Check whether package is already configured/installed.
			if (0 == packages_is_configured($_FILES['ulfile']['name'])) {
				$input_errors[] = gettext("Package is already installed.");
			} else if (0 == packages_is_installed($_FILES['ulfile']['name'])) {
				$input_errors[] = gettext("Package is already installed.");
			} else {
				$packagename = "{$config['packages']['path']}/packages/{$_FILES['ulfile']['name']}";

				// Move the image so PHP won't delete it.
				rename($_FILES['ulfile']['tmp_name'], $packagename);
			
				$package = array();
				$package['filename'] = $_FILES['ulfile']['name'];

				$a_package[] = $package;

				touch($d_packagesconfdirty_path);
				write_config();

				header("Location: system_packages.php");
				exit;
			}
		}
	}
}
?>
<?php include("fbegin.inc"); ?>
<?php if ($input_errors) print_input_errors($input_errors); ?>
<?php if ($savemsg) print_info_box($savemsg); ?>
<form action="system_packages_edit.php" method="post" enctype="multipart/form-data">
  <table width="100%" border="0" cellpadding="6" cellspacing="0">
		<tr>
			<td width="22%" valign="top" class="vncellreq"><?=gettext("Package file");?></td>
			<td width="78%" class="vtable">
        <?=$mandfldhtml;?>
				<input name="ulfile" type="file" class="formfld"> 
			</td>
		</tr>
    <tr> 
      <td width="22%" valign="top">&nbsp;</td>
      <td width="78%"> <input name="Submit" type="submit" class="formbtn" value="<?=(isset($id))?gettext("Save"):gettext("Install")?>"> 
        <?php if (isset($id)): ?>
        <input name="id" type="hidden" value="<?=$id;?>">
        <?php endif; ?>
      </td>
    </tr>
  </table>
</form>
<?php include("fend.inc"); ?>
