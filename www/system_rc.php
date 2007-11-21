#!/usr/local/bin/php
<?php
/*
	system_rc.php
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

$pgtitle = array(gettext("System"),gettext("Advanced"),gettext("Command scripts"));

if (!is_array($config['rc']['preinit']['cmd']))
	$config['rc']['preinit']['cmd'] = array();

if (!is_array($config['rc']['postinit']['cmd']))
	$config['rc']['postinit']['cmd'] = array();

if (!is_array($config['rc']['shutdown']['cmd']))
	$config['rc']['shutdown']['cmd'] = array();

if ($_GET['act'] == "del")
{
	switch($_GET['type']) {
		case "PREINIT":
			$a_cmd = &$config['rc']['preinit']['cmd'];
			break;
		case "POSTINIT":
			$a_cmd = &$config['rc']['postinit']['cmd'];
			break;
		case "SHUTDOWN":
			$a_cmd = &$config['rc']['shutdown']['cmd'];
			break;
	}

	if ($a_cmd[$_GET['id']]) {
		unset($a_cmd[$_GET['id']]);
		write_config();
		header("Location: system_rc.php");
		exit;
	}
}
?>
<?php include("fbegin.inc");?>
<table width="100%" border="0" cellpadding="0" cellspacing="0">
	<tr>
    <td class="tabnavtbl">
      <ul id="tabnav">
      	<li class="tabinact"><a href="system_advanced.php"><?=gettext("Advanced Setup");?></a></li>
      	<li class="tabinact"><a href="system_proxy.php"><?=gettext("Proxy");?></a></li>
      	<li class="tabinact"><a href="system_swap.php"><?=gettext("Swap");?></a></li>
        <li class="tabact"><a href="system_rc.php" style="color:black" title="<?=gettext("Reload page");?>"><?=gettext("Command scripts");?></a></li>
        <li class="tabinact"><a href="system_cron.php"><?=gettext("Cron");?></a></li>
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
			  <?php $i = 0; foreach($config['rc']['preinit']['cmd'] as $cmd): ?>
        <tr>
          <td class="listlr"><?=htmlspecialchars($cmd);?>&nbsp;</td>
          <td class="listbg"><?php echo(gettext("PreInit"));?>&nbsp;</td>
          <td valign="middle" nowrap class="list">
            <a href="system_rc_edit.php?id=<?=$i;?>&type=PREINIT"><img src="e.gif" title="Edit command" width="17" height="17" border="0"></a>&nbsp;
            <a href="system_rc.php?act=del&id=<?=$i;?>&type=PREINIT" onclick="return confirm('<?=gettext("Do you really want to delete this command?");?>')"><img src="x.gif" title="<?=gettext("Delete command"); ?>" width="17" height="17" border="0"></a>
          </td>
        </tr>
        <?php $i++; endforeach;?>
        <?php $i = 0; foreach($config['rc']['postinit']['cmd'] as $cmd): ?>
        <tr>
          <td class="listlr"><?=htmlspecialchars($cmd);?>&nbsp;</td>
          <td class="listbg"><?php echo(gettext("PostInit"));?>&nbsp;</td>
          <td valign="middle" nowrap class="list">
            <a href="system_rc_edit.php?id=<?=$i;?>&type=POSTINIT"><img src="e.gif" title="Edit command" width="17" height="17" border="0"></a>&nbsp;
            <a href="system_rc.php?act=del&id=<?=$i;?>&type=POSTINIT" onclick="return confirm('<?=gettext("Do you really want to delete this command?");?>')"><img src="x.gif" title="<?=gettext("Delete command"); ?>" width="17" height="17" border="0"></a>
          </td>
        </tr>
        <?php $i++; endforeach;?>
        <?php $i = 0; foreach($config['rc']['shutdown']['cmd'] as $cmd): ?>
        <tr>
          <td class="listlr"><?=htmlspecialchars($cmd);?>&nbsp;</td>
          <td class="listbg"><?php echo(gettext("Shutdown"));?>&nbsp;</td>
          <td valign="middle" nowrap class="list">
            <a href="system_rc_edit.php?id=<?=$i;?>&type=SHUTDOWN"><img src="e.gif" title="Edit command" width="17" height="17" border="0"></a>&nbsp;
            <a href="system_rc.php?act=del&id=<?=$i;?>&type=SHUTDOWN" onclick="return confirm('<?=gettext("Do you really want to delete this command?");?>')"><img src="x.gif" title="<?=gettext("Delete command"); ?>" width="17" height="17" border="0"></a>
          </td>
        </tr>
        <?php $i++; endforeach;?>
        <tr>
          <td class="list" colspan="2"></td>
          <td class="list"><a href="system_rc_edit.php"><img src="plus.gif" title="<?=gettext("Add command");?>" width="17" height="17" border="0"></a></td>
        </tr>
      </table>
      <p>
				<span class="vexpl">
					<span class="red"><strong><?=gettext("Note");?>:</strong></span><br/>
					<?php echo gettext("These commands will be executed pre or post system initialization (booting) or before system shutdown.");?>
				</span>
			</p>
    </td>
  </tr>
</table>
<?php include("fend.inc");?>
