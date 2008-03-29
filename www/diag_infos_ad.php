#!/usr/local/bin/php
<?php
/*
	diag_infos_ad.php
	part of FreeNAS (http://www.freenas.org)
	Copyright (C) 2005-2008 Olivier Cochard-Labb� <olivier@freenas.org>.
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
$pgtitle = array(gettext("Diagnostics"), gettext("Information"), gettext("MS Domain"));

if (!is_array($config['ad'])) {
	$config['ad'] = array();
}
?>
<?php include("fbegin.inc");?>
<table width="100%" border="0" cellpadding="0" cellspacing="0">
  <tr>
		<td class="tabnavtbl">
			<ul id="tabnav">
				<li class="tabinact"><a href="diag_infos.php"><?=gettext("Disks");?></a></li>
				<li class="tabinact"><a href="diag_infos_ata.php"><?=gettext("Disks (ATA)");?></a></li>
				<li class="tabinact"><a href="diag_infos_part.php"><?=gettext("Partitions");?></a></li>
				<li class="tabinact"><a href="diag_infos_smart.php"><?=gettext("S.M.A.R.T.");?></a></li>
				<li class="tabinact"><a href="diag_infos_space.php"><?=gettext("Space Used");?></a></li>
				<li class="tabinact"><a href="diag_infos_mount.php"><?=gettext("Mounts");?></a></li>
				<li class="tabinact"><a href="diag_infos_raid.php"><?=gettext("Software RAID");?></a></li>
				<li class="tabinact"><a href="diag_infos_iscsi.php"><?=gettext("iSCSI Initiator");?></a></li>
				<li class="tabact"><a href="diag_infos_ad.php" title="<?=gettext("Reload page");?>"><?=gettext("MS Domain");?></a></li>
				<li class="tabinact"><a href="diag_infos_samba.php"><?=gettext("CIFS/SMB");?></a></li>
				<li class="tabinact"><a href="diag_infos_ftpd.php"><?=gettext("FTP");?></a></li>
				<li class="tabinact"><a href="diag_infos_rsync_client.php"><?=gettext("RSYNC Client");?></a></li>
				<li class="tabinact"><a href="diag_infos_swap.php"><?=gettext("Swap");?></a></li>
				<li class="tabinact"><a href="diag_infos_sockets.php"><?=gettext("Sockets");?></a></li>
			</ul>
		</td>
	</tr>
	<tr>
		<td class="tabcont">
			<table width="100%" border="0">
				<tr>
					<td class="listtopic"><?=gettext("MS Domain informations");?></td>
				</tr>
				<tr>
					<td>
						<?php if (!isset($config['ad']['enable'])):?>
						<pre><br/><?=gettext("AD authentication disabled");?></pre>
						<?php else:?>
						<pre><br/><?php
						echo "<strong>".gettext("Accessiblity test to MS domain").":</strong><br><br>";
						echo gettext("Results for net rpc testjoin:") . "<br>";
						exec("/usr/local/bin/net rpc testjoin", $rawdata);
						echo implode("\n", $rawdata);
						unset($rawdata);
						echo "<br/>" . gettext("Ping winbindd to see if it is alive:") . "<br>";
						exec("/usr/local/bin/wbinfo -p", $rawdata);
						echo implode("\n", $rawdata);
						unset($rawdata);
						echo "<br/><br/>" . gettext("Check shared secret:") . "<br>";
						exec("/usr/local/bin/wbinfo -t", $rawdata);
						echo implode("\n", $rawdata);
						unset($rawdata);
						?></pre>
					</td>
				</tr>
				<tr>
					<td class="listtopic"><?=gettext("List of imported users");?></td>
				</tr>
				<tr>
					<td>
						<pre><br/><?php
						exec("/usr/local/bin/net rpc user -S {$config['ad']['ad_srv_name']} -U {$config['ad']['admin_name']}%{$config['ad']['admin_pass']}", $rawdata);
						echo implode("\n", $rawdata);
						unset($rawdata);
						?></pre>
						<?php endif;?>
					</td>
				</tr>
			</table>
		</td>
	</tr>
</table>
<?php include("fend.inc");?>
