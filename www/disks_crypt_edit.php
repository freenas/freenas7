#!/usr/local/bin/php
<?php 
/*
	disks_crypt_edit.php
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

$pgtitle = array(gettext("Disks"),gettext("Encryption"),gettext("Create"));

if (!is_array($config['geli']['vdisk']))
	$config['geli']['vdisk'] = array();

geli_sort();

$a_geli = &$config['geli']['vdisk'];

if (!is_array($config['disks']['disk']))
	$config['disks']['disk'] = array();

disks_sort();

if (!is_array($config['gconcat']['vdisk']))
	$config['gconcat']['vdisk'] = array();

gconcat_sort();

if (!is_array($config['gmirror']['vdisk']))
	$config['gmirror']['vdisk'] = array();

gmirror_sort();

if (!is_array($config['graid5']['vdisk']))
	$config['graid5']['vdisk'] = array();

graid5_sort();

if (!is_array($config['gstripe']['vdisk']))
	$config['gstripe']['vdisk'] = array();

gstripe_sort();

if (!is_array($config['gvinum']['vdisk']))
	$config['gvinum']['vdisk'] = array();

gvinum_sort();

/* Get disk configurations. */
$a_disk = &$config['disks']['disk'];
$a_gconcat = &$config['gconcat']['vdisk'];
$a_gmirror = &$config['gmirror']['vdisk'];
$a_gstripe = &$config['gstripe']['vdisk'];
$a_graid5 = &$config['graid5']['vdisk'];
$a_gvinum = &$config['gvinum']['vdisk'];
$a_alldisk = array_merge($a_disk,$a_gconcat,$a_gmirror,$a_gstripe,$a_graid5,$a_gvinum);

if (!sizeof($a_disk)) {
	$nodisk_errors[] = gettext("You must add disks first.");
}

if ($config['system']['webgui']['protocol'] == "http") {
	$nohttps_errors = gettext("You should use HTTPS as WebGUI protocol for sending passphrase.");
}

