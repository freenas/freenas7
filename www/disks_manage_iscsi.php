#!/usr/local/bin/php
<?php
/*
	disks_manage_iscsi.php
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

$pgtitle = array(gettext("Disks"),gettext("Management"));

if (!is_array($config['iscsi'])) {
	$config['iscsi'] = array();
}

$pconfig['enable'] = isset($config['iscsi']['enable']);
$pconfig['targetaddress'] = $config['iscsi']['targetaddress'];
$pconfig['targetname'] = $config['iscsi']['targetname'];

if ($_POST) {
	unset($input_errors);
	$pconfig = $_POST;

	/* input validation */
	$reqdfields = array();
	$reqdfieldsn = array();

	if ($_POST['enable']) {
		$reqdfields = array_merge($reqdfields, explode(" ", "targetaddress targetname"));
		$reqdfieldsn = array_merge($reqdfieldsn, array(gettext("Target IP address"),gettext("Targetname")));
	}

	do_input_validation($_POST, $reqdfields, $reqdfieldsn, &$input_errors);

	if ($_POST['enable'] && !is_ipaddr($_POST['targetaddress'])) {
  		$input_errors[] = gettext("A valid IP address must be specified.");
  }

	if (!$input_errors) {
		$config['iscsi']['enable'] = $_POST['enable'] ? true : false;
		$config['iscsi']['targetaddress'] = $_POST['targetaddress'];
		$config['iscsi']['targetname'] = $_POST['targetname'];

		write_config();

		$retval = 0;
		if (!file_exists($d_sysrebootreqd_path)) {
			/* nuke the cache file */
			config_lock();
			services_iscsi_configure();
			config_unlock();
		}
		$savemsg = get_std_save_message($retval);
	}
}
?>
<?php include("fbegin.inc"); ?>
<table width="100%" border="0" cellpadding="0" cellspacing="0">
  <tr>
    <td class="tabnavtbl">
      <ul id="tabnav">
      	<li class="tabinact"><a href="disks_manage.php"><?=gettext("Manage"); ?></a></li>
      	<li class="tabact"><?=gettext("iSCSI initiator"); ?></li>
      </ul>
    </td>
  </tr>
  <tr>
    <td class="tabcont">
<script language="JavaScript">
<!--
function enable_change(enable_change) {
	var endis = !(document.iform.enable.checked || enable_change);
	document.iform.targetname.disabled = endis;
	document.iform.targetaddress.disabled = endis;
}
//-->
</script>
<?php if ($input_errors) print_input_errors($input_errors); ?>
<?php if ($savemsg) print_info_box($savemsg); ?>
<form action="disks_manage_iscsi.php" method="post" name="iform" id="iform">
  <table width="100%" border="0" cellpadding="6" cellspacing="0">
    <tr>
      <td colspan="2" valign="top" class="optsect_t">
        <table border="0" cellspacing="0" cellpadding="0" width="100%">
				  <tr>
            <td class="optsect_s"><strong><?=gettext("iSCSI Initiator"); ?></strong></td>
				    <td align="right" class="optsect_s">
              <input name="enable" type="checkbox" value="yes" <?php if ($pconfig['enable']) echo "checked"; ?> onClick="enable_change(false)"> <strong><?=gettext("Enable"); ?></strong>
            </td>
          </tr>
				</table>
      </td>
    </tr>
    <tr>
      <td width="22%" valign="top" class="vncellreq"><?=gettext("Target IP address"); ?></td>
      <td width="78%" class="vtable">
        <?=$mandfldhtml;?><input name="targetaddress" type="text" class="formfld" id="targetaddress" size="20" value="<?=htmlspecialchars($pconfig['targetaddress']);?>"><br>
        <?=gettext("IP address of the iSCSI target."); ?>
      </td>
    </tr>
    <tr>
      <td width="22%" valign="top" class="vncellreq"><?=gettext("Targetname"); ?></td>
      <td width="78%" class="vtable">
        <?=$mandfldhtml;?><input name="targetname" type="text" class="formfld" id="targetname" size="20" value="<?=htmlspecialchars($pconfig['targetname']);?>"><br>
        <?=gettext("Targetname."); ?>
      </td>
    </tr>
		<tr>
      <td width="22%" valign="top">&nbsp;</td>
      <td width="78%">
        <input name="Submit" type="submit" class="formbtn" value="<?=gettext("Save");?>" onClick="enable_change(true)">
      </td>
    </tr>
  </table>
</form>
<script language="JavaScript">
<!--
enable_change(false);
//-->
</script>
</td></tr></table>
<?php include("fend.inc"); ?>
