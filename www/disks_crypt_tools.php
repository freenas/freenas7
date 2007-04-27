#!/usr/local/bin/php
<?php
/*
	disks_crypt_tools.php
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

$pgtitle = array(gettext("Disks"),gettext("Encryption"),gettext("Tools"));

if (!is_array($config['geli']['vdisk']))
	$config['geli']['vdisk'] = array();

geli_sort();

$a_geli = &$config['geli']['vdisk'];

if (!is_array($config['mounts']['mount']))
	$config['mounts']['mount'] = array();

mount_sort();

$a_mount = &$config['mounts']['mount'];

if ($config['system']['webgui']['protocol'] == "http") {
	$nohttps_errors = gettext("You should use HTTPS as WebGUI protocol for sending passphrase.");
}

if ($_POST) {
	unset($input_errors);
	unset($do_action);

	/* input validation */
	$reqdfields = explode(" ", "fullname action");
	$reqdfieldsn = array(gettext("Disk Name"),gettext("Command"));
	do_input_validation($_POST, $reqdfields, $reqdfieldsn, &$input_errors);
	
	$mount_fullname=$_POST['fullname']."s1";
	if (disks_ismounted_fullname($mount_fullname) && ($_POST['action']== "detach")) {
		$input_errors[] = gettext("This encrypted disk is mounted, umount it before trying to detach it.");
	}

	/* Check for a password if 'attach' mode */
	if ($_POST['password']=="" && $_POST['action']== "attach") 	{
			$input_errors[] = gettext("You must use a passphrase to attach an encrypted disk.");
	}

	if(!$input_errors)
	{
		$do_action = true;
		$gelifullname = $_POST['fullname'];
		$passphrase = $_POST['password'];
		$action = $_POST['action'];
	}
}
if(!isset($do_action))
{
	$do_action = false;
	$gelifullname = '';
	$passphrase = '';
	$action = '';
}

if(isset($_GET['disk'])) {
  $disk = $_GET['disk'];
  $id = array_search_ex($disk, $a_geli, "name");
  $gelifullname = $a_geli[$id]['fullname'];
}
if(isset($_GET['action'])) {
  $action = $_GET['action'];
}
?>
<?php include("fbegin.inc"); ?>
<?php if ($nohttps_errors) print_error_box($nohttps_errors); ?>
<table width="100%" border="0" cellpadding="0" cellspacing="0">
  <tr>
    <td class="tabnavtbl">
      <ul id="tabnav">
        <li class="tabinact"><a href="disks_crypt.php"><?=gettext("Manage");?></a></li>
        <li class="tabact"><a href="disks_crypt_tools.php" title="<?=gettext("Reload page");?>" style="color:black"><?=gettext("Tools");?></a></li>
      </ul>
    </td>
  </tr>
  <tr> 
    <td class="tabcont">
      <?php if ($input_errors) print_input_errors($input_errors); ?>
			<form action="disks_crypt_tools.php" method="post" name="iform" id="iform">
			  <table width="100%" border="0" cellpadding="6" cellspacing="0">
          <tr>
            <td valign="top" class="vncellreq"><?=gettext("Encrypted disk name");?></td>
            <td class="vtable">
              <select name="fullname" class="formfld" id="fullname">
                <?php foreach ($a_geli as $geliv): ?>
									<option value="<?=$geliv['fullname'];?>" <?php if ($geliv['name'] == $geliname) echo "selected";?>><?php echo htmlspecialchars($geliv['name'] . ": " .$geliv['size'] . " (" . $geliv['desc'] . ")");?>
                <?php endforeach; ?>
                </option>
              </select>
            </td>
      		</tr>
					<tr> 
						<td width="22%" valign="top" class="vncellreq"><?=gettext("Passphrase") ;?></td>
						<td width="78%" class="vtable">
							<input name="password" type="password" class="formfld" id="password" size="20">
						</td>
					</tr>
          <tr>
            <td valign="top" class="vncellreq"><?=gettext("Command");?></td>
            <td class="vtable"> 
							<select name="action" class="formfld" id="action">
                <option value="attach" <?php if ($action == "attach") echo "selected"; ?>>attach</option>
                <option value="detach" <?php if ($action == "detach") echo "selected"; ?>>detach</option>
                <option value="list" <?php if ($action == "list") echo "selected"; ?>>list</option>
                <option value="status" <?php if ($action == "status") echo "selected"; ?>>status</option>
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
    				<?php if($do_action)
    				{
    				  echo("<strong>" . gettext("Command output:") . "</strong><br>");
    					echo('<pre>');
    					ob_end_flush();

							/* Search if a mount point use this geli disk. */
							$id = false ;
		          $id = array_search_ex($gelifullname, $a_mount, "mdisk");
		          /* if found, get the mount data */
							if ($id !== false) {
								$mount = $a_mount[$id];
							}

              switch($action)
              {
                case "attach":
                  echo(gettext("Attaching...") . "<br>");
                  $result = disks_geli_attach($gelifullname,$passphrase);
                  break;
                case "detach":
                  echo(gettext("Detaching...") . "<br>");
                  $result = disks_geli_detach($gelifullname);
                  break;
                case "list":
                	echo("<br>");
                	system("/sbin/geli list");
                	break;
                case "status":
                	echo("<br>");
                	system("/sbin/geli status");
                	break;
              }

              /* Display result */
              echo((0 == $result) ? gettext("Successful") : gettext("Failed"));

							/* When attaching the disk, then also mount it. */
							if (("attach" == $action) && $mount) {
								echo(gettext("\nMounting this disk...") . "<br>");
								$result = disks_mount($mount);
								/* Display result */
								echo((0 == $result) ? gettext("Successful") : gettext("Failed"));
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
