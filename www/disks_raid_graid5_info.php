#!/usr/local/bin/php
<?php
/*
	disks_raid_graid5_info.php
	
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

$pgtitle = array(gettext("Disks"), gettext("Software RAID"), gettext("RAID5"), gettext("Information"));
$pgrefresh = 5; // Refresh every 5 seconds.

?>
<?php include("fbegin.inc");?>
<table width="100%" border="0" cellpadding="0" cellspacing="0">
	<tr><td class="tabnavtbl">
  <ul id="tabnav">
	<li class="tabinact"><a href="disks_raid_gconcat.php"><span><?=gettext("JBOD");?></span></a></li>
	<li class="tabinact"><a href="disks_raid_gstripe.php"><span><?=gettext("RAID 0");?></span></a></li>
	<li class="tabinact"><a href="disks_raid_gmirror.php"><span><?=gettext("RAID 1");?></span></a></li>
	<li class="tabact"><a href="disks_raid_graid5.php" title="<?=gettext("Reload page");?>"><span><?=gettext("RAID 5");?></span></a></li>
	<li class="tabinact"><a href="disks_raid_gvinum.php"><span><?=gettext("Geom Vinum");?> <?=gettext("(unstable)");?></span></a></li>
  </ul>
  </td></tr>
  <tr><td class="tabnavtbl">
  <ul id="tabnav">
	<li class="tabinact"><a href="disks_raid_graid5.php"><span><?=gettext("Manage RAID"); ?></span></a></li>
	<li class="tabinact"><a href="disks_raid_graid5_tools.php"><span><?=gettext("Tools"); ?></span></a></li>
	<li class="tabact"><a href="disks_raid_graid5_info.php" title="<?=gettext("Reload page");?>" ><span><?=gettext("Information");?></span></a></li>
  </ul>
  </td></tr>
  <tr> 
    <td class="tabcont">
			<?php
			echo "<pre>";
			echo "<strong>" . gettext("Software RAID information and status") . "</strong><br>";
			exec("/sbin/graid5 list",$rawdata);
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
