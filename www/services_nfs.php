#!/usr/local/bin/php
<?php
/*
	services_nfs.php
	part of FreeNAS (http://www.freenas.org)
	Copyright (C) 2005-2008 Olivier Cochard-Labbe <olivier@freenas.org>.
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

if(!is_array($config['nfsd']['share']))
	$config['nfsd']['share'] = array();

array_sort_key($config['nfsd']['share'], "path");

$a_share = &$config['nfsd']['share'];

$pconfig['enable'] = isset($config['nfsd']['enable']);
$pconfig['numproc'] = $config['nfsd']['numproc'];

if ($_POST) {
	unset($input_errors);

	$pconfig = $_POST;

	if ($_POST['enable']) {
		$reqdfields = explode(" ", "numproc");
		$reqdfieldsn = array(gettext("Number of servers"));
		$reqdfieldst = explode(" ", "numeric");

		do_input_validation($_POST, $reqdfields, $reqdfieldsn, &$input_errors);
		do_input_validation_type($_POST, $reqdfields, $reqdfieldsn, $reqdfieldst, &$input_errors);
	}

	if(!$input_errors) {
		$config['nfsd']['enable'] = $_POST['enable'] ? true : false;
		$config['nfsd']['numproc'] = $_POST['numproc'];

		write_config();

		$retval = 0;
		if (!file_exists($d_sysrebootreqd_path)) {
			config_lock();
			$retval |= rc_update_service("rpcbind"); // !!! Do
			$retval |= rc_update_service("mountd");  // !!! not
			$retval |= rc_update_service("nfsd");    // !!! change
			$retval |= rc_update_service("statd");   // !!! this
			$retval |= rc_update_service("lockd");   // !!! order
			$retval |= rc_update_service("mdnsresponder");
			config_unlock();
		}

		$savemsg = get_std_save_message($retval);

		if (0 == $retval) {
			if (file_exists($d_nfsconfdirty_path))
				unlink($d_nfsconfdirty_path);
		}
	}
}
?>
<?php include("fbegin.inc");?>
<script language="JavaScript">
<!--
function enable_change(enable_change) {
	var endis = !(document.iform.enable.checked || enable_change);
	document.iform.numproc.disabled = endis;
}
//-->
</script>
<table width="100%" border="0" cellpadding="0" cellspacing="0">
	<tr>
		<td class="tabnavtbl">
			<ul id="tabnav">
				<li class="tabact"><a href="services_nfs.php" title="<?=gettext("Reload page");?>"><?=gettext("Settings");?></a></li>
				<li class="tabinact"><a href="services_nfs_share.php"><?=gettext("Shares");?></a></li>
			</ul>
		</td>
	</tr>
	<tr>
		<td class="tabcont">
			<form action="services_nfs.php" method="post" name="iform" id="iform">
				<?php if ($input_errors) print_input_errors($input_errors);?>
				<?php if ($savemsg) print_info_box($savemsg);?>
				<?php if (file_exists($d_nfsconfdirty_path)):?><p>
				<?php print_info_box_np(gettext("The NFS export list has been changed.<br>You must apply the changes in order for them to take effect."));?><br>
				<input name="apply" type="submit" class="formbtn" id="apply" value="<?=gettext("Apply changes");?>"></p>
				<?php endif;?>
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
						<td width="22%" valign="top" class="vncell"><?=gettext("Number of servers");?></td>
						<td width="78%" class="vtable">
							<input name="numproc" type="text" class="formfld" id="numproc" size="5" value="<?=htmlspecialchars($pconfig['numproc']);?>"></br>
							<span class="vexpl"><?=gettext("Specifies how many servers to create.");?> <?=gettext("There should be enough to handle the maximum level of concurrency from its clients, typically four to six.");?></span>
						</td>
					</tr>
					<tr>
						<td width="22%" valign="top">&nbsp;</td>
						<td width="78%">
							<input name="Submit" type="submit" class="formbtn" value="<?=gettext("Save and Restart");?>" onClick="enable_change(true)">
						</td>
					</tr>
				</table>
			</form>
		</td>
	</tr>
</table>
<script language="JavaScript">
<!--
enable_change(false);
//-->
</script>
<?php include("fend.inc");?>
