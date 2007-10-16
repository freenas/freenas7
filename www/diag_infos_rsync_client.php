#!/usr/local/bin/php
<?php 
/*
	diag_infos_rsync_client.php
	Copyright © 2007 Volker Theile (votdev@gmx.de)
  All rights reserved.

	part of FreeNAS (http://www.freenas.org)
	Copyright (C) 2005-2007 Olivier Cochard-Labbé <olivier@freenas.org>.
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
$pgtitle = array(gettext("Diagnostics"), gettext("Information"), gettext("RSYNC Client"));
?>
<?php include("fbegin.inc"); ?>
<table width="100%" border="0" cellpadding="0" cellspacing="0">
  <tr><td class="tabnavtbl">
  <ul id="tabnav">
    <li class="tabinact"><a href="diag_infos.php"><?=gettext("Disks");?></a></li>
    <li class="tabinact"><a href="diag_infos_part.php"><?=gettext("Partitions");?></a></li>
    <li class="tabinact"><a href="diag_infos_smart.php"><?=gettext("S.M.A.R.T.");?></a></li>
    <li class="tabinact"><a href="diag_infos_ataidle.php"><?=gettext("ATAidle");?></a></li>
    <li class="tabinact"><a href="diag_infos_space.php"><?=gettext("Space Used");?></a></li>
    <li class="tabinact"><a href="diag_infos_mount.php"><?=gettext("Mounts");?></a></li>
    <li class="tabinact"><a href="diag_infos_raid.php"><?=gettext("Software RAID");?></a></li>
    <li class="tabinact"><a href="diag_infos_iscsi.php"><?=gettext("iSCSI Initiator");?></a></li>
    <li class="tabinact"><a href="diag_infos_ad.php"><?=gettext("MS Domain");?></a></li>
	<li class="tabinact"><a href="diag_infos_swap.php"><?=gettext("Swap");?></a></li>
	<li class="tabinact"><a href="diag_infos_sensors.php"><?=gettext("Sensors");?></a></li>
	<li class="tabinact"><a href="diag_infos_ftpd.php"><?=gettext("FTP users");?></a></li>
	<li class="tabact"><a href="diag_infos_rsync_client.php" title="<?=gettext("Reload page");?>" style="color:black"><?=gettext("RSYNC Client");?></a></li>
		
  </ul>
  </td></tr>
  <tr>
    <td class="tabcont">
      <?php
	   if (!is_array($config['rsync']['rsyncclient'])) {
      	echo "<strong>".gettext("No RSYNC Client configured")."</strong><br><br>";
      } else {
		echo("<pre>");
		echo("<strong>" . gettext("Detected RSYNC remote shares") . ":</strong><br><br>");
		$i=0;
		foreach($config['rsync']['rsyncclient'] as $rsyncclient) {
			echo("<br>RSYNC client number $i:<br>");
			echo("- Remote server address: {$rsyncclient['rsyncserverip']}<br>");
			echo("- Remote share name configured : {$rsyncclient['remoteshare']}<br>");
			echo("- Detected shares on this server: <br>");
			exec("/usr/local/bin/rsync {$rsyncclient['rsyncserverip']}::", $rawdata);
			foreach($rawdata as $line) {
				echo "$line";
				echo "<br>";
			}
			unset ($line);
		}
		echo "</pre>";
	  }
      ?>
    </td>
  </tr>
</table>
<?php include("fend.inc"); ?>
