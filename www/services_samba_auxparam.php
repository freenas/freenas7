#!/usr/local/bin/php
<?php
/*
	services_samba_auxparam.php
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

$pgtitle = array(gettext("Services"),gettext("CIFS/SMB"),gettext("Auxiliary parameter"),isset($id)?gettext("Edit"):gettext("Add"));

$a_auxparam = &$config['samba']['auxparam'];

if(!is_array($a_auxparam))
	$a_auxparam = array();

sort($a_auxparam);

if (isset($id) && $a_auxparam[$id]) {
	$pconfig['auxparam'] = $a_auxparam[$id];
} else {
	$pconfig['auxparam'] = "";
}

if($_POST) {
	unset($input_errors);

	$pconfig = $_POST;

	/* input validation */
	$reqdfields = explode(" ", "auxparam");
	$reqdfieldsn = array(gettext("Auxiliary parameter"));
	do_input_validation($_POST, $reqdfields, $reqdfieldsn, &$input_errors);

	if(!$input_errors) {
		if (isset($id) && $a_auxparam[$id])
			$a_auxparam[$id] = $_POST['auxparam'];
		else
			$a_auxparam[] = $_POST['auxparam'];

		touch($d_smbconfdirty_path);
		write_config();

		header("Location: services_samba.php");
		exit;
	}
}
?>
<?php include("fbegin.inc");?>
<table width="100%" border="0" cellpadding="0" cellspacing="0">
	<tr>
		<td class="tabnavtbl">
			<ul id="tabnav">
				<li class="tabact"><a href="services_samba.php" style="color:black" title="<?=gettext("Reload page");?>"><?=gettext("Settings");?></a></li>
				<li class="tabinact"><a href="services_samba_share.php"><?=gettext("Shares");?></a></li>
			</ul>
		</td>
	</tr>
	<tr>
		<td class="tabcont">
			<form action="services_samba_auxparam.php" method="post" name="iform" id="iform">
				<?php if ($input_errors) print_input_errors($input_errors); ?>
				<table width="100%" border="0" cellpadding="6" cellspacing="0">
					<tr>
						<td width="22%" valign="top" class="vncellreq"><?=gettext("Auxiliary parameter");?></td>
						<td width="78%" class="vtable">
							<?=$mandfldhtml;?>
							<input name="auxparam" type="text" class="formfld" id="auxparam" size="60" value="<?=htmlspecialchars($pconfig['auxparam']);?>">
							<br><?=gettext("Auxiliary parameter to be added.");?>
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
	</tr>
</table>
<?php include("fend.inc");?>
