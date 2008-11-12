#!/usr/local/bin/php
<?php
/*
	system_firewall.php
	Copyright (C) 2008 Volker Theile (votdev@gmx.de)
	All rights reserved.

	part of FreeNAS (http://www.freenas.org)
	Copyright (C) 2005-2008 Olivier Cochard <olivier@freenas.org>.
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

$pgtitle = array(gettext("Network"), gettext("Firewall"));

$pconfig['enable'] = isset($config['system']['firewall']['enable']);

if ($_POST['export']) {
	$fn = "firewall-" . $config['system']['hostname'] . "." . $config['system']['domain'] . "-" . date("YmdHis") . ".rules";
	$backup = serialize($config['system']['firewall']['rule']);
	$fs = strlen($backup);
	header("Content-Type: application/octet-stream");
	header("Content-Disposition: attachment; filename={$fn}");
	header("Content-Length: {$fs}");
	header("Pragma: hack");
	echo($backup);
	exit;
} else if ($_POST['import']) {
	if (is_uploaded_file($_FILES['rulesfile']['tmp_name'])) {
		$rules = unserialize(file_get_contents($_FILES['rulesfile']['tmp_name']));
		foreach ($rules as $rule) {
			$rule['uuid'] = uuid(); // Create new uuid
			$config['system']['firewall']['rule'][] = $rule;
			ui_set_updatenotification("firewall", UPDATENOTIFICATION_MODE_NEW, $rule['uuid']);
		}
		write_config();
		header("Location: system_firewall.php");
		exit;
	} else {
		$input_errors[] = gettext("Failed to upload file.");
	}
} else if ($_POST) {
	$pconfig = $_POST;

	$config['system']['firewall']['enable'] = $_POST['enable'] ? true : false;

	write_config();

	$retval = 0;
	if (!file_exists($d_sysrebootreqd_path)) {
		$retval |= ui_process_updatenotification("firewall", "firewall_process_updatenotification");
		config_lock();
		$retval |= rc_update_service("ipfw");
		config_unlock();
	}
	$savemsg = get_std_save_message($retval);
	if ($retval == 0) {
		ui_cleanup_updatenotification("firewall");
	}
}

if (!is_array($config['system']['firewall']['rule']))
	$config['system']['firewall']['rule'] = array();

array_sort_key($config['system']['firewall']['rule'], "ruleno");
$a_rule = &$config['system']['firewall']['rule'];

if ($_GET['act'] === "del") {
	ui_set_updatenotification("firewall", UPDATENOTIFICATION_MODE_DIRTY, $_GET['uuid']);
	header("Location: system_firewall.php");
	exit;
}

function firewall_process_updatenotification($mode, $data) {
	global $config;

	$retval = 0;

	switch ($mode) {
		case UPDATENOTIFICATION_MODE_NEW:
		case UPDATENOTIFICATION_MODE_MODIFIED:
			break;
		case UPDATENOTIFICATION_MODE_DIRTY:
			if (is_array($config['system']['firewall']['rule'])) {
				$index = array_search_ex($data, $config['system']['firewall']['rule'], "uuid");
				if (false !== $index) {
					unset($config['system']['firewall']['rule'][$index]);
					write_config();
				}
			}
			break;
	}

	return $retval;
}
?>
<?php include("fbegin.inc");?>
<form action="system_firewall.php" method="post" name="iform" id="iform" enctype="multipart/form-data">
	<table width="100%" border="0" cellpadding="0" cellspacing="0">
	  <tr>
	    <td class="tabcont">
				<?php if ($input_errors) print_input_errors($input_errors);?>
				<?php if ($savemsg) print_info_box($savemsg);?>
				<?php if (ui_exists_updatenotification("firewall")) print_config_change_box();?>
				<table width="100%" border="0" cellpadding="6" cellspacing="0">
					<?php html_titleline_checkbox("enable", gettext("Client firewall"), $pconfig['enable'] ? true : false, gettext("Enable"), "enable_change(false)");?>
					<tr>
			    	<td width="22%" valign="top" class="vncell"><?=gettext("Rules");?></td>
						<td width="78%" class="vtable">
				      <table width="100%" border="0" cellpadding="0" cellspacing="0">
								<tr>
									<td width="5%" class="listhdrr"><?=gettext("Proto");?></td>
									<td width="20%" class="listhdrr"><?=gettext("Source");?></td>
									<td width="5%" class="listhdrr"><?=gettext("Port");?></td>
									<td width="20%" class="listhdrr"><?=gettext("Destination");?></td>
									<td width="5%" class="listhdrr"><?=gettext("Port");?></td>
									<td width="5%" class="listhdrr"><?=gettext("<->");?></td>
									<td width="30%" class="listhdr"><?=gettext("Description");?></td>
									<td width="10%" class="list"></td>
								</tr>
								<?php $i = 0; foreach ($a_rule as $rule):?>
								<?php $notificationmode = ui_get_updatenotification_mode("firewall", $rule['uuid']);?>
								<tr>
									<?php $enable = isset($rule['enable']);?>
									<td class="<?=$enable?"listlr":"listlrd";?>"><?=strtoupper($rule['protocol']);?>&nbsp;</td>
									<td class="<?=$enable?"listr":"listrd";?>"><?=htmlspecialchars(empty($rule['src']) ? "*" : $rule['src']);?>&nbsp;</td>
									<td class="<?=$enable?"listr":"listrd";?>"><?=htmlspecialchars(empty($rule['srcport']) ? "*" : $rule['srcport']);?>&nbsp;</td>
									<td class="<?=$enable?"listr":"listrd";?>"><?=htmlspecialchars(empty($rule['dst']) ? "*" : $rule['dst']);?>&nbsp;</td>
									<td class="<?=$enable?"listr":"listrd";?>"><?=htmlspecialchars(empty($rule['dstport']) ? "*" : $rule['dstport']);?>&nbsp;</td>
									<td class="<?=$enable?"listrc":"listrcd";?>"><?=empty($rule['direction']) ? "*" : strtoupper($rule['direction']);?>&nbsp;</td>
									<td class="listbg"><?=htmlspecialchars($rule['desc']);?>&nbsp;</td>
									<?php if (UPDATENOTIFICATION_MODE_DIRTY != $notificationmode):?>
									<td valign="middle" nowrap class="list">
										<a href="system_firewall_edit.php?id=<?=$i;?>"><img src="e.gif" title="<?=gettext("Edit rule");?>" border="0"></a>
										<a href="system_firewall.php?act=del&uuid=<?=$rule['uuid'];?>" onclick="return confirm('<?=gettext("Do you really want to delete this rule?");?>')"><img src="x.gif" title="<?=gettext("Delete rule");?>" border="0"></a>
									</td>
									<?php else:?>
									<td valign="middle" nowrap class="list">
										<img src="del.gif" border="0">
									</td>
									<?php endif;?>
								</tr>
							  <?php $i++; endforeach;?>
								<tr>
									<td class="list" colspan="7"></td>
									<td class="list">
										<a href="system_firewall_edit.php"><img src="plus.gif" title="<?=gettext("Add rule");?>" border="0"></a>
									</td>
								</tr>
							</table>
						</td>
					</tr>
					<tr>
						<td width="22%" valign="top" class="vncell">&nbsp;</td>
						<td width="78%" class="vtable">
							<input name="export" type="submit" class="formbtn" value="<?=gettext("Export");?>"><br/>
							<?=gettext("Download firewall rules.");?>
						</td>
					</tr>
					<tr>
						<td width="22%" valign="top" class="vncell">&nbsp;</td>
						<td width="78%" class="vtable">
							<input name="rulesfile" type="file" class="formfld" id="rulesfile" size="40" accept="*.rules">&nbsp;
							<input name="import" type="submit" class="formbtn" id="import" value="<?=gettext("Import");?>"><br/>
							<?=gettext("Import firewall rules.");?>
						</td>
					</tr>
				</table>
				<div id="submit">
					<input name="Submit" type="submit" class="formbtn" value="<?=gettext("Save and Restart");?>">
				</div>
			</td>
		</tr>
	</table>
</form>
<?php include("fend.inc");?>
