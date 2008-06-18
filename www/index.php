#!/usr/local/bin/php
<?php
/*
  index.php
  part of FreeNAS (http://www.freenas.org)
  Copyright (C) 2005-2008 Olivier Cochard-Labbe <olivier@freenas.org>.
  All rights reserved.
  Improved by Stefan Hendricks (info@henmedia.de)

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
require("sajax/sajax.php");

$pgtitle = array(get_product_name()." webGUI");
$pgtitle_omit = true;

function update_controls() {
	// Get uptime and date.
	$value['uptime'] = system_get_uptime();
	exec("/bin/date", $value['date']);
	// Get RAM usage.
	$raminfo = system_get_ram_info();
	$percentage = round(($raminfo['used'] * 100) / $raminfo['total'], 0);
	$value['memusage']['percentage'] = $percentage;
	$value['memusage']['caption'] = sprintf(gettext("%d%% of %dMB"), $percentage, round($raminfo['physical'] / 1024 / 1024));
	// Get load average.
	exec("uptime", $result);
	$value['loadaverage'] = substr(strrchr($result[0], "load averages:"), 15);
	// Get CPU usage.
	$value['cpuusage'] = system_get_cpu_usage();
	// Get disk usage.
	$a_diskusage = get_mount_usage();
	if (is_array($a_diskusage) && (0 < count($a_diskusage))) {
		foreach ($a_diskusage as $diskusagek => $diskusagev) {
			$fsid = get_mount_fsid($diskusagev['filesystem'], $diskusagek);
			$diskinfo = array();
			$diskinfo['id'] = $fsid;
			$diskinfo['percentage'] = rtrim($diskusagev['capacity'],"%");
			$diskinfo['tooltip']['used'] = sprintf(gettext("%sB used of %sB"), $diskusagev['used'], $diskusagev['size']);
			$diskinfo['tooltip']['available'] = sprintf(gettext("%sB available of %sB"), $diskusagev['avail'], $diskusagev['size']);
			$diskinfo['caption'] = sprintf(gettext("%s of %sB"), $diskusagev['capacity'], $diskusagev['size']);
			$value['diskusage'][] = $diskinfo;
		}
	}
	// Get swap info.
	$swapinfo = system_get_swap_info();
	if (is_array($swapinfo) && (0 < count($swapinfo))) {
		$id = 0;
		foreach ($swapinfo as $swap) {
			$id++;
			$devswap = array();
			$devswap['id']  = $id;
			$devswap['percentage']  = rtrim($swap['capacity'],"%");
			$devswap['tooltip']['used'] = sprintf(gettext("%s used of %sB"), $swap['used'], $swap['total']);
			$devswap['tooltip']['available']  = sprintf(gettext("%s available of %sB"), $swap['avail'], $swap['total']);
			$devswap['caption'] = sprintf(gettext("%s of %sB"), $swap['capacity'], $swap['total']);
			$value['swapusage'][]= $devswap;
		}
	}
	// Encode data using JSON.
	$value = json_encode($value);
	return $value;
}

sajax_init();
sajax_export("update_controls");
sajax_handle_client_request();
?>
<?php include("fbegin.inc");?>
<script>
<?php sajax_show_javascript();?>
</script>
<script type="text/javascript" src="javascript/index.js"></script>
<table width="100%" border="0" cellspacing="0" cellpadding="0">
  <tr align="center" valign="top">
    <td height="10" colspan="2">&nbsp;</td>
  </tr>
  <tr align="center" valign="top">
    <td height="170" colspan="2"><img src="logobig.gif" width="520" height="149"></td>
  </tr>
</table>
<table width="100%" border="0" cellpadding="0" cellspacing="0">
  <tr>
    <td class="tabcont">
    	<table width="100%" border="0" cellspacing="0" cellpadding="0">
 			  <tr>
			    <td colspan="2" class="listtopic"><?=gettext("System information");?></td>
			  </tr>
			  <tr>
			    <td width="25%" class="vncellt"><?=gettext("Name");?></td>
			    <td width="75%" class="listr">
			      <?php echo $config['system']['hostname'] . "." . $config['system']['domain']; ?>
			    </td>
			  </tr>
			  <tr>
			    <td width="25%" valign="top" class="vncellt"><?=gettext("Version");?></td>
			    <td width="75%" class="listr">
			      <strong><?=get_product_version();?></strong> <?=get_product_versionname();?> (revision <?=get_product_revision();?>)</br>
						<?=gettext("built on");?> <?=get_product_buildtime();?>
			    </td>
			  </tr>
			  <tr>
			    <td width="25%" valign="top" class="vncellt"><?=gettext("OS Version");?></td>
			    <td width="75%" class="listr">
			      <?
			        exec("/sbin/sysctl -n kern.ostype", $ostype);
			        exec("/sbin/sysctl -n kern.osrelease", $osrelease);
			        exec("/sbin/sysctl -n kern.osrevision", $osrevision);
			        echo("$ostype[0] $osrelease[0] (revision $osrevision[0])");
			      ?>
			    </td>
			  </tr>
			  <tr>
			    <td width="25%" class="vncellt"><?=gettext("Platform");?></td>
			    <td width="75%" class="listr">
			      <?=htmlspecialchars($g['fullplatform']);
			      exec("/sbin/sysctl -n hw.model", $cputype);
			      foreach ($cputype as $cputypel) echo " on " . htmlspecialchars($cputypel);
			      exec("/sbin/sysctl -n hw.clockrate", $clockrate);
			      echo " running at $clockrate[0] MHz";
			      ?>
			    </td>
			  </tr>
			  <tr>
			    <td width="25%" class="vncellt"><?=gettext("Date");?></td>
			    <td width="75%" class="listr">
			      <?php exec("/bin/date", $date);?>
			      <input style="padding: 0; border: 0;" size="30" name="date" id="date" value="<?=htmlspecialchars($date[0]);?>"/>
			    </td>
			  </tr>
			  <tr>
			    <td width="25%" class="vncellt"><?=gettext("Uptime");?></td>
			    <td width="75%" class="listr">
						<?php $uptime = system_get_uptime();?>
						<input style="padding: 0; border: 0;" size="30" name="uptime" id="uptime" value="<?=htmlspecialchars($uptime);?>"/>
			    </td>
			  </tr>
			  <?php if ($config['lastchange']):?>
			    <tr>
			      <td width="25%" class="vncellt"><?=gettext("Last config change");?></td>
			      <td width="75%" class="listr">
							<input style="padding: 0; border: 0;" size="30" name="lastchange" id="lastchange" value="<?=htmlspecialchars(date("D M j G:i:s T Y", $config['lastchange']));?>"/>
			      </td>
			    </tr>
			  <?php endif;?>
				<tr>
					<td width="25%" class="vncellt"><?=gettext("CPU usage");?></td>
					<td width="75%" class="listr">
						<?php
						$percentage = 0;
						echo "<img src='bar_left.gif' height='15' width='4' border='0' align='absmiddle'>";
						echo "<img src='bar_blue.gif' name='cpuusageu' id='cpuusageu' height='15' width='" . $percentage . "' border='0' align='absmiddle'>";
						echo "<img src='bar_gray.gif' name='cpuusagef' id='cpuusagef' height='15' width='" . (100 - $percentage) . "' border='0' align='absmiddle'>";
						echo "<img src='bar_right.gif' height='15' width='5' border='0' align='absmiddle'> ";
						?>
						<input style="padding: 0; border: 0;" size="30" name="cpuusage" id="cpuusage" value="<?=gettext("Updating in 5 seconds.");?>"/>
					</td>
				</tr>
			  <tr>
			    <td width="25%" class="vncellt"><?=gettext("Memory usage");?></td>
			    <td width="75%" class="listr">
						<?php
						$raminfo = system_get_ram_info();
						$percentage = round(($raminfo['used'] * 100) / $raminfo['total'], 0);
						echo "<img src='bar_left.gif' height='15' width='4' border='0' align='absmiddle'>";
						echo "<img src='bar_blue.gif' name='memusageu' id='memusageu' height='15' width='" . $percentage . "' border='0' align='absmiddle'>";
						echo "<img src='bar_gray.gif' name='memusagef' id='memusagef' height='15' width='" . (100 - $percentage) . "' border='0' align='absmiddle'>";
						echo "<img src='bar_right.gif' height='15' width='5' border='0' align='absmiddle'> ";
						?>
						<input style="padding: 0; border: 0;" size="30" name="memusage" id="memusage" value="<?=sprintf(gettext("%d%% of %dMB"), $percentage, round($raminfo['physical'] / 1024 / 1024));?>"/>
			    </td>
			  </tr>
				<?php $swapinfo = system_get_swap_info(); if (is_array($swapinfo) && (0 < count($swapinfo))):?>
				<tr>
					<td width="25%" class="vncellt"><?=gettext("Swap usage");?></td>
					<td width="75%" class="listr">
						<table width="100%" border="0" cellspacing="0" cellpadding="1">
							<?php
							$fsid = 0;
							foreach ($swapinfo as $swap) {
								echo "<tr><td>";
								echo htmlspecialchars($swap['device']);
								echo "</td><td>";

								$fsid++;
								$percent_used = rtrim($swap['capacity'],"%");
								$tooltip_used = sprintf(gettext("%sB used of %sB"), $swap['used'], $swap['total']);
								$tooltip_available = sprintf(gettext("%sB available of %sB"), $swap['avail'], $swap['total']);

								echo "<img src='bar_left.gif' height='15' width='4' border='0' align='absmiddle'>";
								echo "<img src='bar_blue.gif' name='swapusageu_{$fsid}' id='swapusageu_{$fsid}' height='15' width='{$percent_used}' border='0' align='absmiddle' title='{$tooltip_used}'>";
								echo "<img src='bar_gray.gif' name='swapusagef_{$fsid}' id='swapusagef_{$fsid}' height='15' width='" . (100 - $percent_used) . "' border='0' align='absmiddle' title='{$tooltip_available}'>";
								echo "<img src='bar_right.gif' height='15' width='5' border='0' align='absmiddle'> ";
								echo "<input style='padding: 0; border: 0;' size='30' name='swapusage_{$fsid}' id='swapusage_{$fsid}' value='" . sprintf(gettext("%s of %sB"), $swap['capacity'], $swap['total']) . "'/>";
								echo "<br></td></tr>";
							}?>
						</table>
					</td>
				</tr>
				<?php endif;?>
				<tr>
			  	<td width="25%" class="vncellt"><?=gettext("Load averages");?></td>
					<td width="75%" class="listr">
						<?php
						exec("uptime", $result);
						$loadaverage = substr(strrchr($result[0], "load averages:"), 15);
						?>
						<input style="padding: 0; border: 0;" size="14" name="loadaverage" id="loadaverage" value="<?=$loadaverage;?>"/>
						<?="<small>[<a href='status_process.php'>".gettext("Show process information")."</a></small>]";?>
			    </td>
			  </tr>
				<tr>
			    <td width="25%" class="vncellt"><?=gettext("Disk space usage");?></td>
			    <td width="75%" class="listr">
				    <table width="100%" border="0" cellspacing="0" cellpadding="1">
				      <?php
				      $a_diskusage = get_mount_usage();
				      $a_mount = get_mounts_list();
				      if (is_array($a_diskusage) && (0 < count($a_diskusage))) {
								foreach ($a_diskusage as $diskusagek => $diskusagev) {
									echo "<tr><td>";
									$index = array_search_ex($diskusagev['filesystem'], $a_mount, "devicespecialfile");
									echo htmlspecialchars($a_mount[$index]['sharename']);
									echo "</td><td>";

									$fsid = get_mount_fsid($diskusagev['filesystem'], $diskusagek);
									$percent_used = rtrim($diskusagev['capacity'],"%");
									$tooltip_used = sprintf(gettext("%sB used of %sB"), $diskusagev['used'], $diskusagev['size']);
									$tooltip_available = sprintf(gettext("%sB available of %sB"), $diskusagev['avail'], $diskusagev['size']);

									echo "<img src='bar_left.gif' height='15' width='4' border='0' align='absmiddle'>";
									echo "<img src='bar_blue.gif' name='diskusageu_{$fsid}' id='diskusageu_{$fsid}' height='15' width='{$percent_used}' border='0' align='absmiddle' title='{$tooltip_used}'>";
									echo "<img src='bar_gray.gif' name='diskusagef_{$fsid}' id='diskusagef_{$fsid}' height='15' width='" . (100 - $percent_used) . "' border='0' align='absmiddle' title='{$tooltip_available}'>";
									echo "<img src='bar_right.gif' height='15' width='5' border='0' align='absmiddle'> ";
									echo "<input style='padding: 0; border: 0;' size='30' name='diskusage_{$fsid}' id='diskusage_{$fsid}' value='" . sprintf(gettext("%s of %sB"), $diskusagev['capacity'], $diskusagev['size']) . "'/>";
									echo "<br></td></tr>";
								}
							} else {
								echo gettext("No disk configured");
							}
							?>
						</table>
					</td>
				</tr>
			</table>
		</td>
	</tr>
</table>
<?php include("fend.inc");?>