if ($_POST) {
	unset($input_errors);
	unset($errormsg);
	unset($do_crypt);

	$pconfig = $_POST;

	/* Check for duplicate disks */
	foreach ($a_geli as $gelival) {
		if ($gelival['fullname'] == $_POST['disk'].".eli") {
			$input_errors[] = gettext("This disk already exists in the disk list.");
			break;
		}
	}

	/* input validation */
  $reqdfields = explode(" ", "disk ealgo password passwordconf");
  $reqdfieldsn = array(gettext("Disk"),gettext("Encryption algorithm"),gettext("Passphrase"),gettext("Passphrase"));
  do_input_validation($_POST, $reqdfields, $reqdfieldsn, &$input_errors);

  /* Check for a password mismatch */
	if ($_POST['password']!=$_POST['passwordconf']) 	{
			$input_errors[] = gettext("Passphrase don't match.");
	}

	if (!$input_errors) {
		$do_crypt = true;
		$geli = array();
		$disk = $_POST['disk'];
		//Remove aalgo value: doesn't work
		// $aalgo = $geli['aalgo'] = $_POST['aalgo'];
		$aalgo = $geli['aalgo'] = "none";
		$ealgo = $geli['ealgo'] = $_POST['ealgo'];
		$geli['fullname'] = "$disk" . ".eli";
		$geli['desc'] = "Encrypted disk";
		$passphrase = $_POST['password'];
		$type = "geli";	
		
		/* Check if disk is mounted. */ 
		if(disks_check_mount_fullname($disk)) {
			$errormsg = sprintf( gettext("The disk is currently mounted! <a href=%s>Unmount</a> this disk first before proceeding."), "disks_mount_tools.php?disk={$disk}&action=umount");
			$do_crypt = false;
		}
		
		if ($do_crypt) {
			/* Get the id of the disk array entry. */
			$NotFound = 1;
			$id = array_search_ex($disk, $a_disk, "fullname");

			/* disk */
			if ($id !== false) {
				/* Set new filesystem type. */
 				$a_disk[$id]['fstype'] = $type;
				$geli['name'] = $a_disk[$id]['name'];
				$geli['size'] = $a_disk[$id]['size'];
				$NotFound = 0;
			} else {
				$id = array_search_ex($disk, $a_gmirror, "fullname");
			}

			/* gmirror */
			if (($id !== false) && $NotFound) {
				/* Set new filesystem type. */
 				$a_gmirror[$id]['fstype'] = $type;
				$geli['name'] = $a_gmirror[$id]['name'];
				$geli['size'] = $a_gmirror[$id]['size'];
				$NotFound = 0;
			} else {
				$id = array_search_ex($disk, $a_gstripe, "fullname");
			}

			/* gstripe */
			if (($id !== false) && $NotFound) {
				/* Set new filesystem type. */
 				$a_gstripe[$id]['fstype'] = $type;
				$geli['name'] = $a_gstripe[$id]['name'];
				$geli['size'] = $a_gstripe[$id]['size'];
				$NotFound = 0;
			} else {
				$id = array_search_ex($disk, $a_gconcat, "fullname");
			}

			/* gconcat */
			if (($id !== false) && $NotFound) {
				/* Set new filesystem type. */
 				$a_gconcat[$id]['fstype'] = $type;
				$geli['name'] = $a_gconcat[$id]['name'];
				$geli['size'] = $a_gconcat[$id]['size'];
				$NotFound = 0;
			} else {
				$id = array_search_ex($disk, $a_graid5, "fullname");
			}

			/* graid5 */
			if (($id !== false) && $NotFound) {
				/* Set new filesystem type. */
 				$a_graid5[$id]['fstype'] = $type;
				$geli['name'] = $a_graid5[$id]['name'];
				$geli['size'] = $a_graid5[$id]['size'];
				$NotFound = 0;
			} else {
				$id = array_search_ex($disk, $a_gvinum, "fullname");
			}

			/* gvinum */
			if (($id !== false) && $NotFound) {
				/* Set new filesystem type. */
 				$a_gvinum[$id]['fstype'] = $type;
				$NotFound = 0;
			}
			
			$a_geli[] = $geli;
			touch($d_gelidirty_path);
			write_config();
		}
	}
}
if (!isset($do_crypt)) {
	$do_crypt = false;
	$disk = '';
	$type = '';
	$aalgo = '';
	$ealgo = '';
	$passphrase = '';
}
?>
<?php include("fbegin.inc"); ?>
<?php if ($nodisk_errors) print_input_errors($nodisk_errors); ?>
<?php if ($nohttps_errors) print_error_box($nohttps_errors); ?>
<?php if ($input_errors) print_input_errors($input_errors); ?>
<form action="disks_crypt_edit.php" method="post" name="iform" id="iform">
  <table width="100%" border="0" cellpadding="6" cellspacing="0">
    <tr> 
      <td valign="top" class="vncellreq"><?=gettext("Disk"); ?></td>
      <td class="vtable">            
				<select name="disk" class="formfld" id="disk">
				<?php foreach ($a_alldisk as $diskval): ?>
				<?php if (strcmp($diskval['size'],"NA") == 0) continue; ?>
    			<?php if ((strcmp($diskval['fstype'],"softraid")==0)) continue;?> 	  
				<?php if ((strcmp($diskval['fstype'],"geli")==0)) continue;?>
   				<option value="<?=$diskval['fullname'];?>" <?php if ($pconfig['disk'] == $diskval['fullname']) echo "selected";?>> 
   				<?php echo htmlspecialchars($diskval['name'] . ": " .$diskval['size'] . " (" . $diskval['desc'] . ")");	?>
   				</option>
    		<?php endforeach; ?>
    		</select>
      </td>
    </tr>  
