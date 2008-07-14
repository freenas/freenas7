<?php
function gentitle($title) {
	$navlevelsep = ": "; // Navigation level separator string.
	return join($navlevelsep, $title);
}

function genhtmltitle($title) {
	global $config;
	return $config['system']['hostname'] . "." . $config['system']['domain'] . " - " . gentitle($title);
}

// Menu items.
// System
$menu['system']['desc'] = gettext("System");
$menu['system']['menuitem'] = array();
$menu['system']['menuitem'][] = array("desc" => gettext("General"), "link" => "system.php", "visible" => true);
$menu['system']['menuitem'][] = array("desc" => gettext("Advanced"), "link" => "system_advanced.php", "visible" => true);
$menu['system']['menuitem'][] = array("desc" => gettext("Static Routes"), "link" => "system_routes.php", "visible" => true);
$menu['system']['menuitem'][] = array("desc" => gettext("Hosts"), "link" => "system_hosts.php", "visible" => true);
if ("full" === $g['platform']) {
	$menu['system']['menuitem'][] = array("desc" => "Packages", "link" => "system_packages.php", "visible" => true);
}
$menu['system']['menuitem'][] = array("desc" => gettext("Firmware"), "link" => "system_firmware.php", "visible" => true);
$menu['system']['menuitem'][] = array("desc" => gettext("Backup/Restore"), "link" => "system_backup.php", "visible" => true);
$menu['system']['menuitem'][] = array("desc" => gettext("Factory defaults"), "link" => "system_defaults.php", "visible" => true);
$menu['system']['menuitem'][] = array("desc" => gettext("Reboot"), "link" => "reboot.php", "visible" => true);
$menu['system']['menuitem'][] = array("desc" => gettext("Shutdown"), "link" => "shutdown.php", "visible" => true);

// Interfaces
$menu['interfaces']['desc'] = gettext("Interfaces");
$menu['interfaces']['menuitem'] = array();
if (!isset($config['system']['webgui']['noassigninterfaces'])) {
	$menu['interfaces']['menuitem'][] = array("desc" => "Management", "link" => "interfaces_assign.php", "visible" => true);
}
$menu['interfaces']['menuitem'][] = array("desc" => "LAN", "link" => "interfaces_lan.php", "visible" => true);
for ($i = 1; isset($config['interfaces']['opt' . $i]); $i++) {
	$desc = $config['interfaces']['opt'.$i]['descr'];
	$menu['interfaces']['menuitem'][] = array("desc" => "{$desc}", "link" => "interfaces_opt.php?index={$i}", "visible" => true);
}

// Disks
$menu['disks']['desc'] = gettext("Disks");
$menu['disks']['menuitem'] = array();
$menu['disks']['menuitem'][] = array("desc" => gettext("Management"), "link" => "disks_manage.php", "visible" => true);
$menu['disks']['menuitem'][] = array("desc" => gettext("Software RAID"), "link" => "disks_raid_gmirror.php", "visible" => true);
$menu['disks']['menuitem'][] = array("desc" => gettext("Encryption"), "link" => "disks_crypt.php", "visible" => true);
$menu['disks']['menuitem'][] = array("desc" => gettext("ZFS"), "link" => "disks_zfs_zpool.php", "visible" => true);
$menu['disks']['menuitem'][] = array("desc" => gettext("Format"), "link" => "disks_init.php", "visible" => true);
$menu['disks']['menuitem'][] = array("desc" => gettext("Mount Point"), "link" => "disks_mount.php", "visible" => true);

// Services
$menu['services']['desc'] = gettext("Services");
$menu['services']['menuitem'] = array();
$menu['services']['menuitem'][] = array("desc" => gettext("CIFS/SMB"), "link" => "services_samba.php", "visible" => true);
$menu['services']['menuitem'][] = array("desc" => gettext("FTP"), "link" => "services_ftp.php", "visible" => true);
$menu['services']['menuitem'][] = array("desc" => gettext("SSH"), "link" => "services_sshd.php", "visible" => true);
$menu['services']['menuitem'][] = array("desc" => gettext("NFS"), "link" => "services_nfs.php", "visible" => true);
$menu['services']['menuitem'][] = array("desc" => gettext("AFP"), "link" => "services_afp.php", "visible" => true);
$menu['services']['menuitem'][] = array("desc" => gettext("RSYNC"), "link" => "services_rsyncd.php", "visible" => true);
$menu['services']['menuitem'][] = array("desc" => gettext("Unison"), "link" => "services_unison.php", "visible" => true);
$menu['services']['menuitem'][] = array("desc" => gettext("iSCSI Target"), "link" => "services_iscsitarget.php", "visible" => true);
$menu['services']['menuitem'][] = array("desc" => gettext("UPnP"), "link" => "services_upnp.php", "visible" => true);
$menu['services']['menuitem'][] = array("desc" => gettext("DAAP"), "link" => "services_daap.php", "visible" => true);
$menu['services']['menuitem'][] = array("desc" => gettext("Dynamic DNS"), "link" => "services_dynamicdns.php", "visible" => true);
$menu['services']['menuitem'][] = array("desc" => gettext("SNMP"), "link" => "services_snmp.php", "visible" => true);
$menu['services']['menuitem'][] = array("desc" => gettext("Webserver"), "link" => "services_websrv.php", "visible" => true);

