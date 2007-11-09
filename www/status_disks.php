#!/usr/local/bin/php
<?php
/*
	status_disks.php
	part of FreeNAS (http://www.freenas.org)
	Copyright (C) 2005-2007 Olivier Cochard <olivier@freenas.org>.
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

$pgtitle = array(gettext("Status"), gettext("Disks"));

if (!is_array($config['disks']['disk']))
	$config['disks']['disk'] = array();
	
array_sort_key($config['disks']['disk'], "name");

$raidstatus=get_sraid_disks_list();
$a_disk_conf = &$config['disks']['disk'];

/* Get the disk temperature */
function get_disk_temp($diskname) {
	$temperature = gettext("n/a");

  exec("/usr/local/sbin/smartctl -A /dev/{$diskname['name']}", $smartctlinfo);
	
  foreach($smartctlinfo as $smartctl) {
    $asmartctl = preg_split("/\s+/", $smartctl);

    $id = trim($asmartctl[0]);
    $attributename = trim($asmartctl[1]);

    if((0 == strncmp($attributename, "Temperature_", 12)) && (0 != strcmp($id, "190"))) {
      $temperature = chop($asmartctl[9])." C";
      break;
    }
  }

	return $temperature;
}
?>
<?php include("fbegin.inc"); ?>
<table width="100%" border="0" cellpadding="0" cellspacing="0">
  <tr>
    <td width="5%" class="listhdrr"><?=gettext("Disk");?></td>
    <td width="5%" class="listhdrr"><?=gettext("Size");?></td>
    <td width="60%" class="listhdrr"><?=gettext("Description");?></td>
    <td width="10%" class="listhdrr"><?=gettext("Temperature");?></td>
    <td width="10%" class="listhdrr"><?=gettext("Status");?></td>
	</tr>
	<?php foreach ($a_disk_conf as $disk): ?>
	<tr>
		<td class="listlr"><?=htmlspecialchars($disk['name']);?></td>
		<td class="listr"><?=htmlspecialchars($disk['size']);?></td>
		<td class="listr"><?=htmlspecialchars($disk['desc']);?>&nbsp;</td>
		<td class="listr"><?php echo get_disk_temp($disk);?>&nbsp;</td>
		<td class="listbg"><?php echo gettext(disks_status($disk));?>&nbsp;</td>
	</tr>
	<?php endforeach; ?>
  <?php if (isset($raidstatus)): ?>
	<?php foreach ($raidstatus as $diskk => $diskv): ?>
	<tr>
		<td class="listlr"><?=htmlspecialchars($diskk);?></td>
		<td class="listr"><?=htmlspecialchars($diskv['size']);?></td>
		<td class="listr"><?=htmlspecialchars(gettext("Software RAID"));?>&nbsp;</td>
		<td class="listr"><?php echo get_disk_temp($disk);?>&nbsp;</td>
		<td class="listbg"><?=htmlspecialchars($diskv['state']);?>&nbsp;</td>
	</tr>
	<?php endforeach; ?>
	<?php endif; ?>
</table>
<?php include("fend.inc"); ?>
