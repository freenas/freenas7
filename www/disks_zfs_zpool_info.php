#!/usr/local/bin/php
<?php
/*
	disks_zfs_zpool_info.php.php
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

$pgtitle = array(gettext("Disks"), gettext("ZFS"), gettext("Information"));
$pgrefresh = 5; // Refresh every 5 seconds.

?>
<?php include("fbegin.inc");?>
<table width="100%" border="0" cellpadding="0" cellspacing="0">
	<tr>
		<td class="tabnavtbl">
  		<ul id="tabnav">
  			<li class="tabinact"><a href="disks_zfs_zpool_vdevice.php"><?=gettext("Virtual device");?></a></li>
				<li class="tabinact"><a href="disks_zfs_zpool.php"><?=gettext("Manage pool");?></a></li>
				<li class="tabact"><a href="disks_zfs_zpool_info.php" title="<?=gettext("Reload page");?>"><?=gettext("Information");?></a></li>
  		</ul>
  	</td>
	</tr>
  <tr> 
  	<td class="tabcont">
			<?php
			echo "<pre>";
			echo "<strong>" . gettext("ZFS information and status") . "</strong><br><br>";
			$cmd = "/sbin/zpool status -v";
			if (isset($id) && $a_pool[$id]) {
				$cmd .= " {$a_pool[$id]['name']}";
			}
			exec($cmd, $rawdata);
			foreach ($rawdata as $line) {
				echo htmlspecialchars($line) . "<br>";
			}
			unset ($line);
			echo "</pre>";
			?>
		</td>
	</tr>
</table>
<?php include("fend.inc");?>
