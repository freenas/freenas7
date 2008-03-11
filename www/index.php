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

$pgtitle = array(get_product_name()." webGUI");
$pgtitle_omit = true;
?>
<?php include("fbegin.inc");?>
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
			      <strong><?=get_product_version();?></strong> (revision <?=get_product_revision();?>)</br>
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
			      <?php exec("/bin/date", $date); echo htmlspecialchars($date[0]); ?>
			    </td>
			  </tr>
			  <tr>
			    <td width="25%" class="vncellt"><?=gettext("Uptime");?></td>
			    <td width="75%" class="listr">
						<?=htmlspecialchars(system_get_uptime());?>
			    </td>
			  </tr>
			  <?php if ($config['lastchange']): ?>
			    <tr>
			      <td width="25%" class="vncellt"><?=gettext("Last config change");?></td>
			      <td width="75%" class="listr">
			        <?=htmlspecialchars(date("D M j G:i:s T Y", $config['lastchange']));?>
			      </td>
			    </tr>
			  <?php endif; ?>
			  <tr>
			    <td width="25%" class="vncellt"><?=gettext("Memory usage");?></td>
			    <td width="75%" class="listr">
			      <?php
							$raminfo = system_get_ram_info();
			        $memUsage = round(($raminfo['used'] * 100) / $raminfo['total'], 0);
			
			        echo "<img src='bar_left.gif' height='15' width='4' border='0' align='absmiddle'>";
			        echo "<img src='bar_blue.gif' height='15' width='" . $memUsage . "' border='0' align='absmiddle'>";
			        echo "<img src='bar_gray.gif' height='15' width='" . (100 - $memUsage) . "' border='0' align='absmiddle'>";
			        echo "<img src='bar_right.gif' height='15' width='5' border='0' align='absmiddle'> ";
			        echo sprintf(gettext("%d%% of %dMB"), $memUsage, round($raminfo['physical'] / 1024 / 1024));
			      ?>
			    </td>
			  </tr>
				<tr>
			  	<td width="25%" class="vncellt"><?=gettext("Load averages");?></td>
					<td width="75%" class="listr">
						<?
						exec("uptime", $result); echo substr(strrchr($result[0], "load averages:"),15)." <small>[<a href='status_process.php'>".gettext("show process information")."</a></small>]";
						?>
			    </td>
			  </tr>
				<tr>
			    <td width="25%" class="vncellt"><?=gettext("Disk space usage");?></td>
			    <td width="75%" class="listr">
				    <table>
				      <?php
				      $diskuse = get_mount_usage();
				      if (is_array($diskuse) && (0 < count($diskuse))) {
								foreach ($diskuse as $diskusek => $diskusev) {
									echo "<tr><td>";
									$index = array_search_ex($diskusev['filesystem'], $config['mounts']['mount'], "devicespecialfile");
									echo htmlspecialchars($config['mounts']['mount'][$index]['desc']);
									echo "</td><td>";
									$percent_used = rtrim($diskusev['capacity'],"%");
			
									$tooltip_used = sprintf(gettext("%sB used of %sB"), $diskusev['used'], $diskusev['size']);
									$tooltip_available = sprintf(gettext("%sB available of %sB"), $diskusev['avail'], $diskusev['size']);
			
									echo "<img src='bar_left.gif' height='15' width='4' border='0' align='absmiddle'>";
									echo "<img src='bar_blue.gif' height='15' width='" . $percent_used . "' border='0' align='absmiddle' title='" . $tooltip_used . "'>";
									echo "<img src='bar_gray.gif' height='15' width='" . (100 - $percent_used) . "' border='0' align='absmiddle' title='" . $tooltip_available . "'>";
									echo "<img src='bar_right.gif' height='15' width='5' border='0' align='absmiddle'> ";
									echo sprintf(gettext("%s of %sB"), $diskusev['capacity'], $diskusev['size']);
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
