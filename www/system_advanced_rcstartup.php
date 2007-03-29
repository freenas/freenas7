#!/usr/local/bin/php
<?php
/*
	system_advanced_rcstartup.php
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

$pgtitle = array(gettext("System"),gettext("Advanced"),gettext("Startup"));

if (!is_array($config['system']['earlyshellcmd']))
	$config['system']['earlyshellcmd'] = array();

if (!is_array($config['system']['shellcmd']))
	$config['system']['shellcmd'] = array();

$a_shellcmd = &$config['system']['shellcmd'];
$a_earlyshellcmd = &$config['system']['earlyshellcmd'];

if ($_GET['act'] == "del")
{
	switch($_GET['type']) {
		case "PRE":
			$a_cmd = &$config['system']['earlyshellcmd'];
			break;
		case "POST":
			$a_cmd = &$config['system']['shellcmd'];
			break;
	}

	if ($a_cmd[$_GET['id']]) {
		unset($a_cmd[$_GET['id']]);
		write_config();
		header("Location: system_advanced_rcstartup.php");
		exit;
	}
}
?>
<?php include("fbegin.inc"); ?>
<p><span class="vexpl"><span class="red"><strong><?=gettext("Note");?>:</strong></span><br><?=gettext("The options on this page are intended for use by advanced users only, and there's <strong>NO</strong> support for them.");?></p>
<table width="100%" border="0" cellpadding="0" cellspacing="0">
	<tr>
    <td class="tabnavtbl">
      <ul id="tabnav">
      	<li class="tabinact"><a href="system_advanced.php"><?=gettext("Advanced");?></a></li>
      	<li class="tabinact"><a href="system_advanced_swap.php"><?=gettext("Swap");?></a></li>
        <li class="tabact"><a href="system_advanced_rcstartup.php" style="color:black" title="<?=gettext("Reload page");?>"><?=gettext("Startup");?></a></li>
      </ul>
    </td>
  </tr>
  <tr> 
    <td class="tabcont">
      <table width="100%" border="0" cellpadding="0" cellspacing="0">
        <tr>
          <td width="80%" class="listhdrr"><?=gettext("Command");?></td>
          <td width="10%" class="listhdrr"><?=gettext("Type");?></td>
          <td width="10%" class="list"></td>
        </tr>
			  <?php $i = 0; foreach($a_earlyshellcmd as $cmd): ?>
        <tr>
          <td class="listlr"><?=htmlspecialchars($cmd);?>&nbsp;</td>
          <td class="listbg"><?php echo(gettext("Pre"));?>&nbsp;</td>
          <td valign="middle" nowrap class="list">
            <a href="system_advanced_rcstartup_edit.php?id=<?=$i;?>&type=PRE"><img src="e.gif" title="Edit command" width="17" height="17" border="0"></a>&nbsp;
            <a href="system_advanced_rcstartup.php?act=del&id=<?=$i;?>&type=PRE" onclick="return confirm('<?=gettext("Do you really want to delete this command?");?>')"><img src="x.gif" title="<?=gettext("Delete command"); ?>" width="17" height="17" border="0"></a>
          </td>
        </tr>
        <?php $i++; endforeach; ?>
        <?php $i = 0; foreach($a_shellcmd as $cmd): ?>
        <tr>
          <td class="listlr"><?=htmlspecialchars($cmd);?>&nbsp;</td>
          <td class="listbg"><?php echo(gettext("Post"));?>&nbsp;</td>
          <td valign="middle" nowrap class="list">
            <a href="system_advanced_rcstartup_edit.php?id=<?=$i;?>&type=POST"><img src="e.gif" title="Edit command" width="17" height="17" border="0"></a>&nbsp;
            <a href="system_advanced_rcstartup.php?act=del&id=<?=$i;?>&type=POST" onclick="return confirm('<?=gettext("Do you really want to delete this command?");?>')"><img src="x.gif" title="<?=gettext("Delete command"); ?>" width="17" height="17" border="0"></a>
          </td>
        </tr>
        <?php $i++; endforeach; ?>
        <tr> 
          <td class="list" colspan="2"></td>
          <td class="list"><a href="system_advanced_rcstartup_edit.php"><img src="plus.gif" title="<?=gettext("Add command");?>" width="17" height="17" border="0"></a></td>
        </tr>
      </table>
      <p><span class="vexpl"><span class="red"><strong><?=gettext("Note");?>:</strong></span><br><?php echo gettext("These commands will be executed pre or post system initialization.");?></p>
    </td>
  </tr>
</table>
<?php include("fend.inc"); ?>
