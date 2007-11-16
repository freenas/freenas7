#!/usr/local/bin/php
<?php
/*
	disks_crypt_tools.php
	Copyright � 2006-2007 Volker Theile (votdev@gmx.de)
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

$pgtitle = array(gettext("Disks"),gettext("Encryption"),gettext("Tools"));

if (!is_array($config['geli']['vdisk']))
	$config['geli']['vdisk'] = array();

array_sort_key($config['geli']['vdisk'], "devicespecialfile");

$a_geli = &$config['geli']['vdisk'];

if (!is_array($config['mounts']['mount']))
	$config['mounts']['mount'] = array();

array_sort_key($config['mounts']['mount'], "devicespecialfile");

$a_mount = &$config['mounts']['mount'];

if ($config['system']['webgui']['protocol'] === "http") {
	$nohttps_error = gettext("You should use HTTPS as WebGUI protocol for sending passphrase.");
}

if ($_POST) {
	unset($input_errors);
	unset($pconfig['do_action']);

	/* input validation */
	$reqdfields = explode(" ", "disk action");
	$reqdfieldsn = array(gettext("Disk Name"),gettext("Command"));
	do_input_validation($_POST, $reqdfields, $reqdfieldsn, &$input_errors);

	$devicespecialfile = $_POST['disk']."s1";
	if (disks_ismounted_ex($devicespecialfile ,"devicespecialfile") && ($_POST['action'] === "detach")) {
		$input_errors[] = gettext("This encrypted disk is mounted, umount it before trying to detach it.");
	}

	/* Check for a passphrase if 'attach' mode */
	if (empty($_POST['passphrase']) && $_POST['action'] === "attach") {
		$input_errors[] = gettext("You must use a passphrase to attach an encrypted disk.");
	}

	if(!$input_errors) {
		$pconfig['do_action'] = true;
		$pconfig['action'] = $_POST['action'];
		$pconfig['disk'] = $_POST['disk'];
		$pconfig['oldpassphrase'] = $_POST['oldpassphrase'];
		$pconfig['passphrase'] = $_POST['passphrase'];
	}
}

if(!isset($pconfig['action'])) {
	$pconfig['do_action'] = false;
	$pconfig['action'] = "";
	$pconfig['disk'] = "";
	$pconfig['oldpassphrase'] = "";
	$pconfig['passphrase'] = "";
}

if(isset($_GET['disk'])) {
  $pconfig['disk'] = $_GET['disk'];
}

