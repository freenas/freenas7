#!/usr/local/bin/php
<?php
/*
	disks_mount_tools.php
	Copyright © 2006 Volker Theile (votdev@gmx.de)
  All rights reserved.

	part of FreeNAS (http://www.freenas.org)
	Copyright (C) 2005-2006 Olivier Cochard-Labbé <olivier@freenas.org>.
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

$pgtitle = array(_DISKS,_DISKSMOUNTPHP_NAME,_DISKSMOUNTPHP_TOOLS);

if (!is_array($config['mounts']['mount']))
	$config['mounts']['mount'] = array();

mount_sort();

$a_mount = &$config['mounts']['mount'];

if ($_POST) {
	unset($input_errors);
	unset($do_action);

	/* input validation */
	$reqdfields = explode(" ", "sharename action");
	$reqdfieldsn = explode(",", "Share Name,Action");
	do_input_validation($_POST, $reqdfields, $reqdfieldsn, &$input_errors);

	if(!$input_errors)
	{
		$do_action = true;
		$sharename = $_POST['sharename'];
		$action = $_POST['action'];
	}
}
if(!isset($do_action))
{
	$do_action = false;
	$sharename = '';
	$action = '';
}
if(isset($_GET['mdisk']) && isset($_GET['partition'])) {
  $mdisk = $_GET['mdisk'];
  $partition = $_GET['partition'];
  $id = array_search_ex(array($mdisk,$partition), $a_mount, array("mdisk","partition"));
  $sharename = $a_mount[$id]['sharename'];
}
if(isset($_GET['action'])) {
  $action = $_GET['action'];
}
?>
<?php include("fbegin.inc"); ?>
<table width="100%" border="0" cellpadding="0" cellspacing="0">
  <tr>
    <td class="tabnavtbl">
      <ul id="tabnav">
        <li class="tabinact"><a href="disks_mount.php"><?=_DISKSMOUNTPHP_MANAGE;?></a></li>
        <li class="tabact"><?=_DISKSMOUNTPHP_TOOLS;?></a></li>
      </ul>
    </td>
  </tr>
  <tr> 
    <td class="tabcont">
      <?php if ($input_errors) print_input_errors($input_errors); ?>
			<form action="disks_mount_tools.php" method="post" name="iform" id="iform">
			  <table width="100%" border="0" cellpadding="6" cellspacing="0">
          <tr>
            <td valign="top" class="vncellreq"><?=_DISKSMOUNTPHP_SHARENAME;?></td>
            <td class="vtable">
              <select name="sharename" class="formfld" id="sharename">
                <?php foreach ($a_mount as $mountv): ?>
                <option value="<?=$mountv['sharename'];?>"<?php if ($mountv['sharename'] == $sharename) echo "selected";?>>
                <?php echo htmlspecialchars($mountv['sharename'] . " (" . _DISK . ": " . $mountv['mdisk'] . " " . _PARTITION . ": " . $mountv['partition'] . ")");?>
                <?php endforeach; ?>
                </option>
              </select>
            </td>
      		</tr>
          <tr>
            <td valign="top" class="vncellreq"><?=_DISKSMOUNTTOOLS_COMMAND;?></td>
            <td class="vtable"> 
              <select name="action" class="formfld" id="action">
                <option value="mount" <?php if ($action == "mount") echo "selected"; ?>>mount</option>
                <option value="umount" <?php if ($action == "umount") echo "selected"; ?>>umount</option>
               </select>
            </td>
          </tr>
  				<tr>
  				  <td width="22%" valign="top">&nbsp;</td>
  				  <td width="78%">
              <input name="Submit" type="submit" class="formbtn" value="<?=_DISKSMOUNTTOOLS_SENDCMD;?>">
  				  </td>
  				</tr>
  				<tr>
    				<td valign="top" colspan="2">
    				<?php if($do_action)
    				{
    				  echo("<strong>" . _DISKSMOUNTTOOLS_CMDINFO . "</strong><br>");
    					echo('<pre>');
    					ob_end_flush();

    					/* Get the id of the mount array entry. */
		          $id = array_search_ex($sharename, $a_mount, "sharename");
		          /* Get the mount data. */
              $mount = $a_mount[$id];

              switch($action)
              {
                case "mount":
                  echo(_DISKSMOUNTTOOLS_MOUNTTEXT . "<br>");
                  echo("/dev/" . $mount['mdisk'] . $mount['partition'] .": mount to /mnt/" . $mount['sharename'] . "<br>");
                  disks_mount_ex($mount['mdisk'],$mount['partition']);
                  break;
                case "umount":
                  echo(_DISKSMOUNTTOOLS_UMOUNTTEXT . "<br>");
                  echo("/dev/" . $mount['mdisk'] . $mount['partition'] .": unmount from /mnt/" . $mount['sharename'] . "<br>");
                  disks_umount_ex($mount['mdisk'],$mount['partition']);
                  break;
              }
    					echo('</pre>');
    				}
    				?>
    				</td>
  				</tr>
			 </table>
    </form>
  </td></tr>
</table>
<?php include("fend.inc"); ?>
