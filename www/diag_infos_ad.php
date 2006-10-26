#!/usr/local/bin/php
<?php 
/*
	diag_infos_ad.php
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

if (!is_array($config['ad']))
{
	$config['ad'] = array();
	
}

?>
<?php include("fbegin.inc"); ?>
<table width="100%" border="0" cellpadding="0" cellspacing="0">
  <tr><td class="tabnavtbl">
  <ul id="tabnav">
	<li class="tabinact"><a href="diag_infos.php">Disks</a></li>
    <li class="tabinact"><a href="diag_infos_part.php">Partitions</a></li>
    <li class="tabinact"><a href="diag_infos_smart.php">SMART</a></li>
    <li class="tabinact"><a href="diag_infos_ataidle.php">ataidle</a></li>
    <li class="tabinact"><a href="diag_infos_space.php">Space Used</a></li>
    <li class="tabinact"><a href="diag_infos_mount.php">Mounts</a></li>
    <li class="tabinact"><a href="diag_infos_raid.php">Software RAID</a></li>
    <li class="tabinact"><a href="diag_infos_iscsi.php">iSCSI</a></li>
    <li class="tabact"><a href="diag_infos_ad.php" title="reload page" style="color:black">MS Domain</a></li>
  </ul>
  </td></tr>
</table>
<?php


if (!isset($config['ad']['enable']))
{
	echo  "<strong>AD Authentication disable</strong><br>";
}
else
{

	echo "<pre>";
	
	echo "<strong>Accessiblity test to MS domain:</strong><br>";
	
	echo htmlspecialchars("Results for net rpc testjoin:") . "<br>";
		
	exec("/usr/local/bin/net rpc testjoin",$rawdata);
	foreach ($rawdata as $line)
	{
		echo htmlspecialchars($line) . "<br>";
	}
	unset ($rawdata);
	
	echo "<br>";
	
	echo htmlspecialchars("Ping winbindd to see if it is alive:") . "<br>";
		
	exec("/usr/local/bin/wbinfo -p",$rawdata);
	foreach ($rawdata as $line)
	{
		echo htmlspecialchars($line) . "<br>";
	}
	unset ($rawdata);
	
	echo "<br>";
	
	echo htmlspecialchars("Check shared secret:") . "<br>";
		
	exec("/usr/local/bin/wbinfo -t",$rawdata);
	foreach ($rawdata as $line)
	{
		echo htmlspecialchars($line) . "<br>";
	}
	unset ($rawdata);
	
	echo "<br>";
	
	echo "</pre>";
}
?>

<?php include("fend.inc"); ?>
