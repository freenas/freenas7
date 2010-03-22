#!/usr/local/bin/php
<?php
/*
	disks_zfs_dataset_info.php
	Modified for XHTML by Daisuke Aoyama (aoyama@peach.ne.jp)
	Copyright (C) 2010 Daisuke Aoyama <aoyama@peach.ne.jp>.
	All rights reserved.

	Copyright (c) 2008-2010 Volker Theile (votdev@gmx.de)
	All rights reserved.

	part of FreeNAS (http://www.freenas.org)
	Copyright (C) 2005-2010 Olivier Cochard-Labbe <olivier@freenas.org>.
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

$pgtitle = array(gettext("Disks"), gettext("ZFS"), gettext("Datasets"), gettext("Information"));

if (!isset($config['zfs']['datasets']) || !is_array($config['zfs']['datasets']['dataset']))
	$config['zfs']['datasets']['dataset'] = array();

$a_dataset = &$config['zfs']['datasets']['dataset'];

function zfs_dataset_display_list() {
	mwexec2("zfs list 2>&1", $rawdata);
	return implode("\n", $rawdata);
}

function zfs_dataset_display_properties() {
	mwexec2("zfs get all 2>&1", $rawdata);
	return implode("\n", $rawdata);
}
?>
<?php include("fbegin.inc");?>
<table width="100%" border="0" cellpadding="0" cellspacing="0">
	<tr>
		<td class="tabnavtbl">
			<ul id="tabnav">
				<li class="tabinact"><a href="disks_zfs_zpool.php"><span><?=gettext("Pools");?></span></a></li>
				<li class="tabact"><a href="disks_zfs_dataset.php" title="<?=gettext("Reload page");?>"><span><?=gettext("Datasets");?></span></a></li>
				<li class="tabinact"><a href="disks_zfs_config.php"><span><?=gettext("Configuration");?></span></a></li>
			</ul>
		</td>
	</tr>
	<tr>
		<td class="tabnavtbl">
			<ul id="tabnav2">
				<li class="tabinact"><a href="disks_zfs_dataset.php"><span><?=gettext("Dataset");?></span></a></li>
				<li class="tabact"><a href="disks_zfs_dataset_info.php" title="<?=gettext("Reload page");?>"><span><?=gettext("Information");?></span></a></li>
			</ul>
		</td>
	</tr>
	<tr>
		<td class="tabcont">
			<table width="100%" border="0">
				<?php html_titleline(gettext("ZFS dataset information and status"));?>
				<tr>
					<td class="listt">
						<pre><span id="zfs_dataset_list"><?=zfs_dataset_display_list();?></span></pre>
					</td>
				</tr>
				<?php html_titleline(gettext("ZFS dataset properties"));?>
				<tr>
					<td class="listt">
						<pre><span id="zfs_dataset_properties"><?=zfs_dataset_display_properties();?></span></pre>
					</td>
				</tr>
			</table>
		</td>
	</tr>
</table>
<?php include("fend.inc");?>
