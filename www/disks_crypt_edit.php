#!/usr/local/bin/php
<?php
/*
	disks_crypt_edit.php
	part of FreeNAS (http://www.freenas.org)
	Copyright (C) 2005-2007 Olivier Cochard-Labbe <olivier@freenas.org>.
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

$pgtitle = array(gettext("Disks"),gettext("Encryption"),gettext("Add"));

if (!is_array($config['geli']['vdisk']))
	$config['geli']['vdisk'] = array();

array_sort_key($config['geli']['vdisk'], "devicespecialfile");

$a_geli = &$config['geli']['vdisk'];

// Get list of all configured disks (physical and virtual).
$a_alldisk = get_conf_all_disks_list();

// Check whether there are disks configured, othersie display a error message.
if (!count($a_alldisk)) {
	$nodisk_errors[] = gettext("You must add disks first.");
}

// Check if protocol is HTTPS, otherwise display a warning message.
if ("http" === $config['system']['webgui']['protocol']) {
	$nohttps_errors = gettext("You should use HTTPS as WebGUI protocol for sending passphrase.");
}

if ($_POST) {
	unset($input_errors);
	unset($errormsg);
	unset($do_action);

	// Input validation.
  $reqdfields = explode(" ", "disk ealgo password passwordconf");
  $reqdfieldsn = array(gettext("Disk"),gettext("Encryption algorithm"),gettext("Passphrase"),gettext("Passphrase"));
  do_input_validation($_POST, $reqdfields, $reqdfieldsn, &$input_errors);

	// Check for duplicate disks.
	if (array_search_ex("{$_POST['disk']}.eli", $a_geli, "devicespecialfile")) {
		$input_errors[] = gettext("This disk already exists in the disk list.");
	}

	// Check for a password mismatch.
	if ($_POST['password'] !== $_POST['passwordconf']) {
		$input_errors[] = gettext("Passphrase don't match.");
	}

	if (!$input_errors) {
		$do_action = true;

		$init = $_POST['init'] ? true : false;
		$disk = $_POST['disk'];
		$aalgo = "none";
		$ealgo = $_POST['ealgo'];
		$passphrase = $_POST['password'];

		// Check whether disk is mounted.
		if (disks_ismounted_ex($disk, "devicespecialfile")) {
			$errormsg = sprintf( gettext("The disk is currently mounted! <a href=%s>Unmount</a> this disk first before proceeding."), "disks_mount_tools.php?disk={$disk}&action=umount");
			$do_action = false;
		}

		if ($do_action) {
			// Get disk information.
			$diskinfo = disks_get_diskinfo($disk);

			$geli = array();
			$geli['name'] = $disk;
			$geli['device'] = "/dev/{$disk}";
			$geli['devicespecialfile'] = "{$geli['device']}.eli";
			$geli['desc'] = "Encrypted disk";
			$geli['size'] = "{$diskinfo['mediasize_mbytes']}MB";
			$geli['aalgo'] = $aalgo;
			$geli['ealgo'] = $ealgo;

			$a_geli[] = $geli;

			// Set new file system type attribute ('fstype') in configuration.
			set_conf_disk_fstype($geli['device'], "geli");

			write_config();
		}
	}
}

if (!isset($do_action)) {
	$do_action = false;
	$init = false;
	$disk = '';
	$aalgo = '';
	$ealgo = '';
	$passphrase = '';
}
?>
<?php include("fbegin.inc"); ?>
<table width="100%" border="0" cellpadding="0" cellspacing="0">
	<tr>
    <td class="tabnavtbl">
      <ul id="tabnav">
        <li class="tabact"><a href="disks_crypt.php" title="<?=gettext("Reload page");?>" style="color:black"><?=gettext("Management");?></a></li>
        <li class="tabinact"><a href="disks_crypt_tools.php"><?=gettext("Tools");?></a></li>
      </ul>
    </td>
  </tr>
  <tr>
    <td class="tabcont">
			<form action="disks_crypt_edit.php" method="post" name="iform" id="iform">
				<?php if ($nodisk_errors) print_input_errors($nodisk_errors); ?>
				<?php if ($nohttps_errors) print_error_box($nohttps_errors); ?>
				<?php if ($input_errors) print_input_errors($input_errors); ?>
			  <table width="100%" border="0" cellpadding="6" cellspacing="0">
			    <tr>
			      <td valign="top" class="vncellreq"><?=gettext("Disk"); ?></td>
			      <td class="vtable">
							<select name="disk" class="formfld" id="disk">
								<option value=""><?=gettext("Must choose one");?></option>
								<?php foreach ($a_alldisk as $diskv):?>
								<?php if (0 == strcmp($diskv['class'], "geli")) continue;?>
								<?php if (0 == strcmp($diskv['size'], "NA")) continue;?>
								<?php if (1 == disks_exists($diskv['devicespecialfile'])) continue;?>
								<option value="<?=$diskv['name'];?>" <?php if ($disk === $diskv['name']) echo "selected";?>>
								<?php echo htmlspecialchars("{$diskv['name']}: {$diskv['size']} ({$diskv['desc']})");?>
								</option>
								<?php endforeach;?>
			    		</select>
			      </td>
			    </tr>
					<?php
					/* Remove Data Intergrity Algorithhm : there is a bug when enabled
					<tr>
						<td valign="top" class="vncellreq"><?=gettext("Data integrity algorithm");?></td>
			      <td class="vtable">
			        <select name="aalgo" class="formfld" id="aalgo">
								<option value="none" <?php if ($aalgo === "none") echo "selected"; ?>>none</option>
			          <option value="HMAC/MD5" <?php if ($aalgo === "HMAC/MD5") echo "selected"; ?>>HMAC/MD5</option>
			          <option value="HMAC/SHA1" <?php if ($aalgo === "HMAC/SHA1") echo "selected"; ?>>HMAC/SHA1</option>
			          <option value="HMAC/RIPEMD160" <?php if ($aalgo === "HMAC/RIPEMD160") echo "selected"; ?>>HMAC/RIPEMD160</option>
			          <option value="HMAC/SHA256" <?php if ($aalgo === "HMAC/SHA256") echo "selected"; ?>>HMAC/SHA256</option>
			          <option value="HMAC/SHA384" <?php if ($aalgo === "HMAC/SHA384") echo "selected"; ?>>HMAC/SHA384</option>
			          <option value="HMAC/SHA512" <?php if ($aalgo === "HMAC/SHA512") echo "selected"; ?>>HMAC/SHA512</option>
			        </select>
			      </td>
			    </tr>
					*/
					?>
			    <tr>
			      <td valign="top" class="vncellreq"><?=gettext("Encryption algorithm");?></td>
			      <td class="vtable">
			        <select name="ealgo" class="formfld" id="ealgo">
			          <option value="AES" <?php if ($ealgo === "AES") echo "selected"; ?>>AES</option>
			          <option value="Blowfish" <?php if ($ealgo === "Blowfish") echo "selected"; ?>>Blowfish</option>
			          <option value="3DES" <?php if ($ealgo === "3DES") echo "selected"; ?>>3DES</option>
			        </select>
			      </td>
			    </tr>
					<tr>
				    <td width="22%" valign="top" class="vncellreq"><?=htmlspecialchars(gettext("Passphrase"));?></td>
				    <td width="78%" class="vtable">
				      <input name="password" type="password" class="formfld" id="password" size="20" value=""><br>
				      <input name="passwordconf" type="password" class="formfld" id="passwordconf" size="20" value="">&nbsp;(<?=gettext("Confirmation");?>)
				    </td>
					</tr>
					<tr>
						<td width="22%" valign="top" class="vncell"><?=gettext("Initialize");?></td>
			      <td width="78%" class="vtable">
							<input name="init" type="checkbox" id="init" value="yes" <?php if (true == $init) echo "checked"; ?>>
							<?=gettext("Initialize and encrypt disk. This will erase ALL data on your disk! Do not use this option if you want to add an existing encrypted disk.");?>
			      </td>
			    </tr>
			    <tr>
			      <td width="22%" valign="top">&nbsp;</td>
			      <td width="78%">
							<input name="Submit" type="submit" class="formbtn" value="<?=gettext("Add");?>">
			      </td>
			    </tr>
					<tr>
						<td valign="top" colspan="2">
						<? if ($do_action) {
							echo("<strong>" . gettext("Command output:") . "</strong>");
							echo('<pre>');
							ob_end_flush();

							if (true == $init) {
								// Initialize and encrypt the disk.
								echo gettext("Encrypting... Please wait") . "!<br/>";
								disks_geli_init($disk, $aalgo, $ealgo, $passphrase, true);
							}

							// Attach the disk.
							echo(sprintf(gettext("Attaching provider '%s'."), $geli['name']) . "<br/>");
							disks_geli_attach($geli['name'], $passphrase, true);

							echo('</pre>');
						}
						?>
						</td>
					</tr>
			  </table>
			</form>
		</td>
	</tr>
</table>
<?php include("fend.inc");?>
