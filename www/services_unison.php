#!/usr/local/bin/php
<?php
/*
	services_unison.php
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

/*      *************************

	Unison Installation Notes

	To work, unison requires an environment variable UNISON to point at
	a writable directory. Unison keeps information there between syncs to
	speed up the process.

	When a user runs the unison client, it will try to invoke ssh to
	connect to the this server. Giving the local ssh a UNISON environment
	variable without compromising ssh turned out to be non-trivial.
	The solution is to modify the default path found in /etc/login.conf.
	The path is seeded with "UNISON=/mnt" and this updated by the
	/etc/rc.d/unison file.

	Todo:
	* 	Arguably, a full client install could be done too to
	allow FreeNAS to FreeNAS syncing.
*/
require("guiconfig.inc");

$pgtitle = array(gettext("Services"),gettext("Unison"));

if (!is_array($config['unison'])) {
	$config['unison'] = array();
}

$pconfig['enable'] = isset($config['unison']['enable']);
$pconfig['share'] = $config['unison']['share'];
$pconfig['workdir'] = $config['unison']['workdir'];
$pconfig['makedir'] = isset($config['unison']['makedir']);

if ($_POST) {
	unset($input_errors);
	$pconfig = $_POST;

	/* input validation */
	$reqdfields = array();
	$reqdfieldsn = array();

	if ($_POST['enable']) {
		$reqdfields = array_merge($reqdfields, explode(" ", "share workdir"));
		$reqdfieldsn = array_merge($reqdfieldsn, array(gettext("Share"),gettext("Working Directory")));
	}

	do_input_validation($_POST, $reqdfields, $reqdfieldsn, &$input_errors);

	$fullpath = "/mnt/{$_POST['share']}/{$_POST['workdir']}";

	if (!$_POST['makedir'] && ($fullpath) && (!file_exists($fullpath))) {
		$input_errors[] = gettext("The combination of share and working directory does not exist.");
	}

	if (!$input_errors) {
		$config['unison']['share'] = $_POST['share'];
		$config['unison']['workdir'] = $_POST['workdir'];
		$config['unison']['enable'] = $_POST['enable'] ? true : false;
		$config['unison']['makedir'] = $_POST['makedir'] ? true : false;

		write_config();

		$retval = 0;
		if (!file_exists($d_sysrebootreqd_path)) {
			config_lock();
			$retval |= rc_update_service("unison");
			config_unlock();
		}
		$savemsg = get_std_save_message($retval);
	}
}

/* retrieve mounts to build list of share names */
if (!is_array($config['mounts']['mount']))
	$config['mounts']['mount'] = array();

array_sort_key($config['mounts']['mount'], "mdisk");

$a_mount = &$config['mounts']['mount'];
?>
<?php include("fbegin.inc"); ?>
<script language="JavaScript">
<!--
function enable_change(enable_change) {
	var endis;

	endis = !(document.iform.enable.checked || enable_change);
	document.iform.share.disabled = endis;
	document.iform.workdir.disabled = endis;
	document.iform.makedir.disabled = endis;
}
//-->
</script>
<?php if ($input_errors) print_input_errors($input_errors); ?>
<?php if ($savemsg) print_info_box($savemsg); ?>
<form action="services_unison.php" method="post" name="iform" id="iform">
<table width="100%" border="0" cellpadding="6" cellspacing="0">
  <tr>
    <td colspan="2" valign="top" class="optsect_t">
		  <table border="0" cellspacing="0" cellpadding="0" width="100%">
		  <tr>
        <td class="optsect_s"><strong><?=gettext("Unison File Synchronisation");?></strong></td>
			  <td align="right" class="optsect_s"><input name="enable" type="checkbox" value="yes" <?php if ($pconfig['enable']) echo "checked"; ?> onClick="enable_change(false)"> <strong><?=gettext("Enable");?></strong></td>
      </tr>
		  </table>
    </td>
  </tr>
  <tr>
    <td width="22%" valign="top" class="vncellreq"><?=gettext("Share");?></td>
    <td width="78%" class="vtable">
      <select name="share" class="formfld" id="share">
        <?php foreach ($a_mount as $mount): $tmp=$mount['sharename']; ?>
        <option value="<?=$tmp;?>"
        <?php if ($tmp == $pconfig['share']) echo "selected";?>><?=$tmp?></option>
        <?php endforeach; ?>
      </select>
      <br><?=gettext("You may need enough space to duplicate all files being synced");?>.</td>
    </td>
  </tr>
  <tr>
    <td width="22%" valign="top" class="vncellreq"><?=gettext("Working Directory");?></td>
    <td width="78%" class="vtable">
			<input name="workdir" type="text" class="formfld" id="workdir" size="20" value="<?=htmlspecialchars($pconfig['workdir']);?>">
			<br><?=gettext("Where the working files will be stored");?>.</td>
    </td>
  </tr>
  <tr>
    <td width="22%" valign="top" class="vncellreq"><?=gettext("Create");?></td>
    <td width="78%" class="vtable">
      <input name="makedir" type="checkbox" id="makedir" value="yes" <?php if ($pconfig['makedir']) echo "checked"; ?>>
      <?=gettext("Create work directory if it doesn't exist");?><span class="vexpl"><br>
    </td>
  </tr>
  <tr>
    <td width="22%" valign="top">&nbsp;</td>
    <td width="78%">
      <input name="Submit" type="submit" class="formbtn" value="<?=gettext("Save and Restart");?>" onClick="enable_change(true)">
    </td>
  </tr>
  <tr>
    <td width="22%" valign="top">&nbsp;</td>
    <td width="78%"><span class="red"><strong><?=gettext("Note");?>:</strong></span><br><?php echo sprintf( gettext("<a href=%s>SSHD</a> must be enabled for Unison to work, and the <a href=%s>user</a> must have full Shell enabled."), "services_sshd.php", "access_users.php");?></td>
  </tr>
  </table>
</form>
<script language="JavaScript">
<!--
enable_change(false);
//-->
</script>
<?php include("fend.inc"); ?>
