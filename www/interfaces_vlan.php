#!/usr/local/bin/php
<?php
/*
	interfaces_vlan.php
	part of m0n0wall (http://m0n0.ch/wall)

	Copyright (C) 2003-2005 Manuel Kasper <mk@neon1.net>.
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

$pgtitle = array(gettext("Interfaces"), gettext("Management"), gettext("VLAN"));

if (!is_array($config['vlans']['vlan']))
	$config['vlans']['vlan'] = array();

$a_vlans = &$config['vlans']['vlan'];
array_sort_key($a_vlans, "tag");

function vlan_inuse($num) {
	global $config, $g;

	if ($config['interfaces']['lan']['if'] == "vlan{$num}")
		return true;
	if ($config['interfaces']['wan']['if'] == "vlan{$num}")
		return true;

	for ($i = 1; isset($config['interfaces']['opt' . $i]); $i++) {
		if ($config['interfaces']['opt' . $i]['if'] == "vlan{$num}")
			return true;
	}

	return false;
}

if ($_GET['act'] == "del") {
	// Check if still in use.
	if (vlan_inuse($_GET['id'])) {
		$input_errors[] = gettext("This VLAN cannot be deleted because it is still being used as an interface.");
	} else {
		$vlan = $a_vlans[$_GET['id']];
		mwexec("/usr/local/sbin/rconf attribute remove 'ifconfig_vlan{$vlan['id']}'");

		unset($a_vlans[$_GET['id']]);

		write_config();
		touch($d_sysrebootreqd_path);

		header("Location: interfaces_vlan.php");
		exit;
	}
}
?>
<?php include("fbegin.inc");?>
<table width="100%" border="0" cellpadding="0" cellspacing="0">
	<tr>
		<td class="tabnavtbl">
		  <ul id="tabnav">
				<li class="tabinact"><a href="interfaces_assign.php"><?=gettext("Management");?></a></li>
				<li class="tabact"><a href="interfaces_vlan.php" title="<?=gettext("Reload page");?>"><?=gettext("VLAN");?></a></li>
			</ul>
		</td>
	</tr>
	<tr>
		<td class="tabcont">
			<form action="disks_raid_gmirror.php" method="post">
				<?php if ($input_errors) print_input_errors($input_errors); ?>
				<?php if (file_exists($d_sysrebootreqd_path)) print_info_box(get_std_save_message(0)); ?>
				<table width="100%" border="0" cellpadding="0" cellspacing="0">
					<tr>
						<td width="20%" class="listhdrr"><?=gettext("Virtual interface");?></td>
						<td width="20%" class="listhdrr"><?=gettext("Physical interface");?></td>
						<td width="5%" class="listhdrr"><?=gettext("VLAN tag");?></td>
						<td width="45%" class="listhdr"><?=gettext("Description");?></td>
						<td width="10%" class="list"></td>
					</tr>
					<?php $i = 0; foreach ($a_vlans as $vlan):?>
					<tr>
						<td class="listlr">vlan<?=htmlspecialchars($vlan['id']);?></td>
						<td class="listr"><?=htmlspecialchars($vlan['if']);?></td>
						<td class="listr"><?=htmlspecialchars($vlan['tag']);?></td>
						<td class="listbg"><?=htmlspecialchars($vlan['desc']);?>&nbsp;</td>
						<td valign="middle" nowrap class="list"> <a href="interfaces_vlan_edit.php?id=<?=$i;?>"><img src="e.gif" title="<?=gettext("Edit VLAN");?>" width="17" height="17" border="0"></a>&nbsp;<a href="interfaces_vlan.php?act=del&id=<?=$i;?>" onclick="return confirm('<?=gettext("Do you really want to delete this VLAN?");?>')"><img src="x.gif" title="<?=gettext("Delete VLAN");?>" width="17" height="17" border="0"></a></td>
					</tr>
					<?php $i++; endforeach;?>
					<tr>
						<td class="list" colspan="4">&nbsp;</td>
						<td class="list"> <a href="interfaces_vlan_edit.php"><img src="plus.gif" title="<?=gettext("Add VLAN");?>" width="17" height="17" border="0"></a></td>
					</tr>
					<tr>
						<td class="list" colspan="4">
							<span class="red"><strong><?=gettext("Note");?>:</strong></span><br/>
							<?=gettext("Not all drivers/NICs support 802.1Q VLAN tagging properly. On cards that do not explicitly support it, VLAN tagging will still work, but the reduced MTU may cause problems.");?>
						</td>
					</tr>
				</table>
			</form>
		</td>
	</tr>
</table>
<?php include("fend.inc");?>
