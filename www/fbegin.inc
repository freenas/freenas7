<?php
$navlevelsep = ": ";	/* navigation level separator string */
$mandfldhtml = "";		/* display this before mandatory input fields */

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
$menu['system']['menuitem'][] = array("desc" => "Packages", "link" => "system_packages.php", "visible" => true);
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
$menu['services']['menuitem'][] = array("desc" => "CIFS", "link" => "services_samba.php", "visible" => true);
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

/* Shall the diagnostics menu be expanded? */
function diagnostics_is_expanded() {
	global $config;
	return (isset($config['system']['webgui']['expanddiags']) || strstr($_SERVER['SCRIPT_FILENAME'], "diag_") || strstr($_SERVER['SCRIPT_FILENAME'], "reboot") || strstr($_SERVER['SCRIPT_FILENAME'], "shutdown") || strstr($_SERVER['SCRIPT_FILENAME'], "exec")); 
}

function display_menu($menu) {
	# Is menu expandable?
	if ($menu['expandable']) {
		$span_id = "span".rand(); # Create unique id.
		$icon_id = "icon".rand(); # Create unique id.
		if (call_user_func($menu['is_expanded_func'])) {
			echo "<a href=\"javascript:showhideMenu('{$span_id}','{$icon_id}')\"><img src='/tri_o.gif' id='{$icon_id}' width='14' height='10' border='0'></a><strong><a href=\"javascript:showhideMenu('{$span_id}','{$icon_id}')\" class='navlnk'>".gettext($menu['desc'])."</a></strong><br>";
			echo "<span id='{$span_id}'>";
		} else {
			echo "<a href=\"javascript:showhideMenu('{$span_id}','{$icon_id}')\"><img src='/tri_c.gif' id='{$icon_id}' width='14' height='10' border='0'></a><strong><a href=\"javascript:showhideMenu('{$span_id}','{$icon_id}')\" class='navlnk'>".gettext($menu['desc'])."</a></strong><br>";
			echo "<span id='{$span_id}' style='display: none'>";
		}
	}
	else {
		# Display menu section description only.
		echo "<strong>".gettext($menu['desc'])."</strong><br>";
	}

	# Display menu items.
	foreach( $menu['menuitem'] as $menuk => $menuv) {
		# Is menu item visible?
		if (!$menuv['visible']) {
			continue;
		}
		# Display menuitem.
		echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href='".$menuv['link']."' class='navlnk' title='".gettext($menuv['desc'])."'>".gettext($menuv['desc'])."</a><br>";
	}

	# Is menu expandable?
	if ($menu['expandable']) {
		echo "</span>";
	}
}
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
	<head>
	<title><?=genhtmltitle($pgtitle);?></title>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
	<link href="gui.css" rel="stylesheet" type="text/css">
</head>
<body link="#0000CC" vlink="#0000CC" alink="#0000CC">
<script language="javascript">
<!--
function showElementById(id,state) {
	element = document.getElementById(id);
	if ('show' == state) {
		element.style.display = '';
	}
	if ('hide' == state) {
		element.style.display = 'none';
	}
}

function showhideMenu(tspan, tri) {
	tspanel = document.getElementById(tspan);
	triel = document.getElementById(tri);
	if (tspanel.style.display == 'none') {
		tspanel.style.display = '';
		triel.src = "/tri_o.gif";
	} else {
		tspanel.style.display = 'none';
		triel.src = "/tri_c.gif";
	}
}
-->
</script>
<table width="750" border="0" cellspacing="0" cellpadding="2">
  <tr valign="bottom"> 
    <td width="150" height="65" align="center" valign="middle">
			<strong><a href="http://www.<?=get_product_url();?>" target="_blank"><img src="/logo.gif" width="150" height="47" border="0"></a></strong>
		</td>
    <td height="65" bgcolor="#435370">
			<table border="0" cellspacing="0" cellpadding="0" width="100%">
				<tr>
					<td align="left" valign="bottom">
						<span class="tfrtitle">&nbsp;<?=gettext("webGUI Configuration");?></span>
					</td>
			  	<td align="right" valign="bottom">
						<span class="hostname"><?=$config['system']['hostname'] . "." . $config['system']['domain'];?>&nbsp;</span>
					</td>
				</tr>
			</table>
		</td>
	</tr>
  <tr valign="top"> 
    <td width="150" bgcolor="#9D9D9D">
			<table width="100%" border="0" cellpadding="6" cellspacing="0">
        <tr>
          <td><span class="navlnk"><font color="#FFFFFF">
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
						<strong><?=gettext("Extensions");?></strong><br>
						<?php
						$dh = @opendir("{$g['www_path']}/ext");
						if ($dh) {
							while (($extd = readdir($dh)) !== false) {
								if (($extd === ".") || ($extd === ".."))
									continue;
								@include("{$g['www_path']}/ext/" . $extd . "/menu.inc");
							}
							closedir($dh);
						}
						endif;
						?>
						<?=display_menu($menu['diagnostics']);?>
			  	</font></span></td>
        </tr>
			</table>
		</td>
    <td width="600">
			<table width="100%" border="0" cellpadding="10" cellspacing="0">
        <tr>
					<td>
						<?php if (!$pgtitle_omit): ?>
      			<p class="pgtitle"><?=gentitle($pgtitle);?></p>
						<?php endif; ?>
