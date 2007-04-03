#!/usr/local/bin/php
<?php
/*
	services_nfs.php
	
	part of FreeNAS (http://www.freenas.org)
	Copyright (C) 2005-2007 Olivier Cochard <olivier@freenas.org>.
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

$pgtitle = array(gettext("Services"),gettext("NFS"));

if (!is_array($config['nfs'])) {
	$config['nfs'] = array();
}

if(!is_array($config['nfs']['nfsnetworks']))
	$config['nfs']['nfsnetworks'] = array();

sort($config['nfs']['nfsnetworks']);

$pconfig['enable'] = isset($config['nfs']['enable']);
$pconfig['mapall'] = $config['nfs']['mapall'];
$pconfig['nfsnetworks'] = $config['nfs']['nfsnetworks'];

if ($_POST) {
	unset($input_errors);

	$pconfig = $_POST;
	$pconfig['nfsnetworks'] = $config['nfs']['nfsnetworks'];

	/* input validation */
	if($_POST['enable']) {
		if(0 == count($pconfig['nfsnetworks']))
			$input_errors[] = gettext("No networks declared.");
	}

	if (!$input_errors) {
		$config['nfs']['enable'] = $_POST['enable'] ? true : false;
		$config['nfs']['mapall'] = $_POST['mapall'];

		write_config();
		
		$retval = 0;
		if(!file_exists($d_sysrebootreqd_path)) {
			config_lock();
			$retval = services_nfs_configure();
			config_unlock();
		}

		$savemsg = get_std_save_message($retval);

		if($retval == 0) {
			if(file_exists($d_nfsconfdirty_path))
				unlink($d_nfsconfdirty_path);
		}
	}
}

if($_GET['act'] == "del") {
	/* Remove network entry from list */
	$config['nfs']['nfsnetworks'] = array_diff($config['nfs']['nfsnetworks'],array($config['nfs']['nfsnetworks'][$_GET['id']]));
	write_config();
	touch($d_nfsconfdirty_path);
	header("Location: services_nfs.php");
	exit;
}
?>
<?php include("fbegin.inc"); ?>
<script language="JavaScript">
<!--
function enable_change(enable_change) {
	var endis = !(document.iform.enable.checked || enable_change);
  document.iform.mapall.disabled = endis;
  document.iform.nfsnetworks.disabled = endis;
}
//-->
</script>
<?php if ($input_errors) print_input_errors($input_errors); ?>
<?php if ($savemsg) print_info_box($savemsg); ?>
<form action="services_nfs.php" method="post" name="iform" id="iform">
  <table width="100%" border="0" cellpadding="6" cellspacing="0">
    <tr>
      <td colspan="2" valign="top" class="optsect_t">
        <table border="0" cellspacing="0" cellpadding="0" width="100%">
          <tr>
            <td class="optsect_s"><strong><?=gettext("NFS Server"); ?></strong></td>
            <td align="right" class="optsect_s">
              <input name="enable" type="checkbox" value="yes" <?php if ($pconfig['enable']) echo "checked"; ?> onClick="enable_change(false)"> <strong><?=gettext("Enable") ;?></strong>
            </td>
          </tr>
        </table>
      </td>
    </tr>
    <tr>
      <td width="22%" valign="top" class="vncellreq"><?=gettext("Map all users to root"); ?></td>
      <td width="78%" class="vtable">
        <select name="mapall" class="formfld" id="mapall">
        <?php $types = array(gettext("Yes"),gettext("No"));?>
        <?php $vals = explode(" ", "yes no");?>
        <?php $j = 0; for ($j = 0; $j < count($vals); $j++): ?>
          <option value="<?=$vals[$j];?>" <?php if ($vals[$j] == $pconfig['mapall']) echo "selected";?>>
          <?=htmlspecialchars($types[$j]);?>
          </option>
        <?php endfor; ?>
        </select><br>
        <?=gettext("All users will have the root privilege.") ;?>
      </td>
    </tr>
	<tr>
      <td width="22%" valign="top" class="vncellreq"><?=gettext("Authorized");?></td>
      <td width="78%" class="vtable">
        <?=$mandfldhtml;?>
        <table width="100%" border="0" cellpadding="0" cellspacing="0">
          <tr>
            <td width="90%" class="listhdrr"><?=gettext("Networks");?></td>
            <td width="10%" class="list"></td>
          </tr>
					<?php $i = 0; foreach($pconfig['nfsnetworks'] as $nfsnetworksv): ?>
					<tr>
						<td class="listlr"><?=htmlspecialchars($nfsnetworksv);?> &nbsp;</td>
						<td valign="middle" nowrap class="list">
							<?php if(isset($config['nfs']['enable'])): ?>
							<a href="services_nfs_edit.php?id=<?=$i;?>"><img src="e.gif" title="<?=gettext("Edit network");?>" width="17" height="17" border="0"></a>&nbsp;
							<a href="services_nfs.php?act=del&id=<?=$i;?>" onclick="return confirm('<?=gettext("Do you really want to delete this network entry?");?>')"><img src="x.gif" title="<?=gettext("Delete network"); ?>" width="17" height="17" border="0"></a>
							<?php endif; ?>
						</td>
					</tr>
					<?php $i++; endforeach; ?>
					<tr>
						<td class="list" colspan="1"></td>
						<td class="list">
							<a href="services_nfs_edit.php"><img src="plus.gif" title="<?=gettext("Add network");?>" width="17" height="17" border="0"></a>
						</td>
					</tr>
        </table>
        <?=gettext("Networks authorized.");?>
      </td>
    </tr>
    <tr>
      <td width="22%" valign="top">&nbsp;</td>
      <td width="78%">
        <input name="Submit" type="submit" class="formbtn" value="<?=gettext("Save and Restart NFS");?>" onClick="enable_change(true)">
      </td>
    </tr>
    <tr>
      <td width="22%" valign="top">&nbsp;</td>
      <td width="78%"><span class="vexpl"><span class="red"><strong><?=gettext("Warning");?>:</strong></span><br><?=gettext("The name of the exported directories are : /mnt/sharename");?></span></td>
    </tr>
  </table>
</form>
<script language="JavaScript">
<!--
enable_change(false);
//-->
</script>
<?php include("fend.inc"); ?>
