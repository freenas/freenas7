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
$menu['advanced']['expandable'] = true;
$menu['advanced']['is_expanded'] = strstr($_SERVER['SCRIPT_FILENAME'], "exec") || strstr($_SERVER['SCRIPT_FILENAME'], "edit");

// Diagnostics
$menu['diagnostics']['desc'] = gettext("Diagnostics");
$menu['diagnostics']['menuitem'] = array();
$menu['diagnostics']['menuitem'][] = array("desc" => gettext("Logs"), "link" => "diag_logs.php", "visible" => true);
$menu['diagnostics']['menuitem'][] = array("desc" => gettext("Information"), "link" => "diag_infos.php", "visible" => true);
$menu['diagnostics']['menuitem'][] = array("desc" => gettext("Ping/Traceroute"), "link" => "diag_ping.php", "visible" => true);
$menu['diagnostics']['menuitem'][] = array("desc" => gettext("ARP Tables"), "link" => "diag_arp.php", "visible" => true);
$menu['diagnostics']['menuitem'][] = array("desc" => gettext("Routes"), "link" => "diag_routes.php", "visible" => true);
$menu['diagnostics']['expandable'] = true;
$menu['diagnostics']['is_expanded'] = isset($config['system']['webgui']['expanddiags']) || strstr($_SERVER['SCRIPT_FILENAME'], "diag_");

function display_menu($menu) {
	# Is menu expandable?
	if ($menu['expandable']) {
		$span_id = "span".rand(); # Create unique id.
		$icon_id = "icon".rand(); # Create unique id.
		if (true === $menu['is_expanded']) {
			echo "<li><h1><a href=\"javascript:showhideMenu('{$span_id}','{$icon_id}')\"><img src='/tri_o.gif' id='{$icon_id}' width='14' height='10' border='0'></a><a href=\"javascript:showhideMenu('{$span_id}','{$icon_id}')\">".gettext($menu['desc'])."</a></h1>\n";
			echo "<span id='{$span_id}'>\n";
		} else {
			echo "<li><h1><a href=\"javascript:showhideMenu('{$span_id}','{$icon_id}')\"><img src='/tri_c.gif' id='{$icon_id}' width='14' height='10' border='0'></a><a href=\"javascript:showhideMenu('{$span_id}','{$icon_id}')\">".gettext($menu['desc'])."</a></h1>\n";
			echo "<span id='{$span_id}' style='display: none'>\n";
		}
	}	else {
		# Display menu section description only.
		echo "<li><h1>{$menu['desc']}</h1>\n";
	}

	# Open new navigation layer.
	echo "<ul>\n";

	# Display menu items.
	foreach( $menu['menuitem'] as $menuk => $menuv) {
		# Is menu item visible?
		if (!$menuv['visible']) {
			continue;
		}
		# Display menuitem.
		echo "<li><a href='{$menuv['link']}' title='{$menuv['desc']}'>{$menuv['desc']}</a></li>\n";
	}

	# Close navigation layer.
	echo "</ul></li>\n";

	# Is menu expandable?
	if ($menu['expandable']) {
		echo "</span>\n";
	}
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
	<script type="text/javascript" src="gui.js"></script>
	<script type="text/javascript" src="niftycube/niftycube.js"></script>
	<script type="text/javascript" src="niftycube/niftylayout.js"></script>
</head>
<body>
<div id="topmasterhead"></div>
<div id="topnavigation">
	<div id="topnavigation-content">
		<table border="0" cellspacing="0" cellpadding="0" height="100%" width="100%">
			<tr>
				<td align="left" >
					<img src="/header_logo.gif" border="0">
				</td>
				<td align="right" valign="bottom">
					<span class="hostname"><?=$config['system']['hostname'] . "." . $config['system']['domain'];?>&nbsp;</span>
				</td>
			</tr>
		</table>
	</div>
</div>
<div id="topborder"></div>
<div id="content">
	<table width="100%" border="0" cellspacing="0" cellpadding="2">
	  <tr valign="top">
	    <td width="150" class="pgnavbar">
				<table border="0" cellpadding="6" cellspacing="0">
	        <tr>
	          <td>
	          	<ul id="navigation">
								<?=display_menu($menu['system']);?>
				  			<?=display_menu($menu['interfaces']);?>
								<?=display_menu($menu['disks']);?>
								<?=display_menu($menu['services']);?>
								<?=display_menu($menu['access']);?>
								<?=display_menu($menu['status']);?>
								<?php
								/* extensions section */
								if (is_dir("{$g['www_path']}/ext")):
								?>
								<li><h1><?=gettext("Extensions");?></h1>
								<ul>
									<?php $dh = @opendir("{$g['www_path']}/ext");
									if ($dh) {
										while (($extd = readdir($dh)) !== false) {
											if (($extd === ".") || ($extd === ".."))
												continue;
											@include("{$g['www_path']}/ext/" . $extd . "/menu.inc");
										}
										closedir($dh);
									}?>
								</ul>
								</li>
								<?php endif;?>
								<?=display_menu($menu['diagnostics']);?>
								<?=display_menu($menu['advanced']);?>
				  		</ul>
						</td>
	        </tr>
				</table>
			</td>
	    <td>
				<table width="100%" border="0" cellpadding="10" cellspacing="0">
	        <tr>
						<td>
							<?php if (!$pgtitle_omit): ?>
	      			<p class="pgtitle"><?=gentitle($pgtitle);?></p>
							<?php endif;?>
