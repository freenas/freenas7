#!/usr/local/bin/php
<?php
/*
	disks_zfs_dataset_info.php
	Copyright (c) 2008 Volker Theile (votdev@gmx.de)
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

$pgtitle = array(gettext("Disks"), gettext("ZFS"), gettext("Datasets"), gettext("Information"));

if (!isset($config['zfs']['datasets']) || !is_array($config['zfs']['datasets']['dataset']))
	$config['zfs']['datasets']['dataset'] = array();

$a_dataset = &$config['zfs']['datasets']['dataset'];
?>
<?php include("fbegin.inc");?>
<table width="100%" border="0" cellpadding="0" cellspacing="0">
	<tr>
		<td class="tabnavtbl">
			<ul id="tabnav">
				<li class="tabinact"><a href="disks_zfs_zpool.php"><?=gettext("Pools");?></a></li>
				<li class="tabact"><a href="disks_zfs_dataset.php" title="<?=gettext("Reload page");?>"><?=gettext("Datasets");?></a></li>
			</ul>
		</td>
	</tr>
	<tr>
		<td class="tabnavtbl">
			<ul id="tabnav">
				<li class="tabinact"><a href="disks_zfs_dataset.php"><?=gettext("Dataset");?></a></li>
				<li class="tabact"><a href="disks_zfs_dataset_info.php" title="<?=gettext("Reload page");?>"><?=gettext("Information");?></a></li>
			</ul>
		</td>
	</tr>
	<tr>
		<td class="tabcont">
			<table width="100%" border="0">
				<tr>
					<td class="listtopic"><?=gettext("ZFS dataset information and status");?></td>
				</tr>
				<tr>
					<td>
						<pre><br/><?php
						exec("/sbin/zfs list", $rawdata);
						foreach ($rawdata as $line) {
							echo htmlspecialchars($line) . "<br/>";
						}
						unset ($line);
						?></pre>
					</td>
				</tr>
				<?php foreach($a_dataset as $datasetv):?>
				<tr>
					<td class="listtopic"><?=sprintf(gettext("Dataset %s"), "{$datasetv['pool'][0]}/{$datasetv['name']}");?></td>
				</tr>
				<tr>
					<td>
						<pre><br/><?php
						exec("/sbin/zfs get all {$datasetv['pool'][0]}/{$datasetv['name']}", $rawdata);
						$rawdata = array_slice($rawdata, 3);
						echo implode("\n", $rawdata);
						unset($rawdata);
						?></pre>
					</td>
				</tr>
				<?php endforeach;?>
			</table>
		</td>
	</tr>
</table>
<?php include("fend.inc");?>
