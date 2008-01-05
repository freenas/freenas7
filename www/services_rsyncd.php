#!/usr/local/bin/php
<?php 
/*
	services_rsyncd.php
	part of FreeNAS (http://www.freenas.org)
	Copyright (C) 2005-2008 Olivier Cochard-Labbé <olivier@freenas.org>.
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

$pgtitle = array(gettext("Services"),gettext("RSYNCD"),gettext("Server"));

if (!is_array($config['access']['user']))
	$config['access']['user'] = array();

array_sort_key($config['access']['user'], "login");

$a_user = &$config['access']['user'];

if (!is_array($config['rsync'])) {
	$config['rsync'] = array();
}

$pconfig['port'] = $config['rsyncd']['port'];
$pconfig['motd'] = $config['rsyncd']['motd'];
$pconfig['rsyncd_user'] = $config['rsyncd']['rsyncd_user'];
$pconfig['enable'] = isset($config['rsyncd']['enable']);

if ($_POST) {
	unset($input_errors);
	$pconfig = $_POST;

	/* input validation */
	$reqdfields = array();
	$reqdfieldsn = array();

	if ($_POST['enable']) {
		$reqdfields = array_merge($reqdfields, explode(" ", "port"));
		$reqdfieldsn = array_merge($reqdfieldsn, array(gettext("TCP port")));
	}

	do_input_validation($_POST, $reqdfields, $reqdfieldsn, &$input_errors);
	
	if ($_POST['enable']) {
		if (!is_port($_POST['port']))
			$input_errors[] = gettext("The TCP port must be a valid port number.");
	}

	if (!$input_errors) {
		$config['rsyncd']['port'] = $_POST['port'];
		$config['rsyncd']['motd'] = $_POST['motd'];
		$config['rsyncd']['enable'] = $_POST['enable'] ? true : false;
		$config['rsyncd']['rsyncd_user'] = $_POST['rsyncd_user'];
		
		write_config();
		
		$retval = 0;
		if (!file_exists($d_sysrebootreqd_path)) {
			config_lock();
			$retval |= rc_update_service("rsyncd");
			$retval |= rc_update_service("mdnsresponder");
			config_unlock();
		}
		$savemsg = get_std_save_message($retval);
	}
}
?>
<?php include("fbegin.inc"); ?>
<script language="JavaScript">
<!--
function enable_change(enable_change) {
	var endis = !(document.iform.enable.checked || enable_change);
	document.iform.port.disabled = endis;
	document.iform.motd.disabled = endis;
	document.iform.rsyncd_user.disabled = endis;
}
//-->
</script>
<?php if ($input_errors) print_input_errors($input_errors); ?>
<?php if ($savemsg) print_info_box($savemsg); ?>
<table width="100%" border="0" cellpadding="0" cellspacing="0">
	<tr>
		<td class="tabnavtbl">
			<ul id="tabnav">
				<li class="tabact"><a href="services_rsyncd.php" style="color:black" title="<?=gettext("Reload page");?>"><?=gettext("Server");?></a></li>
				<li class="tabinact"><a href="services_rsyncd_client.php"><?=gettext("Client");?></a></li>
				<li class="tabinact"><a href="services_rsyncd_local.php"><?=gettext("Local");?></a></li>
			</ul>
		</td>
	</tr>
	<tr>
		<td class="tabnavtbl">
			<ul id="tabnav">
				<li class="tabact"><a href="services_rsyncd.php" title="<?=gettext("Reload page");?>" style="color:black"><?=gettext("Settings");?></a></li>
				<li class="tabinact"><a href="services_rsyncd_share.php"><?=gettext("Shares");?></a></li>
			</ul>
		</td>
	</tr>
	<tr> 
		<td class="tabcont">
			<form action="services_rsyncd.php" method="post" name="iform" id="iform">
				<table width="100%" border="0" cellpadding="6" cellspacing="0">
					<tr> 
						<td colspan="2" valign="top" class="optsect_t">
							<table border="0" cellspacing="0" cellpadding="0" width="100%">
								<tr>
									<td class="optsect_s"><strong><?=gettext("Rsync Daemon");?></strong></td>
				  				<td align="right" class="optsect_s">
										<input name="enable" type="checkbox" value="yes" <?php if ($pconfig['enable']) echo "checked"; ?> onClick="enable_change(false)"> <strong><?=gettext("Enable");?></strong>
									</td>
								</tr>
				  		</table>
						</td>
					</tr>
					<tr>			  		
						<td valign="top" class="vncellreq"><?=gettext("Map to user");?></td>
						<td class="vtable"> 
							<select name="rsyncd_user" class="formfld" id="rsyncd_user">
								<option value="ftp"<?php if ("ftp" === $pconfig['rsyncd_user']) echo "selected";?>><?=gettext("Guest");?></option>
								<?php foreach ($a_user as $user):?>
								<option value="<?=$user['login'];?>"<?php if ($user['login'] === $pconfig['rsyncd_user']) echo "selected";?>><?php echo htmlspecialchars($user['login']);?></option>
								<?php endforeach;?>
							</select>
						</td>
					</tr>
					<tr> 
						<td width="22%" valign="top" class="vncellreq"><?=gettext("TCP port");?></td>
						<td width="78%" class="vtable"> 
							<input name="port" type="text" class="formfld" id="port" size="20" value="<?=htmlspecialchars($pconfig['port']);?>"> 
							<br><?=gettext("Alternate TCP port. Default is 873");?>
						</td>
					</tr>
					<tr>
						<td width="22%" valign="top" class="vncell"><?=gettext("MOTD");?></td>
						<td width="78%" class="vtable"> 
							<textarea name="motd" cols="65" rows="7" id="motd" class="formpre"><?=htmlspecialchars($pconfig['motd']);?></textarea><br/> 
							<?=gettext("Message of the day.");?>
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
<?php include("fend.inc"); ?>
