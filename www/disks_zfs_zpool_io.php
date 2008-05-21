#!/usr/local/bin/php
<?php
/*
	disks_raid_gmirror_info.php
	
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

$id = $_GET['id'];
if (isset($_POST['id']))
	$id = $_POST['id'];
	
$pgtitle = array(gettext("Disks"), gettext("ZFS"), gettext("ZPool"), gettext("IO statistics"));
$pgrefresh = 5; // Refresh every 5 seconds.
	
$a_pool = get_all_conf_pools();
array_sort_key($a_pool, "name");

?>
<?php include("fbegin.inc"); ?>
<table width="100%" border="0" cellpadding="0" cellspacing="0">
<tr>
	<td class="tabnavtbl">
		<ul id="tabnav">
			<li class="tabact"><a href="disks_zfs_zpool.php" title="<?=gettext("Reload page");?>" ><?=gettext("ZPool");?></a></li>
			<li class="tabinact"><a href="disks_zfs.php"><?=gettext("ZFS"); ?></a></li>
		</ul>
	</td>
</tr>
<tr>
	<td class="tabcont">
		<table width="100%" border="0" cellpadding="0" cellspacing="0">
		<tr>
			<td class="tabnavtbl">
				<ul id="tabnav">
					<li class="tabinact"><a href="disks_zfs_zpool_groups.php"><?=gettext("Groups");?></a></li>
					<li class="tabinact"><a href="disks_zfs_zpool.php"><?=gettext("Manage Pool");?></a></li>
					<li class="tabinact"><a href="disks_zfs_zpool_tools.php"><?=gettext("Tools"); ?></a></li>
					<li class="tabinact"><a href="disks_zfs_zpool_info.php"><?=gettext("Information"); ?></a></li>
					<li class="tabact"><a href="disks_zfs_zpool_io.php" title="<?=gettext("Reload page");?>"><?=gettext("IO Status"); ?></a></li>
				</ul>
			</td>
		</tr>
		<tr>
			<td class="tabcont">
				<?php
				echo "<center><h3><strong>" . gettext("ZFS IO statistics") . "</strong></h3></center>";
				echo "<pre>";
				$cmd = "/sbin/zpool iostat -v";
				if(isset($id) && $a_pool[$id]) {
					$cmd .= " ".$a_pool[$id]['name'];
				}
				exec($cmd, $rawdata, $output);
				if($output == 0 ) {
					foreach ($rawdata as $line) {
						echo htmlspecialchars($line);
						echo "<br>";
					}
				} else {
					echo "no pools available";
				}
				echo "</pre>";
				?>
				</td>
		</tr>
		</table>
	</td>
</tr>
</table>
<?php include("fend.inc");?>