// Access
$menu['access']['desc'] = gettext("Access");
$menu['access']['menuitem'] = array();
$menu['access']['menuitem'][] = array("desc" => gettext("Users and Groups"), "link" => "access_users.php", "visible" => true);
$menu['access']['menuitem'][] = array("desc" => gettext("Active Directory"), "link" => "access_ad.php", "visible" => true);
$menu['access']['menuitem'][] = array("desc" => gettext("LDAP"), "link" => "access_ldap.php", "visible" => true);
$menu['access']['menuitem'][] = array("desc" => gettext("NIS"), "link" => "notavailable.php", "visible" => false);

// Status
$menu['status']['desc'] = gettext("Status");
$menu['status']['menuitem'] = array();
$menu['status']['menuitem'][] = array("desc" => gettext("System"), "link" => "index.php", "visible" => true);
$menu['status']['menuitem'][] = array("desc" => gettext("Process"), "link" => "status_process.php", "visible" => true);
$menu['status']['menuitem'][] = array("desc" => gettext("Interfaces"), "link" => "status_interfaces.php", "visible" => true);
$menu['status']['menuitem'][] = array("desc" => gettext("Disks"), "link" => "status_disks.php", "visible" => true);
$menu['status']['menuitem'][] = array("desc" => gettext("Wireless"), "link" => "status_wireless.php", "visible" => true);
$menu['status']['menuitem'][] = array("desc" => gettext("Graph"), "link" => "status_graph.php", "visible" => true);
$menu['status']['menuitem'][] = array("desc" => gettext("Email Report"), "link" => "status_report.php", "visible" => true);

// Advanced
$menu['advanced']['desc'] = gettext("Advanced");
$menu['advanced']['menuitem'] = array();
$menu['advanced']['menuitem'][] = array("desc" => gettext("Command"), "link" => "exec.php", "visible" => true);
$menu['advanced']['menuitem'][] = array("desc" => gettext("Edit File"), "link" => "system_edit.php", "visible" => true);

// Diagnostics
$menu['diagnostics']['desc'] = gettext("Diagnostics");
$menu['diagnostics']['menuitem'] = array();
$menu['diagnostics']['menuitem'][] = array("desc" => gettext("Logs"), "link" => "diag_logs.php", "visible" => true);
$menu['diagnostics']['menuitem'][] = array("desc" => gettext("Information"), "link" => "diag_infos.php", "visible" => true);
$menu['diagnostics']['menuitem'][] = array("desc" => gettext("Ping/Traceroute"), "link" => "diag_ping.php", "visible" => true);
$menu['diagnostics']['menuitem'][] = array("desc" => gettext("ARP Tables"), "link" => "diag_arp.php", "visible" => true);
$menu['diagnostics']['menuitem'][] = array("desc" => gettext("Routes"), "link" => "diag_routes.php", "visible" => true);

function display_menu($menuid) {
	global $menu;

	echo "<li>\n";
	echo "	<a href=\"\" onmouseover=\"mopen('{$menuid}')\" onmouseout=\"mclosetime()\">{$menu[$menuid]['desc']}</a>\n";
	echo "	<div id=\"{$menuid}\" onmouseover=\"mcancelclosetime()\" onmouseout=\"mclosetime()\">\n";

	# Display menu items.
	foreach ($menu[$menuid]['menuitem'] as $menuk => $menuv) {
		# Is menu item visible?
		if (!$menuv['visible']) {
			continue;
		}
		# Display menuitem.
		echo "<a href=\"{$menuv['link']}\" title=\"{$menuv['desc']}\">{$menuv['desc']}</a>\n";
	}

	echo "	</div>\n";
	echo "</li>\n";
}
?>
<html>
<head>
	<title><?=genhtmltitle($pgtitle);?></title>
	<meta http-equiv="Content-Type" content="text/html; charset=<?=system_get_language_codeset();?>">
	<?php if ($pgrefresh):?>
	<meta http-equiv="refresh" content="<?=$pgrefresh;?>">
	<?php endif;?>
	<link href="gui.css" rel="stylesheet" type="text/css">
	<link href="navbar.css" rel="stylesheet" type="text/css">
	<script type="text/javascript" src="javascript/gui.js"></script>
	<script type="text/javascript" src="javascript/navbar.js"></script>
	<script type="text/javascript" src="niftycube/niftycube.js"></script>
	<script type="text/javascript" src="niftycube/niftylayout.js"></script>
</head>
<body>
<div id="headerborder"></div>
<div id="headerlogo">
	<table border="0" cellspacing="0" cellpadding="0" width="100%">
		<tr>
			<td align="left">
				<img src="/header_logo.gif">
			</td>
			<td align="right" valign="bottom">
				<span class="hostname"><?=$config['system']['hostname'] . "." . $config['system']['domain'];?>&nbsp;</span>
			</td>
		</tr>
	</table>
</div>
<div id="smallheaderborder"></div>
<div id="headernavbar">
	<ul id="navbarmenu">
		<?=display_menu("system");?>
		<?=display_menu("interfaces");?>
		<?=display_menu("disks");?>
		<?=display_menu("services");?>
		<?=display_menu("access");?>
		<?=display_menu("status");?>
		<?=display_menu("diagnostics");?>
		<?=display_menu("advanced");?>
	</ul>
	<div style="clear:both"></div>
</div>
<div id="smallheaderborder"></div>
<div id="content">
	<table width="100%" border="0" cellspacing="0" cellpadding="2">
	  <tr valign="top">
	    <td>
				<table width="100%" border="0" cellpadding="10" cellspacing="0">
	        <tr>
						<td>
							<?php if (!$pgtitle_omit): ?>
	      			<p class="pgtitle"><?=gentitle($pgtitle);?></p>
							<?php endif;?>
