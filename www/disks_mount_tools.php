#!/usr/local/bin/php
<?php
/*
	disks_mount_tools.php
	Copyright © 2006-2009 Volker Theile (votdev@gmx.de)
  All rights reserved.

	part of FreeNAS (http://www.freenas.org)
	Copyright (C) 2005-2009 Olivier Cochard-Labbe <olivier@freenas.org>.
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

$pgtitle = array(gettext("Disks"), gettext("Mount Point"), gettext("Tools"));

if (isset($_GET['disk'])) {
	$index = array_search_ex($_GET['disk'], $config['mounts']['mount'], "mdisk");
	if (false !== $index) {
		$uuid = $config['mounts']['mount'][$index]['uuid'];
	}
}

if (isset($_GET['action'])) {
	$action = $_GET['action'];
}

if (!is_array($config['mounts']['mount']))
	$config['mounts']['mount'] = array();

if ($_POST) {
	unset($input_errors);
	unset($errormsg);
	unset($do_action);

	// Input validation.
	$reqdfields = explode(" ", "mountpoint action");
	$reqdfieldsn = array(gettext("Mount point"), gettext("Command"));
	do_input_validation($_POST, $reqdfields, $reqdfieldsn, &$input_errors);

	// Check if mount point is used to store a swap file.
	if (("umount" === $_POST['action']) &&
			(isset($config['system']['swap']['enable'])) &&
			($config['system']['swap']['type'] === "file") &&
			($config['system']['swap']['mountpoint'] === $_POST['mountpoint'])) {
		$index = array_search_ex($_POST['mountpoint'], $config['mounts']['mount'], "uuid");
		$errormsg[] = gettext(sprintf("A swap file is located on the mounted device %s.",
			$config['mounts']['mount'][$index]['devicespecialfile']));
  }

	if ((!$input_errors) || (!$errormsg)) {
		$do_action = true;
		$uuid = $_POST['mountpoint'];
		$action = $_POST['action'];
	}
}

if (!isset($do_action)) {
	$do_action = false;
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
					<?php html_mountcombobox("mountpoint", gettext("Mount point"), $uuid, "", true);?>
					<?php html_combobox("action", gettext("Command"), $action, array("mount" => gettext("mount"), "umount" => gettext("umount")), "", true);?>
				</table>
				<div id="submit">
					<input name="Submit" type="submit" class="formbtn" value="<?=gettext("Execute");?>">
				</div>
				<?php if(($do_action) && (!$errormsg)) {
					echo('<pre>');
					echo(sprintf("<div id='cmdoutput'>%s</div>", gettext("Command output:")));
					ob_end_flush();

					$index = array_search_ex($uuid, $config['mounts']['mount'], "uuid");
					if (false !== $index) {
						$mount = $config['mounts']['mount'][$index];

						switch ($action) {
						  case "mount":
						    echo(gettext("Mounting...") . "<br>");
								$result = disks_mount($mount);
						    break;

						  case "umount":
						    echo(gettext("Unmounting...") . "<br>");
								$result = disks_umount($mount);
						    break;
						}

						echo (0 == $result) ? gettext("Done.") : gettext("Failed.");
					}
					echo('</pre>');
				}?>
				<div id="remarks">
					<?php html_remark("note", gettext("Note"), gettext("You can't unmount a drive which is used by swap file, a iSCSI-target file or any other running process!"));?>
				</div>
			</form>
		</td>
	</tr>
</table>
<?php include("fend.inc");?>
