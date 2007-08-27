<?php
$pgtitle = array("FreeNAS webGUI");

require_once("guicore.inc");
require_once("util.inc");

$smarty->assign("hostname", "{$config['system']['hostname']}.{$config['system']['domain']}");
$smarty->assign("version", get_product_version());
$smarty->assign("buildtime", get_product_buildtime());

exec("/sbin/sysctl -n kern.ostype", $ostype);
exec("/sbin/sysctl -n kern.osrelease", $osrelease);
exec("/sbin/sysctl -n kern.osrevision", $osrevision);
$smarty->assign("osversion", "$ostype[0] $osrelease[0] (revison $osrevision[0])");

$platform = htmlspecialchars($g['fullplatform']);
exec("/sbin/sysctl -n hw.model", $cputype);
foreach ($cputype as $cputypel) $platform .= " on " . htmlspecialchars($cputypel);
exec("/sbin/sysctl -n hw.clockrate", $clockrate);
$platform .= " running at $clockrate[0] MHz";
$smarty->assign("platform", $platform);

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
$smarty->assign("uptime", htmlspecialchars($uptimestr));

/* Get RAM informations. */
$raminfo = get_ram_info();
$memusage['percentage'] = round(($raminfo['used'] * 100) / $raminfo['total'], 0);
$memusage['caption'] = $memusage['percentage'] . "% of " . round($raminfo['physical'] / 1024 / 1024) . "MB";
$smarty->assign("memusage", $memusage);

exec("uptime", $result);
$smarty->assign("loadaverages", substr(strrchr($result[0], "load averages:"),15)." <SMALL>[<A href='status_process.php'>".gettext("show process information")."</A></SMALL>]");

$smarty->display('index.tpl');
?>
