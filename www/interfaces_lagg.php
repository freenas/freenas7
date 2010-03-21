#!/usr/local/bin/php
<?php
/*
	interfaces_lagg.php
	Modified for XHTML by Daisuke Aoyama (aoyama@peach.ne.jp)
	Copyright (C) 2010 Daisuke Aoyama <aoyama@peach.ne.jp>.
	All rights reserved.

	Copyright (C) 2006-2010 Volker Theile (votdev@gmx.de)
	All rights reserved.

	part of FreeNAS (http://www.freenas.org)
	Copyright (C) 2005-2010 Olivier Cochard-Labbe <olivier@freenas.org>.
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

$pgtitle = array(gettext("Network"), gettext("Interface Management"), gettext("Link Aggregation and Failover"));

if (!is_array($config['vinterfaces']['lagg']))
	$config['vinterfaces']['lagg'] = array();

$a_lagg = &$config['vinterfaces']['lagg'];
array_sort_key($a_lagg, "if");

function lagg_inuse($ifn) {
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
	if (FALSE === ($cnid = array_search_ex($_GET['uuid'], $config['vinterfaces']['lagg'], "uuid"))) {
		header("Location: interfaces_lagg.php");
		exit;
	}

	$lagg = $a_lagg[$cnid];

	// Check if still in use.
	if (lagg_inuse($lagg['if'])) {
		$input_errors[] = gettext("This LAGG cannot be deleted because it is still being used as an interface.");
	} else {
		mwexec("/usr/local/sbin/rconf attribute remove 'ifconfig_{$lagg['if']}'");

		unset($a_lagg[$cnid]);

		write_config();
		touch($d_sysrebootreqd_path);

		header("Location: interfaces_lagg.php");
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
				<li class="tabinact"><a href="interfaces_vlan.php"><span><?=gettext("VLAN");?></span></a></li>
				<li class="tabact"><a href="interfaces_lagg.php" title="<?=gettext("Reload page");?>"><span><?=gettext("LAGG");?></span></a></li>
			</ul>
		</td>
	</tr>
	<tr>
		<td class="tabcont">
			<form action="interfaces_lagg.php" method="post">
				<?php if ($input_errors) print_input_errors($input_errors);?>
				<?php if (file_exists($d_sysrebootreqd_path)) print_info_box(get_std_save_message(0));?>
				<table width="100%" border="0" cellpadding="0" cellspacing="0">
					<tr>
						<td width="20%" class="listhdrlr"><?=gettext("Virtual interface");?></td>
						<td width="20%" class="listhdrr"><?=gettext("Ports");?></td>
						<td width="45%" class="listhdrr"><?=gettext("Description");?></td>
						<td width="10%" class="list"></td>
					</tr>
					<?php foreach ($a_lagg as $lagg):?>
					<tr>
						<td class="listlr"><?=htmlspecialchars($lagg['if']);?></td>
						<td class="listr"><?=htmlspecialchars(implode(" ", $lagg['laggport']));?></td>
						<td class="listbg"><?=htmlspecialchars($lagg['desc']);?>&nbsp;</td>
						<td valign="middle" nowrap="nowrap" class="list">
							<a href="interfaces_lagg_edit.php?uuid=<?=$lagg['uuid'];?>"><img src="e.gif" title="<?=gettext("Edit interface");?>" border="0" alt="<?=gettext("Edit interface");?>" /></a>&nbsp;
							<a href="interfaces_lagg.php?act=del&amp;uuid=<?=$lagg['uuid'];?>" onclick="return confirm('<?=gettext("Do you really want to delete this interface?");?>')"><img src="x.gif" title="<?=gettext("Delete interface");?>" border="0" alt="<?=gettext("Delete interface");?>" /></a>
						</td>
					</tr>
					<?php endforeach;?>
					<tr>
						<td class="list" colspan="3">&nbsp;</td>
						<td class="list">
							<a href="interfaces_lagg_edit.php"><img src="plus.gif" title="<?=gettext("Add interface");?>" border="0" alt="<?=gettext("Add interface");?>" /></a>
						</td>
					</tr>
				</table>
				<?php include("formend.inc");?>
			</form>
		</td>
	</tr>
</table>
<?php include("fend.inc");?>
