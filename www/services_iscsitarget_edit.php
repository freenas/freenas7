#!/usr/local/bin/php
<?php 
/*
	services_iscsitarget_edit.php
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

$id = $_GET['id'];
if (isset($_POST['id']))
	$id = $_POST['id'];

$pgtitle = array(gettext("Services"),gettext("iSCSI Target"),gettext("Add"));

if (!is_array($config['iscsitarget']['vdisk']))
	$config['iscsitarget']['vdisk'] = array();

//iscsiinit_sort();

if (!is_array($config['mounts']['mount']))
	$config['mounts']['mount'] = array();

mount_sort();

$a_mount = &$config['mounts']['mount'];

$a_iscsitarget = &$config['iscsitarget']['vdisk'];

if (isset($id) && $a_iscsitarget[$id]) {
	$pconfig['sharename'] = $a_iscsitarget[$id]['sharename'];
	$pconfig['size'] = $a_iscsitarget[$id]['size'];
}

if ($_POST) {
	unset($input_errors);
	unset($errormsg);

	$pconfig = $_POST;

	/* input validation */
  $reqdfields = explode(" ", "sharename size network network_subnet");
  $reqdfieldsn = array(gettext("Mount Point"),gettext("Size"),gettext("Authorised network"),gettext("Subnet bit count"));
  do_input_validation($_POST, $reqdfields, $reqdfieldsn, &$input_errors);
  
  if (($_POST['network'] && !is_ipaddr($_POST['network']))) {
		$input_errors[] = gettext("A valid network must be specified.");
	}
	if (($_POST['network_subnet'] && !is_numeric($_POST['network_subnet']))) {
		$input_errors[] = gettext("A valid network bit count must be specified.");
	}
	
	if (!is_numeric($_POST['size'])) {
		$input_errors[] = gettext("A valid size target value must be specified.");
	}
	$osn = gen_subnet($_POST['network'], $_POST['network_subnet']) . "/" . $_POST['network_subnet'];
	
	if (!$input_errors) {
		$iscsitarget = array();
		$iscsitarget['sharename'] = $_POST['sharename'];
		$iscsitarget['size'] = $_POST['size'];
		$iscsitarget['network'] = $osn;
	
		if (isset($id) && $a_iscsiinit[$id])
			$a_iscsitarget[$id] = $iscsitarget;
		else
			$a_iscsitarget[] = $iscsitarget;
		touch($d_iscsitargetdirty_path);
		
		write_config();
		
		header("Location: services_iscsitarget.php");
		exit;

	}
}

?>
<?php include("fbegin.inc"); ?>
<?php if ($input_errors) print_input_errors($input_errors); ?>
<form action="services_iscsitarget_edit.php" method="post" name="iform" id="iform">
  <table width="100%" border="0" cellpadding="6" cellspacing="0">
     <tr>
			<td width="22%" valign="top" class="vncellreq"><?=gettext("Mount to use"); ?></td>
			<td width="78%" class="vtable">
				<select name="sharename" class="formfld" id="sharename">
				  <?php foreach ($a_mount as $mount): ?>
				  <option value="<?=$mount['sharename'];?>" <?php if ($mount['sharename'] == $pconfig['mount']) echo "selected";?>><?php echo htmlspecialchars($mount['sharename']);?></option>
		  		<?php endforeach; ?>
		  	</select>
		  </td>
		</tr>
		  <tr>
          <td width="22%" valign="top" class="vncellreq"><?=gettext("File size") ;?></td>
          <td width="78%" class="vtable">
              <?=$mandfldhtml;?><input name="size" type="text" class="formfld" id="size" size="30" value="<?=htmlspecialchars($pconfig['size']);?>">
			   <br><?=gettext("Size in MB.") ;?>
            </td>
          </tr>
		  <tr>
      <td width="22%" valign="top" class="vncellreq"><?=gettext("Authorised network") ; ?></td>
      <td width="78%" class="vtable">
        <?=$mandfldhtml;?><input name="network" type="text" class="formfld" id="network" size="20" value="<?=htmlspecialchars($pconfig['network']);?>"> / 
        <select name="network_subnet" class="formfld" id="network_subnet">
          <?php for ($i = 32; $i >= 1; $i--): ?>
          <option value="<?=$i;?>" <?php if ($i == $pconfig['network_subnet']) echo "selected"; ?>>
          <?=$i;?>
          </option>
          <?php endfor; ?>
        </select><br>
        <span class="vexpl"><?=gettext("Network that is authorised to access to this iSCSI target") ;?></span>
      </td>
	
    <tr> 
      <td width="22%" valign="top">&nbsp;</td>
      <td width="78%">
				<input name="Submit" type="submit" class="formbtn" value="<?=gettext("Add");?>">
      </td>
    </tr>
  </table>
</form>
<?php include("fend.inc"); ?>
