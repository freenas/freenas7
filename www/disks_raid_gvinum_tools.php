#!/usr/local/bin/php
<?php
/*
	disks_raid_gvinum_tools.php
	
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

$pgtitle = array(gettext("Disks"), gettext("Geom Vinum"), gettext("Edit"));

if ($_POST) {
	unset($input_errors);
	unset($do_action);

	/* input validation */
	$reqdfields = explode(" ", "action object");
	$reqdfieldsn = explode(",", "Action,Object");
	do_input_validation($_POST, $reqdfields, $reqdfieldsn, &$input_errors);

	
	if (!$input_errors)
	{
		$do_action = true;
		$action = $_POST['action'];
		$object = $_POST['object'];
	}
}
if (!isset($do_action))
{
	$do_action = false;
	$action = '';
	$object = '';
}

?>
<?php include("fbegin.inc"); ?>
<table width="100%" border="0" cellpadding="0" cellspacing="0">
<tr><td class="tabnavtbl">
  <ul id="tabnav">
	<li class="tabinact"><a href="disks_raid_gmirror.php"><?=gettext("Geom Mirror"); ?></a></li>
	<li class="tabinact"><a href="disks_raid_gconcat.php"><?=gettext("Geom Concat"); ?> </a></li>
	<li class="tabinact"><a href="disks_raid_gstripe.php"><?=gettext("Geom Stripe"); ?></a></li>
	<li class="tabinact"><a href="disks_raid_graid5.php"><?=gettext("Geom Raid5"); ?></a></li> 
	<li class="tabact"><?=gettext("Geom Vinum"); ?> <?=gettext("(unstable)") ;?></li>
  </ul>
  </td></tr>
  <tr><td class="tabnavtbl">
  <ul id="tabnav">
	<li class="tabinact"><a href="disks_raid_gvinum.php"><?=gettext("Manage RAID"); ?></a></li>
	<li class="tabact"><?=gettext("Tools"); ?></li>
	<li class="tabinact"><a href="disks_raid_gvinum_info.php"><?=gettext("Information"); ?></a></li>
  </ul>
  </td></tr>
  <tr> 
    <td class="tabcont">
<?php if ($input_errors) print_input_errors($input_errors); ?>
			<form action="disks_raid_gvinum_tools.php" method="post" name="iform" id="iform">
			  <table width="100%" border="0" cellpadding="6" cellspacing="0">
                <tr>
				  <td width="22%" valign="top" class="vncellreq"><?=gettext("Object name");?></td>
				  <td width="78%" class="vtable"> 
                    <?=$mandfldhtml;?><input name="object" type="text" class="formfld" id="object" size="20" value="<?=htmlspecialchars($disk);?>"></td>
				</tr>
				<tr> 
                  <td valign="top" class="vncellreq"><?=gettext("Command");?></td>
                  <td class="vtable"> 
                    <select name="action" class="formfld" id="action">
                      <option value="start" <?php if ($action == "start") echo "selected"; ?>>start</option>
                      <option value="rebuild" <?php if ($action == "rebuild") echo "selected"; ?>>rebuild parity</option>
                      <option value="list" <?php if ($action == "list") echo "selected"; ?>>list</option>
                      <option value="remove" <?php if ($action == "remove") echo "selected"; ?>>remove</option>
                      <option value="forceup" <?php if ($action == "forceup") echo "selected"; ?>>Force State to UP</option>
                      <option value="saveconfig" <?php if ($action == "saveconfig") echo "selected"; ?>>saveconfig</option>
                     </select>
                  </td>
                </tr>
				<tr>
				  <td width="22%" valign="top">&nbsp;</td>
				  <td width="78%"> 
                    <input name="Submit" type="submit" class="formbtn" value="<?=gettext("Send Command!");?>">
				</td>
				</tr>
				<tr>
				<td valign="top" colspan="2">
				<? if ($do_action)
				{
					echo("<strong>" . gettext("Command output:") . "</strong><br>");
					echo('<pre>');
					ob_end_flush();
					
					switch ($action)
					{
					case "remove":					
						/* Remove recursivly object */
						system("/sbin/gvinum rm -r " . escapeshellarg($object));
						break;
					case "start":
						/* Start object */
						system("/sbin/gvinum start " . escapeshellarg($object));
						break;
					case "rebuild":
						/* Rebuild RAID 5 parity */
						system("/sbin/gvinum rebuildparity " . escapeshellarg($object));
						break;
					case "list":
						/* Disaply a detailed list of object */
						system("/sbin/gvinum list " . escapeshellarg($object));
						break;
					case "forceup":					
						/* Force object state up */
						system("/sbin/gvinum setstate -f up " . escapeshellarg($object));
						break;
					case "saveconfig":					
						/* Save config */
						system("/sbin/gvinum saveconfig");
						break;
					}
					
					echo('</pre>');
				}
				?>
				</td>
				</tr>
			</table>
</form>
<p><span class="vexpl"><span class="red"><strong><?=gettext("Warning");?>:</strong></span><br><?=gettext("1. Use these specials actions for debugging only!<br>2. There is no need of using this menu for start a RAID volume (start automaticaly).");?></span></p>
</td></tr></table>
<?php include("fend.inc"); ?>