<?php 
/* Remove Data Intergrity Algorithhm : there is a bug when enabled 	
     <tr> 
      <td valign="top" class="vncellreq"><?=gettext("Data integrity algorithm") ; ?></td>
      <td class="vtable"> 
        <select name="aalgo" class="formfld" id="aalgo">
					<option value="none" <?php if ($pconfig['aalgo'] == "none") echo "selected"; ?>>none</option>
          <option value="HMAC/MD5" <?php if ($pconfig['aalgo'] == "HMAC/MD5") echo "selected"; ?>>HMAC/MD5</option>
          <option value="HMAC/SHA1" <?php if ($pconfig['aalgo'] == "HMAC/SHA1") echo "selected"; ?>>HMAC/SHA1</option>
          <option value="HMAC/RIPEMD160" <?php if ($pconfig['aalgo'] == "HMAC/RIPEMD160") echo "selected"; ?>>HMAC/RIPEMD160</option>
          <option value="HMAC/SHA256" <?php if ($pconfig['aalgo'] == "HMAC/SHA256") echo "selected"; ?>>HMAC/SHA256</option>
          <option value="HMAC/SHA384" <?php if ($pconfig['aalgo'] == "HMAC/SHA384") echo "selected"; ?>>HMAC/SHA384</option>
          <option value="HMAC/SHA512" <?php if ($pconfig['aalgo'] == "HMAC/SHA512") echo "selected"; ?>>HMAC/SHA512</option>
        </select>
      </td>
    </tr>
*/
?>
    <tr> 
      <td valign="top" class="vncellreq"><?=gettext("Encryption algorithm") ;?></td>
      <td class="vtable"> 
        <select name="ealgo" class="formfld" id="ealgo">
          <option value="AES" <?php if ($pconfig['ealgo'] == "AES") echo "selected"; ?>>AES</option>
          <option value="Blowfish" <?php if ($pconfig['ealgo'] == "Blowfish") echo "selected"; ?>>Blowfish</option>
          <option value="3DES" <?php if ($pconfig['ealgo'] == "3DES") echo "selected"; ?>>3DES</option> 
        </select>
      </td>
    </tr>
		<tr> 
	    <td width="22%" valign="top" class="vncellreq"><?=gettext("Passphrase") ;?></td>
	    <td width="78%" class="vtable">
	      <input name="password" type="password" class="formfld" id="password" size="20" value="<?=htmlspecialchars($pconfig['password']);?>"><br>
	      <input name="passwordconf" type="password" class="formfld" id="passwordconf" size="20" value="<?=htmlspecialchars($pconfig['passwordconf']);?>">&nbsp;(<?=gettext("Confirmation");?>)
	    </td>
		</tr>
    <tr> 
      <td width="22%" valign="top">&nbsp;</td>
      <td width="78%">
				<input name="Submit" type="submit" class="formbtn" value="<?=gettext("Init and encrypt disk");?>" onclick="return confirm('<?=gettext("Do you really want to initialize and encrypt this disk? All data will be lost!");?>')">
      </td>
    </tr>
		<tr>
			<td valign="top" colspan="2">
			<? if ($do_crypt)
			{
				echo("<strong>".gettext("Disk initialization and encryption").":</strong>");
				echo('<pre>');
				ob_end_flush();

				// Initialize and encrypt the disk.
				echo gettext("Encrypting... Please wait")."!\n";
				if( 0 == strcmp($aalgo,"none")) {
					system("/sbin/geli init -v -e $ealgo -X " . escapeshellarg($passphrase) . " " . $disk);
				}
				else {
					system("/sbin/geli init -v -a $aalgo -e $ealgo -X " . escapeshellarg($passphrase) . " " . $disk);
				}

				// Attach the disk.
				echo(gettext("Attaching...")."\n");
				$result = disks_geli_attach($disk,$passphrase,true);
				echo((0 == $result) ? gettext("Successful") : gettext("Failed"));

				echo('</pre>');
			}
			?>
			</td>
		</tr>
    <tr> 
      <td width="22%" valign="top">&nbsp;</td>
      <td width="78%"><span class="vexpl"><span class="red"><strong><?=gettext("Warning"); ?>:<br>
        </strong></span><?=gettext("This will erase ALL data on your disk!<br>Using Data integrity will reduce size of available storage and also reduce speed.");?></span>
      </td>
    </tr>
  </table>
</form>
<?php include("fend.inc"); ?>
