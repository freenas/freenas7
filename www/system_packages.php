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

$a_packages = packages_get_installed(); 

if ($_POST) {
	$pconfig = $_POST;

	if ($_POST['apply']) {
		$retval = 0;
	}
}

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
<form action="system_packages.php" method="post">
<?php if ($savemsg) print_info_box($savemsg); ?>
	<table width="100%" border="0" cellpadding="0" cellspacing="0">
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
</form>
<?php include("fend.inc"); ?>
