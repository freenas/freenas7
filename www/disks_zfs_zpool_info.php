#!/usr/local/bin/php
<?php
/*
	disks_zfs_zpool_info.php
	Copyright (c) 2008 Volker Theile (votdev@gmx.de)
	Copyright (c) 2008 Nelson Silva
	All rights reserved.

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

$pgtitle = array(gettext("Disks"), gettext("ZFS"), gettext("Pools"), gettext("Information"));
$pgrefresh = 5; // Refresh every 5 seconds.

if (!isset($config['zfs']['pools']) || !is_array($config['zfs']['pools']['pool']))
	$config['zfs']['pools']['pool'] = array();

if (!isset($config['zfs']['vdevices']) || !is_array($config['zfs']['vdevices']['vdevice']))
	$config['zfs']['vdevices']['vdevice'] = array();

array_sort_key($config['zfs']['pools']['pool'], "name");
array_sort_key($config['zfs']['vdevices']['vdevice'], "name");

$a_pool = $config['zfs']['pools']['pool'];
$a_vdevice = $config['zfs']['vdevices']['vdevice'];
?>
<?php include("fbegin.inc");?>
<table width="100%" border="0" cellpadding="0" cellspacing="0">
	<tr>
		<td class="tabnavtbl">
			<ul id="tabnav">
				<li class="tabact"><a href="disks_zfs_zpool.php" title="<?=gettext("Reload page");?>"><span><?=gettext("Pools");?></span></a></li>
				<li class="tabinact"><a href="disks_zfs_dataset.php"><span><?=gettext("Datasets");?></span></a></li>
			</ul>
		</td>
	</tr>
	<tr>
		<td class="tabnavtbl">
  		<ul id="tabnav">
  			<li class="tabinact"><a href="disks_zfs_zpool_vdevice.php"><span><?=gettext("Virtual device");?></span></a></li>
				<li class="tabinact"><a href="disks_zfs_zpool.php"><span><?=gettext("Pool");?></span></a></li>
				<li class="tabinact"><a href="disks_zfs_zpool_tools.php"><span><?=gettext("Tools");?></span></a></li>
				<li class="tabact"><a href="disks_zfs_zpool_info.php" title="<?=gettext("Reload page");?>"><span><?=gettext("Information");?></span></a></li>
				<li class="tabinact"><a href="disks_zfs_zpool_io.php"><span><?=gettext("IO statistics");?></span></a></li>
  		</ul>
  	</td>
	</tr>
  <tr> 
		<td class="tabcont">
			<?php
			echo "<pre>";
			echo "<strong>" . gettext("ZFS pool information and status") . "</strong><br/><br/>";
			$cmd = "/sbin/zpool status -v";
			if (isset($id) && $a_pool[$id]) {
				$cmd .= " {$a_pool[$id]['name']}";
			}
			exec($cmd, $rawdata);
			foreach ($rawdata as $line) {
				if (preg_match("/(\s+)(?:pool\:)(\s+)(.*)/", $line, $match)) {
					$pool = trim($match[3]);
					$index = array_search_ex($pool, $a_pool, "name");
					$href = "<a href='disks_zfs_zpool.php?id={$index}'>{$pool}</a>";
					echo "{$match[1]}pool:{$match[2]}{$href}";
				} else if (preg_match("/(\s+)(?:scrub\:)(\s+)(.*)/", $line, $match)) {
					if (isset($pool)) {
						$href  = "<a href='disks_zfs_zpool_tools.php?action=scrub&option=s&pool={$pool}' title=\"".sprintf(gettext("Start scrub on '%s'."), $pool)."\">scrub</a>:";
					} else {
						$href = "scrub";
					}
					echo "{$match[1]}{$href}{$match[2]}{$match[3]}";
				} else {
					if (isset($pool)) {
						$index = array_search_ex($pool, $a_pool, "name");
						$pool_conf = $a_pool[$index];
						$found = false;
						foreach ($pool_conf['vdevice'] as $vdevicev) {
							$index = array_search_ex($vdevicev, $a_vdevice, "name");
							$vdevice = $a_vdevice[$index];
							foreach ($vdevice['device'] as $devicev) {
								$a_disk = get_conf_disks_filtered_ex("fstype", "zfs");
								$index = array_search_ex($devicev, $a_disk, "devicespecialfile");
								$disk = $a_disk[$index];
								$string = "/(\s+)(?:".$disk['name'].")(\s+)(\w+)(.*)/";
								if (preg_match($string, $line, $match)) {
									$href = "<a href='disks_zfs_zpool_tools.php'>{$disk['name']}</a>";
									if ($match[3] === "ONLINE") {
										$href1 = "<a href='disks_zfs_zpool_tools.php?action=offline&option=d&pool={$pool}&device={$disk[name]}'>{$match[3]}</a>";
									} else if($match[3] == "OFFLINE") {
										$href1 = "<a href='disks_zfs_zpool_tools.php?action=online&option=d&pool={$pool}&device={$disk[name]}'>{$match[3]}</a>";
									} else {
										$href1 = "";
									}
									echo "{$match[1]}{$href}{$match[2]}{$href1}{$match[4]}";
									$found = true;
									continue 2;
								}
							}
						}
						if (!$found) {
							echo htmlspecialchars($line);
						}
					} else {
						echo htmlspecialchars($line);
					}
				}
				echo "<br/>";
			}
			echo "</pre>";
			?>
		</td>
	</tr>
</table>
<?php include("fend.inc");?>
