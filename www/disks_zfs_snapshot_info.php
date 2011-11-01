#!/usr/local/bin/php
<?php
/*
	disks_zfs_snapshot_info.php
	Modified for XHTML by Daisuke Aoyama (aoyama@peach.ne.jp)
	Copyright (C) 2010-2011 Daisuke Aoyama <aoyama@peach.ne.jp>.
	All rights reserved.

	Copyright (c) 2008-2010 Volker Theile (votdev@gmx.de)
	All rights reserved.

	part of FreeNAS (http://www.freenas.org)
	Copyright (C) 2005-2011 Olivier Cochard-Labbe <olivier@freenas.org>.
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
require("auth.inc");
require("guiconfig.inc");

$pgtitle = array(gettext("Disks"), gettext("ZFS"), gettext("Snapshots"), gettext("Information"));

//if (!isset($config['zfs']['snapshots']) || !is_array($config['zfs']['snapshots']['snaposhot']))
//	$config['zfs']['snapshots']['snapshot'] = array();

//$a_snapshot = &$config['zfs']['snapshots']['snapshot'];

function zfs_snapshot_display_list() {
	mwexec2("zfs list -t snapshot 2>&1", $rawdata);
	return implode("\n", $rawdata);
}

function zfs_snapshot_display_properties() {
	mwexec2("zfs list -H -o name -t snapshot 2>&1", $rawdata);
	$snaps = implode(" ", $rawdata);
	$rawdata2 = array();
	if (!empty($snaps)) {
		mwexec2("zfs get all $snaps 2>&1", $rawdata2);
	}
	return implode("\n", $rawdata2);
}
?>
<?php include("fbegin.inc");?>
<table width="100%" border="0" cellpadding="0" cellspacing="0">
	<tr>
		<td class="tabnavtbl">
			<ul id="tabnav">
				<li class="tabinact"><a href="disks_zfs_zpool.php"><span><?=gettext("Pools");?></span></a></li>
				<li class="tabinact"><a href="disks_zfs_dataset.php"><span><?=gettext("Datasets");?></span></a></li>
				<li class="tabinact"><a href="disks_zfs_volume.php"><span><?=gettext("Volumes");?></span></a></li>
				<li class="tabact"><a href="disks_zfs_snapshot.php" title="<?=gettext("Reload page");?>"><span><?=gettext("Snapshots");?></span></a></li>
				<li class="tabinact"><a href="disks_zfs_config.php"><span><?=gettext("Configuration");?></span></a></li>
			</ul>
		</td>
	</tr>
	<tr>
		<td class="tabnavtbl">
			<ul id="tabnav2">
				<li class="tabinact"><a href="disks_zfs_snapshot.php"><span><?=gettext("Snapshot");?></span></a></li>
				<li class="tabinact"><a href="disks_zfs_snapshot_clone.php"><span><?=gettext("Clone");?></span></a></li>
				<li class="tabact"><a href="disks_zfs_snapshot_info.php" title="<?=gettext("Reload page");?>"><span><?=gettext("Information");?></span></a></li>
			</ul>
		</td>
	</tr>
	<tr>
		<td class="tabcont">
			<table width="100%" border="0">
				<?php html_titleline(gettext("ZFS snapshot information and status"));?>
				<tr>
					<td class="listt">
						<pre><span id="zfs_snapshot_list"><?=zfs_snapshot_display_list();?></span></pre>
					</td>
				</tr>
				<?php html_titleline(gettext("ZFS snapshot properties"));?>
				<tr>
					<td class="listt">
						<pre><span id="zfs_snapshot_properties"><?=zfs_snapshot_display_properties();?></span></pre>
					</td>
				</tr>
			</table>
		</td>
	</tr>
</table>
<?php include("fend.inc");?>
