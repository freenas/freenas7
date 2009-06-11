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
require("auth.inc");
require("guiconfig.inc");

$pgtitle = array(gettext("Network"), gettext("Interface Management"), gettext("VLAN"));

if (!is_array($config['vinterfaces']['vlan']))
	$config['vinterfaces']['vlan'] = array();

$a_vlan = &$config['vinterfaces']['vlan'];
array_sort_key($a_vlan, "if");

function vlan_inuse($ifn) {
	global $config, $g;

	if ($config['interfaces']['lan']['if'] === $ifn)
		return true;

	if ($config['interfaces']['wan']['if'] === $ifn)
		return true;

	for ($i = 1; isset($config['interfaces']['opt' . $i]); $i++) {
		if ($config['interfaces']['opt' . $i]['if'] === $ifn)
			return true;
	}

	return false;
}

if ($_GET['act'] === "del") {
	if (FALSE === ($cnid = array_search_ex($_GET['uuid'], $config['vinterfaces']['vlan'], "uuid"))) {
		header("Location: interfaces_vlan.php");
		exit;
	}

	$vlan = $a_vlan[$cnid];

	// Check if still in use.
	if (vlan_inuse($vlan['if'])) {
		$input_errors[] = gettext("This VLAN cannot be deleted because it is still being used as an interface.");
	} else {
		mwexec("/usr/local/sbin/rconf attribute remove 'ifconfig_{$vlan['if']}'");

		unset($a_vlan[$cnid]);

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
				<li class="tabinact"><a href="interfaces_assign.php"><span><?=gettext("Management");?></span></a></li>
				<li class="tabact"><a href="interfaces_vlan.php" title="<?=gettext("Reload page");?>"><span><?=gettext("VLAN");?></span></a></li>
				<li class="tabinact"><a href="interfaces_lagg.php"><span><?=gettext("LAGG");?></span></a></li>
			</ul>
		</td>
	</tr>
	<tr>
		<td class="tabcont">
			<form action="interfaces_vlan.php" method="post">
				<?php if ($input_errors) print_input_errors($input_errors);?>
				<?php if (file_exists($d_sysrebootreqd_path)) print_info_box(get_std_save_message(0));?>
				<table width="100%" border="0" cellpadding="0" cellspacing="0">
					<tr>
						<td width="20%" class="listhdrr"><?=gettext("Virtual interface");?></td>
						<td width="20%" class="listhdrr"><?=gettext("Physical interface");?></td>
						<td width="5%" class="listhdrr"><?=gettext("VLAN tag");?></td>
						<td width="45%" class="listhdr"><?=gettext("Description");?></td>
						<td width="10%" class="list"></td>
					</tr>
					<?php foreach ($a_vlan as $vlan):?>
					<tr>
						<td class="listlr"><?=htmlspecialchars($vlan['if']);?></td>
						<td class="listr"><?=htmlspecialchars($vlan['vlandev']);?></td>
						<td class="listr"><?=htmlspecialchars($vlan['tag']);?></td>
						<td class="listbg"><?=htmlspecialchars($vlan['desc']);?>&nbsp;</td>
						<td valign="middle" nowrap class="list">
							<a href="interfaces_vlan_edit.php?uuid=<?=$vlan['uuid'];?>"><img src="e.gif" title="<?=gettext("Edit interface");?>" border="0"></a>&nbsp;
							<a href="interfaces_vlan.php?act=del&uuid=<?=$vlan['uuid'];?>" onclick="return confirm('<?=gettext("Do you really want to delete this interface?");?>')"><img src="x.gif" title="<?=gettext("Delete interface");?>" border="0"></a>
						</td>
					</tr>
					<?php endforeach;?>
					<tr>
						<td class="list" colspan="4">&nbsp;</td>
						<td class="list">
							<a href="interfaces_vlan_edit.php"><img src="plus.gif" title="<?=gettext("Add interface");?>" border="0"></a>
						</td>
					</tr>
				</table>
				<div id="remarks">
					<?php html_remark("note", gettext("Note"), gettext("Not all drivers/NICs support 802.1Q VLAN tagging properly. On cards that do not explicitly support it, VLAN tagging will still work, but the reduced MTU may cause problems."));?>
				</div>
			</form>
		</td>
	</tr>
</table>
<?php include("fend.inc");?>
