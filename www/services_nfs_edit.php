#!/usr/local/bin/php
<?php 
/*
	services_nfs_edit.php
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

$id = $_GET['id'];
if (isset($_POST['id']))
	$id = $_POST['id'];

$pgtitle = array(gettext("Services"),gettext("NFS"),gettext("Networks"),isset($id)?gettext("Edit"):gettext("Add"));

if(!is_array($config['nfs']['nfsnetworks']))
	$config['nfs']['nfsnetworks'] = array();

sort($config['nfs']['nfsnetworks']);

list($pconfig['network'],$pconfig['network_subnet']) =
		explode('/', $config['nfs']['nfsnetworks'][$id]);


if($_POST) {
	unset($input_errors);

	/* input validation */
	$reqdfields = explode(" ", "network network_subnet");
	$reqdfieldsn = array(gettext("Networks"),gettext("Subnet"));
	do_input_validation($_POST, $reqdfields, $reqdfieldsn, &$input_errors);
	
	if (($_POST['network'] && !is_ipaddr($_POST['network']))) {
		$input_errors[] = gettext("A valid network must be specified.");
	}
	if (($_POST['network_subnet'] && !is_numeric($_POST['network_subnet']))) {
		$input_errors[] = gettext("A valid network bit count must be specified.");
	}

	$osn = gen_subnet($_POST['network'], $_POST['network_subnet']) . "/" . $_POST['network_subnet'];

	if(!$input_errors) {
		/* Remove old entry from content list */
		$config['nfs']['nfsnetworks'] = array_diff($config['nfs']['nfsnetworks'],array($config['nfs']['nfsnetworks'][$id]));
		/* Add new entry */
		$config['nfs']['nfsnetworks'] = array_merge($config['nfs']['nfsnetworks'],array($osn));

		touch($d_nfsconfdirty_path);
		write_config();
    header("Location: services_nfs.php");
		exit;
	}
}
?>
<?php include("fbegin.inc"); ?>
<?php if ($input_errors) print_input_errors($input_errors); ?>
<form action="services_nfs_edit.php" method="post" name="iform" id="iform">
  <table width="100%" border="0" cellpadding="6" cellspacing="0">
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
        <span class="vexpl"><?=gettext("Network that is authorised to access to NFS share") ;?></span>
      </td>
    </tr>
    <tr> 
      <td width="22%" valign="top">&nbsp;</td>
      <td width="78%"> <input name="Submit" type="submit" class="formbtn" value="<?=gettext("Save");?>"> 
        <?php if(isset($id)): ?>
        <input name="id" type="hidden" value="<?=$id;?>"> 
        <?php endif; ?>
      </td>
    </tr>
  </table>
</form>
<?php include("fend.inc"); ?>
