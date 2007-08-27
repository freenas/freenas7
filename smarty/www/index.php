<?php
$pgtitle = array("FreeNAS webGUI");

require_once("guicore.inc");
require_once("util.inc");

$webgui->assign("page_title", "");
$webgui->assign("hostname", "{$config['system']['hostname']}.{$config['system']['domain']}");
$webgui->assign("version", get_product_version());
$webgui->assign("buildtime", get_product_buildtime());

exec("/sbin/sysctl -n kern.ostype", $ostype);
exec("/sbin/sysctl -n kern.osrelease", $osrelease);
exec("/sbin/sysctl -n kern.osrevision", $osrevision);
$webgui->assign("osversion", "$ostype[0] $osrelease[0] (revison $osrevision[0])");

$platform = htmlspecialchars($g['fullplatform']);
exec("/sbin/sysctl -n hw.model", $cputype);
foreach ($cputype as $cputypel) $platform .= " on " . htmlspecialchars($cputypel);
exec("/sbin/sysctl -n hw.clockrate", $clockrate);
$platform .= " running at $clockrate[0] MHz";
$webgui->assign("platform", $platform);

exec("/sbin/sysctl -n kern.boottime", $boottime);
preg_match("/sec = (\d+)/", $boottime[0], $matches);
$boottime = $matches[1];
$uptime = time() - $boottime;
if ($uptime > 60)$uptime += 30;
$updays = (int)($uptime / 86400);
$uptime %= 86400;
$uphours = (int)($uptime / 3600);
$uptime %= 3600;
$upmins = (int)($uptime / 60);
$uptimestr = "";
if ($updays > 1)
	$uptimestr .= "$updays ".gettext("days").", ";
else if ($updays > 0)
	$uptimestr .= "1 ".gettext("day").", ";
$uptimestr .= sprintf("%02d:%02d", $uphours, $upmins);
$webgui->assign("uptime", htmlspecialchars($uptimestr));

if ($config['lastchange']) {
	$webgui->assign("lastchange", htmlspecialchars(date("D M j G:i:s T Y", $config['lastchange'])));
}

/* Get RAM informations. */
$raminfo = get_ram_info();
$memusage['percentage'] = round(($raminfo['used'] * 100) / $raminfo['total'], 0);
$memusage['caption'] = $memusage['percentage'] . "% of " . round($raminfo['physical'] / 1024 / 1024) . "MB";
$webgui->assign("memusage", $memusage);

exec("uptime", $result);
$webgui->assign("loadaverages", substr(strrchr($result[0], "load averages:"),15)." <SMALL>[<A href='status_process.php'>".gettext("show process information")."</A></SMALL>]");

$diskuse = get_mount_use();
$diskusage = array();
if (is_array($diskuse)) {
	foreach ($diskuse as $diskusek => $diskusev) {
		$percent_used = rtrim($diskusev['capacity'],"%");
		$disk['name'] = htmlspecialchars($diskusek);
		$disk['percentage'] = intval(rtrim($diskusev['capacity'],"%"));
		$disk['caption'] = $disk['percentage'] . "% of " . $diskusev['size'] . "B";
		$diskusage[] = $disk;
	}
}
$webgui->assign("diskusage", $diskusage);

$webgui->display('index.tpl');
?>
