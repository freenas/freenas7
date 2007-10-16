#!/usr/local/bin/php
<?php
/*
	disks_mount_fsck.php
	Copyright © 2006-2007 Volker Theile (votdev@gmx.de)
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

$pgtitle = array(gettext("Disks"),gettext("Mount Point"),gettext("Fsck"));

if (!is_array($config['mounts']['mount']))
	$config['mounts']['mount'] = array();

mount_sort();

$a_mount = &$config['mounts']['mount'];

if ($_POST) {
	unset($input_errors);
	unset($errormsg);
	unset($do_action);

	/* input validation */
	$reqdfields = explode(" ", "disk");
	$reqdfieldsn = array(gettext("Disk"));
	do_input_validation($_POST, $reqdfields, $reqdfieldsn, &$input_errors);

	if(!$input_errors) {
		$do_action = true;
		$disk = $_POST['disk'];
		$umount = $_POST['umount'];
	}
}

if (!isset($do_action)) {
	$do_action = false;
	$disk = '';
	$umount = false;
}
?>
<?php include("fbegin.inc"); ?>
<table width="100%" border="0" cellpadding="0" cellspacing="0">
  <tr>
    <td class="tabnavtbl">
      <ul id="tabnav">
				<li class="tabinact"><a href="disks_mount.php"><?=gettext("Management");?></a></li>
        <li class="tabinact"><a href="disks_mount_tools.php"><?=gettext("Tools");?></a></li>
				<li class="tabact"><a href="disks_mount_fsck.php" style="color:black" title="<?=gettext("Reload page");?>"><?=gettext("Fsck");?></a></li>
      </ul>
    </td>
  </tr>
  <tr>
    <td class="tabcont">
      <?php if ($input_errors) print_input_errors($input_errors); ?>
			<form action="disks_mount_fsck.php" method="post" name="iform" id="iform">
			  <table width="100%" border="0" cellpadding="6" cellspacing="0">
          <tr>
            <td valign="top" class="vncellreq"><?=gettext("Disk");?></td>
            <td class="vtable">
              <select name="disk" class="formfld" id="disk">
                <?php foreach ($a_mount as $mount): ?>
								<?php if (strcmp($mount['fstype'],"cd9660") == 0) continue; ?>
                <option value="<?=$mount['fullname'];?>"<?php if ($mount['fullname'] == $disk) echo "selected";?>>
                <?php echo htmlspecialchars($mount['sharename'] . ": " .$mount['fullname']);?>
                <?php endforeach; ?>
                </option>
              </select>
            </td>
      		</tr>
          <tr> 
            <td width="22%" valign="top" class="vncell"></td>
            <td width="78%" class="vtable"> 
              <input name="umount" type="checkbox" id="umount" value="yes" <?php if ($umount) echo "checked"; ?>>
              <strong><?=gettext("Unmount disk/partition");?></strong><span class="vexpl"><br>
              <?=gettext("If the selected disk/partition is mounted it will be unmounted temporarily to perform selected command, otherwise the commands work in read-only mode.<br>Service disruption to users accessing this mount will occur during this process.");?></span>
            </td>
          </tr>
  				<tr>
  				  <td width="22%" valign="top">&nbsp;</td>
  				  <td width="78%">
             <input name="Submit" type="submit" class="formbtn" value="<?=gettext("Fsck");?>">
  				  </td>
  				</tr>
  				<tr>
    				<td valign="top" colspan="2">
						<?php if($do_action) {
							echo("<strong>" . gettext("Command output:") . "</strong>");
							echo('<pre>');
							ob_end_flush();
							/* Check filesystem */
							$result = disks_fsck($disk,$umount);
							/* Display result */
              echo((0 == $result) ? gettext("Successful") : gettext("Failed"));
							echo('</pre>');
						}
						?>
    				</td>
  				</tr>
			 </table>
    	</form>
		<p><span class="vexpl"><span class="red"><strong><?=gettext("Warning");?>:</strong></span><br><?php echo sprintf(gettext("You can't unmount a drive used by swap file or iSCSI-target file!"), get_product_name());?></p>
  	</td>
	</tr>
</table>
<?php include("fend.inc"); ?>
