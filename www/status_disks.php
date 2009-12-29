#!/usr/local/bin/php
<?php
/*
	status_disks.php
	part of FreeNAS (http://www.freenas.org)
	Copyright (C) 2005-2009 Olivier Cochard <olivier@freenas.org>.
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

$pgtitle = array(gettext("Status"), gettext("Disks"));

if (!is_array($config['disks']['disk']))
	$config['disks']['disk'] = array();

array_sort_key($config['disks']['disk'], "name");
$a_disk_conf = &$config['disks']['disk'];

$raidstatus = get_sraid_disks_list();
?>
<?php include("fbegin.inc");?>
<table width="100%" border="0" cellpadding="0" cellspacing="0">
	<tr>
		<td class="tabcont">
			<table width="100%" border="0" cellpadding="0" cellspacing="0">
				<tr>
					<td width="5%" class="listhdrlr"><?=gettext("Disk");?></td>
					<td width="5%" class="listhdrr"><?=gettext("Capacity");?></td>
					<td width="40%" class="listhdrr"><?=gettext("Description");?></td>
					<td width="15%" class="listhdrr"><?=gettext("Serial number"); ?></td>
					<td width="15%" class="listhdrr"><?=gettext("I/O statistics");?></td>
					<td width="10%" class="listhdrr"><?=gettext("Temperature");?></td>
					<td width="10%" class="listhdrr"><?=gettext("Status");?></td>
				</tr>
				<?php foreach ($a_disk_conf as $disk):?>
				<?php (($iostat = system_get_device_iostat($disk['name'])) === FALSE) ? $iostat = gettext("n/a") : $iostat = sprintf("%s KiB/t, %s tps, %s MiB/s", $iostat['kpt'], $iostat['tps'], $iostat['mps']);?>
				<?php (($temp = system_get_device_temp($disk['name'])) === FALSE) ? $temp = gettext("n/a") : $temp = sprintf("%s &deg;C", $temp);?>
				<tr>
					<td class="listlr"><?=htmlspecialchars($disk['name']);?></td>
					<td class="listr"><?=htmlspecialchars($disk['size']);?></td>
					<td class="listr"><?=htmlspecialchars($disk['desc']);?>&nbsp;</td>
					<td class="listr"><?=system_get_volume_serial($disk['devicespecialfile']);?>&nbsp;</td>
					<td class="listr"><?=$iostat;?>&nbsp;</td>
					<td class="listr"><?=$temp;?>&nbsp;</td>
					<td class="listbg"><?=(0 == disks_exists($disk['devicespecialfile'])) ? gettext("ONLINE") : gettext("MISSING");?>&nbsp;</td>
				</tr>
				<?php endforeach; ?>
				<?php if (isset($raidstatus)):?>
				<?php foreach ($raidstatus as $diskk => $diskv):?>
				<?php (($iostat = system_get_device_iostat($diskk)) === FALSE) ? $iostat = gettext("n/a") : $iostat = sprintf("%s KiB/t, %s tps, %s MiB/s", $iostat['kpt'], $iostat['tps'], $iostat['mps']);?>
				<?php (($temp = system_get_device_temp($diskk)) === FALSE) ? $temp = gettext("n/a") : $temp = sprintf("%s &deg;C", $temp);?>
				<tr>
					<td class="listlr"><?=htmlspecialchars($diskk);?></td>
					<td class="listr"><?=htmlspecialchars($diskv['size']);?></td>
					<td class="listr"><?=htmlspecialchars(gettext("Software RAID"));?>&nbsp;</td>
					<td class="listr"><?=htmlspecialchars(gettext("n/a"));?>&nbsp;</td>
					<td class="listr"><?=htmlspecialchars($iostat);?>&nbsp;</td>
					<td class="listr"><?=htmlspecialchars($temp);?>&nbsp;</td>
					<td class="listbg"><?=htmlspecialchars($diskv['state']);?>&nbsp;</td>
				</tr>
				<?php endforeach;?>
				<?php endif;?>
			</table>
			</td>
		</tr>
	</table>
<?php include("fend.inc");?>