if(isset($_GET['action'])) {
  $pconfig['action'] = $_GET['action'];
}
?>
<?php include("fbegin.inc");?>
<script language="JavaScript">
<!--
function action_change() {
	switch(document.iform.action.value) {
		case "attach":
			showElementById('passphrase_tr','show');
			showElementById('oldpassphrase_tr','hide');
			break;
		case "setkey":
			showElementById('passphrase_tr','show');
			showElementById('oldpassphrase_tr','show');
			break;
		default:
			showElementById('passphrase_tr','hide');
			showElementById('oldpassphrase_tr','hide');
			break;
	}
}
//-->
</script>
<table width="100%" border="0" cellpadding="0" cellspacing="0">
  <tr>
    <td class="tabnavtbl">
      <ul id="tabnav">
        <li class="tabinact"><a href="disks_crypt.php"><?=gettext("Management");?></a></li>
        <li class="tabact"><a href="disks_crypt_tools.php" title="<?=gettext("Reload page");?>" style="color:black"><?=gettext("Tools");?></a></li>
      </ul>
    </td>
  </tr>
  <tr>
    <td class="tabcont">
    	<?php if ($nohttps_error) print_error_box($nohttps_error); ?>
      <?php if ($input_errors) print_input_errors($input_errors); ?>
			<form action="disks_crypt_tools.php" method="post" name="iform" id="iform">
			  <table width="100%" border="0" cellpadding="6" cellspacing="0">
          <tr>
            <td valign="top" class="vncellreq"><?=gettext("Encrypted disk name");?></td>
            <td class="vtable">
              <select name="disk" class="formfld" id="disk">
              	<option value=""><?=gettext("Must choose one");?></option>
                <?php foreach ($a_geli as $geliv):?>
								<option value="<?=$geliv['devicespecialfile'];?>" <?php if ($geliv['devicespecialfile'] === $pconfig['disk']) echo "selected";?>>
								<?php echo htmlspecialchars("{$geliv['name']}: {$geliv['size']} ({$geliv['desc']})");?>
                </option>
                <?php endforeach;?>
              </select>
            </td>
      		</tr>
          <tr>
            <td valign="top" class="vncellreq"><?=gettext("Command");?></td>
            <td class="vtable">
							<select name="action" class="formfld" id="action" onchange="action_change()">
                <option value="attach" <?php if ($pconfig['action'] == "attach") echo "selected"; ?>>attach</option>
                <option value="detach" <?php if ($pconfig['action'] == "detach") echo "selected"; ?>>detach</option>
								<option value="setkey" <?php if ($pconfig['action'] == "setkey") echo "selected"; ?>>setkey</option>
                <option value="list" <?php if ($pconfig['action'] == "list") echo "selected"; ?>>list</option>
                <option value="status" <?php if ($pconfig['action'] == "status") echo "selected"; ?>>status</option>
							</select>
            </td>
          </tr>
          <tr id="oldpassphrase_tr" style="display: none">
						<td width="22%" valign="top" class="vncellreq"><?=htmlspecialchars(gettext("Old passphrase"));?></td>
						<td width="78%" class="vtable">
							<input name="oldpassphrase" type="password" class="formfld" id="oldpassphrase" size="20">
						</td>
					</tr>
          <tr id="passphrase_tr" style="display: none">
						<td width="22%" valign="top" class="vncellreq"><?=htmlspecialchars(gettext("Passphrase"));?></td>
						<td width="78%" class="vtable">
							<input name="passphrase" type="password" class="formfld" id="passphrase" size="20">
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
    				<?php if ($pconfig['do_action']) {
    				  echo("<strong>" . gettext("Command output:") . "</strong><br>");
    					echo('<pre>');
    					ob_end_flush();

              switch($pconfig['action']) {
                case "attach":
                case "detach":
                	// Get GEOM Eli configuration.
									$id = array_search_ex($pconfig['disk'], $a_geli, "devicespecialfile");
									$geli = $a_geli[$id];

									// Search if a mount point use this GEOM Eli disk.
									$id = array_search_ex($geli['devicespecialfile'], $a_mount, "mdisk");

									// If found, get the mount point configuration.
									if ($id !== false) $mount = $a_mount[$id];

									switch($pconfig['action']) {
                		case "attach":
 		                  $result = disks_geli_attach($geli['device'][0], $pconfig['passphrase'], true);
		                  // When attaching the disk, then also mount it.
											if ((0 == $result) && is_array($mount)) {
												echo("<br>" . gettext("Mounting device.") . "<br>");
												$result = disks_mount($mount);
												echo((0 == $result) ? gettext("Successful.") : gettext("Failed."));
											}
		                  break;

		                case "detach":
		                	// Check if disk is mounted.
		                	if (disks_ismounted($mount)) {
		                		echo gettext("Device is mounted, umount it first before detaching.") ."<br>";
											} else {
												$result = disks_geli_detach($geli['devicespecialfile'], true);
												echo((0 == $result) ? gettext("Done.") : gettext("Failed."));
											}
		                  break;
									}
                  break;

								case "setkey":
									// Get GEOM Eli configuration.
									$id = array_search_ex($pconfig['disk'], $a_geli, "devicespecialfile");
									$geli = $a_geli[$id];
									disks_geli_setkey($geli['name'], $pconfig['oldpassphrase'], $pconfig['passphrase'], true);
                	break;

                case "list":
                	system("/sbin/geli list");
                	break;

                case "status":
                	system("/sbin/geli status");
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
<script language="JavaScript">
<!--
action_change();
//-->
</script>
<?php include("fend.inc");?>
