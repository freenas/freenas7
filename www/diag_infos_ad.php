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
$pgtitle = array(gettext("Diagnostics"), gettext("Information"), gettext("MS Active Directory"));

if (!is_array($config['ad'])) {
	$config['ad'] = array();
}
?>
<?php include("fbegin.inc");?>
<table width="100%" border="0" cellpadding="0" cellspacing="0">
  <tr>
		<td class="tabnavtbl">
			<ul id="tabnav">
				<li class="tabinact"><a href="diag_infos.php"><span><?=gettext("Disks");?></span></a></li>
				<li class="tabinact"><a href="diag_infos_ata.php"><span><?=gettext("Disks (ATA)");?></span></a></li>
				<li class="tabinact"><a href="diag_infos_part.php"><span><?=gettext("Partitions");?></span></a></li>
				<li class="tabinact"><a href="diag_infos_smart.php"><span><?=gettext("S.M.A.R.T.");?></span></a></li>
				<li class="tabinact"><a href="diag_infos_space.php"><span><?=gettext("Space Used");?></span></a></li>
				<li class="tabinact"><a href="diag_infos_mount.php"><span><?=gettext("Mounts");?></span></a></li>
				<li class="tabinact"><a href="diag_infos_raid.php"><span><?=gettext("Software RAID");?></span></a></li>
				<li class="tabinact"><a href="diag_infos_iscsi.php"><span><?=gettext("iSCSI Initiator");?></span></a></li>
				<li class="tabact"><a href="diag_infos_ad.php" title="<?=gettext("Reload page");?>"><span><?=gettext("MS Domain");?></span></a></li>
				<li class="tabinact"><a href="diag_infos_samba.php"><span><?=gettext("CIFS/SMB");?></span></a></li>
				<li class="tabinact"><a href="diag_infos_ftpd.php"><span><?=gettext("FTP");?></span></a></li>
				<li class="tabinact"><a href="diag_infos_rsync_client.php"><span><?=gettext("RSYNC Client");?></span></a></li>
				<li class="tabinact"><a href="diag_infos_swap.php"><span><?=gettext("Swap");?></span></a></li>
				<li class="tabinact"><a href="diag_infos_sockets.php"><span><?=gettext("Sockets");?></span></a></li>
				<li class="tabinact"><a href="diag_infos_ups.php"><span><?=gettext("UPS");?></span></a></li>
			</ul>
		</td>
	</tr>
	<tr>
		<td class="tabcont">
			<table width="100%" border="0">
				<tr>
					<td class="listtopic"><?=gettext("MS Active Directory informations");?></td>
				</tr>
				<tr>
					<td>
						<?php if (!isset($config['ad']['enable'])):?>
						<pre><br/><?=gettext("AD authentication disabled");?></pre>
						<?php else:?>
						<pre><br/><?php
						echo gettext("Results for net rpc testjoin:") . "<br>";
						system("/usr/local/bin/net rpc testjoin -S {$config['ad']['domaincontrollername']} 2>&1");
						echo "<br/>" . gettext("Ping winbindd to see if it is alive:") . "<br>";
						system("/usr/local/bin/wbinfo -p 2>&1");
						echo "<br/>" . gettext("Check shared secret:") . "<br>";
						system("/usr/local/bin/wbinfo -t 2>&1");
						?></pre>
					</td>
				</tr>
				<tr>
					<td class="listtopic"><?=gettext("List of imported users");?></td>
				</tr>
				<tr>
					<td>
						<pre><br/><?php
						exec("/usr/local/bin/net rpc user -S {$config['ad']['domaincontrollername']} -U {$config['ad']['username']}%{$config['ad']['password']} 2>&1", $rawdata);
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
