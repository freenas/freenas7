<?php
require("config.inc");
require("smarty/Smarty.class.php");

global $config;

function gentitle($title) {
	global $navlevelsep;
	return join($navlevelsep, $title);
}

function genhtmltitle($title) {
	global $config;
	return $config['system']['hostname'] . "." . $config['system']['domain'] . " - " . gentitle($title);
}

// Menu items.
// System
$menu['system']['desc'] = "System";
$menu['system']['menuitem'] = array();
$menu['system']['menuitem'][] = array("desc" => "General setup", "link" => "system.php", "visible" => true);
$menu['system']['menuitem'][] = array("desc" => "Static routes", "link" => "system_routes.php", "visible" => true);
$menu['system']['menuitem'][] = array("desc" => "Hosts", "link" => "system_hosts.php", "visible" => true);
$menu['system']['menuitem'][] = array("desc" => "Advanced", "link" => "system_advanced.php", "visible" => true);
if ("full" === $g['platform']) {
	$menu['system']['menuitem'][] = array("desc" => "Packages", "link" => "system_packages.php", "visible" => true);
}
$menu['system']['menuitem'][] = array("desc" => "Firmware", "link" => "system_firmware.php", "visible" => true);

// Interfaces
$menu['interfaces']['desc'] = "Interfaces";
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
$menu['disks']['desc'] = "Disks";
$menu['disks']['menuitem'] = array();
$menu['disks']['menuitem'][] = array("desc" => "Management", "link" => "disks_manage.php", "visible" => true);
$menu['disks']['menuitem'][] = array("desc" => "Software RAID", "link" => "disks_raid_gmirror.php", "visible" => true);
$menu['disks']['menuitem'][] = array("desc" => "Encryption", "link" => "disks_crypt.php", "visible" => true);
$menu['disks']['menuitem'][] = array("desc" => "Format", "link" => "disks_init.php", "visible" => true);
$menu['disks']['menuitem'][] = array("desc" => "Mount Point", "link" => "disks_mount.php", "visible" => true);

// Services
$menu['services']['desc'] = "Services";
$menu['services']['menuitem'] = array();
$menu['services']['menuitem'][] = array("desc" => "CIFS/SMB", "link" => "services_samba.php", "visible" => true);
$menu['services']['menuitem'][] = array("desc" => "FTP", "link" => "services_ftp.php", "visible" => true);
$menu['services']['menuitem'][] = array("desc" => "SSHD", "link" => "services_sshd.php", "visible" => true);
$menu['services']['menuitem'][] = array("desc" => "NFS", "link" => "services_nfs.php", "visible" => true);
$menu['services']['menuitem'][] = array("desc" => "AFP", "link" => "services_afp.php", "visible" => true);
$menu['services']['menuitem'][] = array("desc" => "RSYNCD", "link" => "services_rsyncd.php", "visible" => true);
$menu['services']['menuitem'][] = array("desc" => "Unison", "link" => "services_unison.php", "visible" => true);
$menu['services']['menuitem'][] = array("desc" => "iSCSI Target", "link" => "services_iscsitarget.php", "visible" => true);
$menu['services']['menuitem'][] = array("desc" => "UPnP", "link" => "services_upnp.php", "visible" => true);
$menu['services']['menuitem'][] = array("desc" => "Dynamic DNS", "link" => "services_dynamicdns.php", "visible" => true);

// Access
$menu['access']['desc'] = "Access";
$menu['access']['menuitem'] = array();
$menu['access']['menuitem'][] = array("desc" => "Users and Groups", "link" => "access_users.php", "visible" => true);
$menu['access']['menuitem'][] = array("desc" => "Active Directory", "link" => "access_ad.php", "visible" => true);
$menu['access']['menuitem'][] = array("desc" => "LDAP", "link" => "access_ldap.php", "visible" => true);
$menu['access']['menuitem'][] = array("desc" => "NIS", "link" => "notavailable.php", "visible" => false);

// Status
$menu['status']['desc'] = "Status";
$menu['status']['menuitem'] = array();
$menu['status']['menuitem'][] = array("desc" => "System", "link" => "index.php", "visible" => true);
$menu['status']['menuitem'][] = array("desc" => "Process", "link" => "status_process.php", "visible" => true);
$menu['status']['menuitem'][] = array("desc" => "Interfaces", "link" => "status_interfaces.php", "visible" => true);
$menu['status']['menuitem'][] = array("desc" => "Disks", "link" => "status_disks.php", "visible" => true);
$menu['status']['menuitem'][] = array("desc" => "Wireless", "link" => "status_wireless.php", "visible" => true);
$menu['status']['menuitem'][] = array("desc" => "Graph", "link" => "status_graph.php", "visible" => true);
$menu['status']['menuitem'][] = array("desc" => "Email report", "link" => "status_report.php", "visible" => true);

// Diagnostics
$menu['diagnostics']['desc'] = "Diagnostics";
$menu['diagnostics']['menuitem'] = array();
$menu['diagnostics']['menuitem'][] = array("desc" => "Logs", "link" => "diag_logs.php", "visible" => true);
$menu['diagnostics']['menuitem'][] = array("desc" => "Information", "link" => "diag_infos.php", "visible" => true);
$menu['diagnostics']['menuitem'][] = array("desc" => "Ping/Traceroute", "link" => "diag_ping.php", "visible" => true);
$menu['diagnostics']['menuitem'][] = array("desc" => "ARP table", "link" => "diag_arp.php", "visible" => true);
$menu['diagnostics']['menuitem'][] = array("desc" => "Command", "link" => "exec.php", "visible" => true);
$menu['diagnostics']['menuitem'][] = array("desc" => "Edit File", "link" => "diag_edit.php", "visible" => true);
$menu['diagnostics']['menuitem'][] = array("desc" => "Backup/Restore", "link" => "diag_backup.php", "visible" => true);
$menu['diagnostics']['menuitem'][] = array("desc" => "Factory defaults", "link" => "diag_defaults.php", "visible" => true);
$menu['diagnostics']['menuitem'][] = array("desc" => "Reboot system", "link" => "reboot.php", "visible" => true);
$menu['diagnostics']['menuitem'][] = array("desc" => "Shutdown system", "link" => "shutdown.php", "visible" => true);
$menu['diagnostics']['expandable'] = true;
$menu['diagnostics']['is_expanded_func'] = "diagnostics_is_expanded";

// Generate hostname.
$hostname = "{$config['system']['hostname']}.{$config['system']['domain']}";

$smarty = new Smarty;
$smarty->compile_check = true;
$smarty->force_compile = true;
$smarty->debugging = true;
$smarty->caching = 0;

$smarty->assign("title", genhtmltitle($pgtitle));
$smarty->assign("top_logo_link", "http://www.".get_product_url());
$smarty->assign("top_title", gettext("webGUI Configuration"));
$smarty->assign("top_hostname", $hostname);
$smarty->assign("nav_menu", $menu);
?>
