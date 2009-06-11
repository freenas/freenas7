#!/usr/local/bin/php
<?php
/*
	system_packages_edit.php
	Copyright � 2007-2009 Volker Theile (votdev@gmx.de)
	All rights reserved.

	part of FreeNAS (http://www.freenas.org)
	Copyright (C) 2005-2009 Olivier Cochard-Labb� <olivier@freenas.org>.
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
require("auth.inc");
require("guiconfig.inc");
require("packages.inc");

$pgtitle = array(gettext("System"),gettext("Packages"),gettext("Install"));

if ($_POST) {
	unset($input_errors);

	if (is_uploaded_file($_FILES['ulfile']['tmp_name'])) {
		if (!file_exists($_FILES['ulfile']['tmp_name'])) {
			// Probably out of memory for the MFS.
			$input_errors[] = gettext("Package upload failed (out of memory?)");
		} else {
			// Check whether package is already installed.
			if (0 == packages_is_installed($_FILES['ulfile']['name'])) {
				$input_errors[] = gettext("Package is already installed.");
			} else {
				$packagename = "{$g['tmp_path']}/{$_FILES['ulfile']['name']}";

				// Move the image so PHP won't delete it.
				@rename($_FILES['ulfile']['tmp_name'], $packagename);

				$do_action = true;
			}
		}
	}
}

if(!isset($do_action)) {
	$do_action = false;
	$packagename = "";
}
?>
<?php include("fbegin.inc");?>
<table width="100%" border="0" cellpadding="0" cellspacing="0">
  <tr>
		<td class="tabnavtbl">
  		<ul id="tabnav">
				<li class="tabact"><a href="system_packages.php" title="<?=gettext("Reload page");?>"><span><?=gettext("Packages");?></span></a></li>
  		</ul>
  	</td>
	</tr>
  <tr>
    <td class="tabcont">
			<form action="system_packages_edit.php" method="post" enctype="multipart/form-data">
				<?php if ($input_errors) print_input_errors($input_errors); ?>
				<?php if ($savemsg) print_info_box($savemsg); ?>
			  <table width="100%" border="0" cellpadding="6" cellspacing="0">
					<tr>
						<td width="22%" valign="top" class="vncellreq"><?=gettext("Package file");?></td>
						<td width="78%" class="vtable">
							<input name="ulfile" type="file" class="formfld">
							<br><?=gettext("Select the FreeBSD package to be installed.");?>
						</td>
					</tr>
			  </table>
				<div id="submit">
					<input name="Submit" type="submit" class="formbtn" value="<?=gettext("Install")?>">
				</div>
				<?php if($do_action)
				{
				echo('<pre>');
				echo(sprintf("<div id='cmdoutput'>%s</div>", gettext("Command output:")));
				ob_end_flush();
				
				// Install package.
				packages_install($packagename);
				
				// Delete file.
				@unlink($packagename);
				
				echo('</pre>');
				}
				?>
				<div id="remarks">
					<?php html_remark("note", gettext("Note"), gettext("You can also install a package via SSH or console using the the pkg_add command.<br>Example: pkg_add -r packagename"));?>
				</div>
			</form>
		</td>
	</tr>
</table>
<?php include("fend.inc");?>
