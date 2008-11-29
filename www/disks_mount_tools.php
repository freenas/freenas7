#!/usr/local/bin/php
<?php
/*
	disks_mount_tools.php
	Copyright © 2006-2008 Volker Theile (votdev@gmx.de)
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

$pgtitle = array(gettext("Disks"),gettext("Mount Point"),gettext("Tools"));

if (!is_array($config['mounts']['mount']))
	$config['mounts']['mount'] = array();

array_sort_key($config['mounts']['mount'], "devicespecialfile");

$a_mount = $config['mounts']['mount'];

if ($_POST) {
	unset($input_errors);
	unset($errormsg);
	unset($do_action);

	/* input validation */
	$reqdfields = explode(" ", "sharename action");
	$reqdfieldsn = array(gettext("Share Name"),gettext("Command"));
	do_input_validation($_POST, $reqdfields, $reqdfieldsn, &$input_errors);

	if (isset($config['system']['swap_enable']) && ($config['system']['swap_mountname'] == $_POST['sharename'])) {
		$errormsg[] = gettext("The swap file is using this mount point.");
  }

	if((!$input_errors) || (!$errormsg)) 	{
		$do_action = true;
		$sharename = $_POST['sharename'];
		$action = $_POST['action'];
	}
}
if(!isset($do_action))
{
	$do_action = false;
	$action = '';
}

if(isset($_GET['disk'])) {
	$disk = $_GET['disk'];
	$id = array_search_ex($disk, $a_mount, "mdisk");
	if (false !== $id) {
		$sharename = $a_mount[$id]['sharename'];
	}
}

if(isset($_GET['action'])) {
  $action = $_GET['action'];
}
?>
<?php include("fbegin.inc");?>
<?php if($errormsg) print_input_errors($errormsg);?>
<table width="100%" border="0" cellpadding="0" cellspacing="0">
  <tr>
    <td class="tabnavtbl">
      <ul id="tabnav">
        <li class="tabinact"><a href="disks_mount.php"><span><?=gettext("Management");?></span></a></li>
        <li class="tabact"><a href="disks_mount_tools.php" title="<?=gettext("Reload page");?>"><span><?=gettext("Tools");?></span></a></li>
        <li class="tabinact"><a href="disks_mount_fsck.php"><span><?=gettext("Fsck");?></span></a></li>
      </ul>
    </td>
  </tr>
  <tr>
    <td class="tabcont">
      <?php if ($input_errors) print_input_errors($input_errors);?>
			<form action="disks_mount_tools.php" method="post" name="iform" id="iform">
			  <table width="100%" border="0" cellpadding="6" cellspacing="0">
          <tr>
          	<td width="22%" valign="top" class="vncellreq"><?=gettext("Mount point");?></td>
          	<td width="78%" class="vtable">
              <select name="sharename" class="formfld" id="sharename">
              	<option value=""><?=gettext("Must choose one");?></option>
                <?php foreach ($a_mount as $mountv):?>
                <option value="<?=$mountv['sharename'];?>"<?php if ($mountv['sharename'] === $sharename) echo "selected";?>>
                <?php if ("disk" === $mountv['type']):?>
                <?php echo htmlspecialchars($mountv['sharename'] . " (" . gettext("Disk") . ": " . $mountv['mdisk'] . " " . gettext("Partition") . ": " . $mountv['partition'] . ")");?>
                <?php else:?>
                <?php echo htmlspecialchars($mountv['sharename'] . " (" . gettext("File") . ": " . $mountv['filename']. ")");?>
                <?php endif;?>
                </option>
                <?php endforeach;?>
              </select>
            </td>
      		</tr>
          <tr>
          	<td width="22%" valign="top" class="vncellreq"><?=gettext("Command");?></td>
          	<td width="78%" class="vtable">
              <select name="action" class="formfld" id="action">
                <option value="mount" <?php if ($action == "mount") echo "selected"; ?>>mount</option>
                <option value="umount" <?php if ($action == "umount") echo "selected"; ?>>umount</option>
               </select>
            </td>
          </tr>
				</table>
				<div id="submit">
					<input name="Submit" type="submit" class="formbtn" value="<?=gettext("Send Command!");?>">
				</div>
				<?php if(($do_action) && (!$errormsg))
				{
				echo('<pre>');
				echo(sprintf("<div id='cmdoutput'>%s</div>", gettext("Command output:")));
				ob_end_flush();

				/* Get the id of the mount array entry. */
				$id = array_search_ex($sharename, $a_mount, "sharename");
				/* Get the mount data. */
				$mount = $a_mount[$id];

				switch($action)
				{
				  case "mount":
				    echo(gettext("Mounting...") . "<br>");
						$result = disks_mount($mount);
				    break;
				  case "umount":
				    echo(gettext("Unmounting...") . "<br>");
						$result = disks_umount($mount);
				    break;
				}

				/* Display result */
				echo((0 == $result) ? gettext("Done.") : gettext("Failed."));

				echo('</pre>');
				}
				?>
				<div id="remarks">
					<?php html_remark("note", gettext("Note"), gettext("You can't unmount a drive which is used by swap file, a iSCSI-target file or any other running process!"));?>
				</div>
			</form>
		</td>
	</tr>
</table>
<?php include("fend.inc");?>
