#!/usr/local/bin/php
<?php 
/*
	diag_infos.php
	part of FreeNAS (http://www.freenas.org)
	Copyright (C) 2005-2006 Olivier Cochard-Labbé <olivier@freenas.org>.
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
$pgtitle = array(_DIAGINFOS_NAME, _DIAGINFOS_NAMEDESC);
?>
<?php include("fbegin.inc"); ?>
<table width="100%" border="0" cellpadding="0" cellspacing="0">
  <tr><td class="tabnavtbl">
  <ul id="tabnav">
	<li class="tabact"><a href="diag_infos.php" style="color:black" title="reload page">Disks</a></li>
    <li class="tabinact"><a href="diag_infos_part.php">Partitions</a></li>
    <li class="tabinact"><a href="diag_infos_smart.php">SMART</a></li>
    <li class="tabinact"><a href="diag_infos_ataidle.php">ATAidle</a></li>
    <li class="tabinact"><a href="diag_infos_space.php">Space Used</a></li>
    <li class="tabinact"><a href="diag_infos_mount.php">Mounts</a></li>
    <li class="tabinact"><a href="diag_infos_raid.php">Software RAID</a></li>
    <li class="tabinact"><a href="diag_infos_iscsi.php">iSCSI</a></li>
    <li class="tabinact"><a href="diag_infos_ad.php">MS Domain</a></li> 
  </ul>
  </td></tr>
</table>
<?php

exec("/sbin/atacontrol list",$iderawdata);

echo "<pre>";

echo "<strong>List of detected ATA disks:</strong><br>";
foreach ($iderawdata as $line)
{
       echo htmlspecialchars($line) . "<br>";
}

unset ($line);

exec("/sbin/camcontrol devlist",$scsirawdata);

echo "<strong>List of detected SCSI disks:</strong><br>";
foreach ($scsirawdata as $line)
{
	echo htmlspecialchars($line) . "<br>";

}

echo "</pre>";
?>

<?php include("fend.inc"); ?>
