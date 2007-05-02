#!/usr/local/bin/php
<?php
/*
	system_packages.php
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

$pgtitle = array(gettext("System"), gettext("Packages"));

if(!is_array($config['packages']))
	$config['packages'] = array();

$pconfig['path'] = $config['packages']['path'];

if ($_POST) {
	unset($input_errors);

	$pconfig = $_POST;

	$reqdfields = explode(" ", "path");
	$reqdfieldsn = array(gettext("Path"));
	do_input_validation($_POST, $reqdfields, $reqdfieldsn, &$input_errors);

	if(!$input_errors) {
    $config['packages']['path'] = $_POST['path'];

		write_config();

		$retval = 0;
		if(!file_exists($d_sysrebootreqd_path)) {
			config_lock();
			$retval = packages_configure();
			config_unlock();
		}

		$savemsg = get_std_save_message($retval);
	}
}

// Get list of installed packages.
$a_packages = packages_get_installed();

if ($_GET['act'] == "del") {
	if ($a_packages[$_GET['id']]) {
		packages_uninstall($a_packages[$_GET['id']]['name']);
		unset($a_packages[$_GET['id']]);
		write_config();
		header("Location: system_packages.php");
		exit;
	}
}
?>
<?php include("fbegin.inc"); ?>
<?php if ($input_errors) print_input_errors($input_errors); ?>
<?php if ($savemsg) print_info_box($savemsg); ?>
<form action="system_packages.php" method="post" name="iform" id="iform">
	<table width="100%" border="0" cellpadding="6" cellspacing="0">
    <tr> 
      <td colspan="2" valign="top" class="listtopic"><?=gettext("General configuration"); ?></td>
    </tr>
    <tr>
      <td width="22%" valign="top" class="vncellreq"><?=gettext("Path");?></td>
      <td width="78%" class="vtable">
        <?=$mandfldhtml;?>
        <input name="path" type="text" class="formfld" id="path" size="60" value="<?=htmlentities($pconfig['path']);?>">
        <input name="browse" type="button" class="formbtn" id="Browse" onClick='ifield = form.path; filechooser = window.open("filechooser.php?p="+escape(ifield.value), "filechooser", "scrollbars=yes,toolbar=no,menubar=no,statusbar=no,width=500,height=300"); filechooser.ifield = ifield; window.ifield = ifield;' value="..." \>
				<br><?=gettext("Path where to store packages.");?>
      </td>
    </tr>
    <tr> 
      <td colspan="2" valign="top" height="16"></td>
    </tr>
    <tr> 
      <td colspan="2" valign="top" class="listtopic"><?=gettext("Installed packages"); ?></td>
    </tr>
  </table>
	<table width="100%" border="0" cellpadding="6" cellspacing="0">
    <tr>
      <td width="40%" class="listhdrr"><?=gettext("Package Name");?></td>
      <td width="50%" class="listhdrr"><?=gettext("Description");?></td>
      <td width="10%" class="list"></td>
    </tr>
	  <?php $i = 0; foreach($a_packages as $packagev): ?>
    <tr>
      <td class="listr"><?=htmlspecialchars($packagev['name']);?>&nbsp;</td>
      <td class="listbg"><?=htmlspecialchars($packagev['desc']);?>&nbsp;</td>
      <td valign="middle" nowrap class="list"> <a href="system_packages.php?act=del&id=<?=$i;?>" onclick="return confirm('<?=gettext("Do you really want to uninstall this package?"); ?>')"><img src="x.gif" title="<?=gettext("Uninstall package"); ?>" width="17" height="17" border="0"></a></td>
    </tr>
    <?php $i++; endforeach; ?>
    <tr> 
			<td class="list" colspan="2"></td>
			<td class="list"> <a href="system_packages_edit.php"><img src="plus.gif" title="<?=gettext("Install package"); ?>" width="17" height="17" border="0"></a></td>
		</tr>
  </table>
  <input name="Submit" type="submit" class="formbtn" value="<?=gettext("Save");?>"><br><br>
</form>
<?php include("fend.inc"); ?>
