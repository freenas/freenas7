#!/usr/local/bin/php
<?php
/*
	status_disks.php
	part of FreeNAS (http://www.freenas.org)
	Copyright (C) 2005-2006 Olivier Cochard <olivier@freenas.org>.
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

$pgtitle = array(_STATSDISKS_NAME, _STATSDISKS_NAMEDESC);

if (!is_array($config['disks']['disk']))
	$config['disks']['disk'] = array();
	
disks_sort();

$raidstatus=get_sraid_disks_list();

$a_disk_conf = &$config['disks']['disk'];
?>
<?php include("fbegin.inc"); ?>
              <table width="100%" border="0" cellpadding="0" cellspacing="0">
                <tr>
                  <td width="5%" class="listhdrr"><?=_STATSDISKS_DISK;?></td>
                  <td width="5%" class="listhdrr"><?=_STATSDISKS_SIZE;?></td>
                  <td width="60%" class="listhdrr"><?=_STATSDISKS_DESC;?></td>
                  <td width="10%" class="listhdr"><?=_STATSDISKS_STATUS;?></td>
				</tr>
			  <?php foreach ($a_disk_conf as $disk): ?>
                <tr>
                  <td class="listbg">
                    <?=htmlspecialchars($disk['name']);?>
                  </td>
                  <td class="listbg">
                    <?=htmlspecialchars($disk['size']);?>
                  </td>
                  <td class="listbg">
                    <?=htmlspecialchars($disk['desc']);?>&nbsp;
                  </td>
                   <td class="listbg">
                    <?php
                    $stat=disks_status($disk);
                    echo $stat;?>&nbsp;
                  </td>
				</tr>
			  <?php endforeach; ?>
			  <?php if (isset($raidstatus)): ?>
				<?php foreach ($raidstatus as $diskk => $diskv): ?>
                <tr>
                  <td class="listbg">
                    <?=htmlspecialchars($diskk);?>
                  </td>
                  <td class="listbg">
                    <?=htmlspecialchars($diskv['size']);?>
                  </td>
                  <td class="listbg">
                  
                   <?=htmlspecialchars("Software RAID volume");?>&nbsp;
                  </td>
                   <td class="listbg">
                      <?=htmlspecialchars($diskv['desc']);?>&nbsp;
                  </td>
				</tr>
				<?php endforeach; ?>
			  <?php endif; ?>
              </table>
<?php include("fend.inc"); ?>
