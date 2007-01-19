<?php
$navlevelsep = ": ";	/* navigation level separator string */
$mandfldhtml = "";		/* display this before mandatory input fields */
$mandfldhtmlspc = "";	/* same as above, but with spacing */

function gentitle($title) {
	global $navlevelsep;
	return join($navlevelsep, $title);
}

function genhtmltitle($title) {
	global $config;
	return $config['system']['hostname'] . "." . $config['system']['domain'] . " - " . gentitle($title);
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
var tri_open = "";
var tri_closed = "";

window.onload = preload;

function preload() {
	if (document.images) {
		tri_open = new Image(14,10);
		tri_closed = new Image(14,10);
		tri_open.src = "/tri_o.gif";
		tri_closed.src = "/tri_c.gif";
	}
}

function showhide(tspan, tri) {
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
    <td width="150" height="65" align="center" valign="middle"> <strong><a href="http://<?=$g['product_url'];?>" target="_blank"><img src="/logo.gif" width="150" height="47" border="0"></a></strong></td>
    <td height="65" bgcolor="#435370">
	<table border="0" cellspacing="0" cellpadding="0" width="100%">
	<tr><td align="left" valign="bottom"><span class="tfrtitle">&nbsp;webGUI 
      Configuration</span></td>
	  <td align="right" valign="bottom">
	  <span class="hostname"><?=$config['system']['hostname'] . "." . $config['system']['domain'];?>&nbsp;</span>
	  </td></tr></table>
	</td>
  </tr>
  <tr valign="top"> 
    <td width="150" bgcolor="#9D9D9D">
	<table width="100%" border="0" cellpadding="6" cellspacing="0">
        <tr>
          <td><span class="navlnk"><font color="#FFFFFF"> <strong><?=gettext("System");?></strong> 
              <br>
              &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href="/system.php" class="navlnk"><?=gettext("General setup");?></a><br>
              &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href="/system_routes.php" class="navlnk"><?=gettext("Static routes");?></a><br>
              &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href="/system_hosts.php" class="navlnk"><?=gettext("Hosts");?></a><br>
              &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href="/system_advanced.php" class="navlnk"><?=gettext("Advanced");?></a><br>
              &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href="/system_firmware.php" class="navlnk"><?=gettext("Firmware");?></a><br>
              <strong><?=gettext("Interfaces");?></strong>
			  <?php if (!isset($config['system']['webgui']['noassigninterfaces'])): ?>
			  <a href="/interfaces_assign.php" class="navlnks">(<?=gettext("Assign");?>)</a>
			  <?php endif; ?>
			  <br>
              &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href="/interfaces_lan.php" class="navlnk"><?=gettext("LAN");?></a><br>
              <?php for ($i = 1; isset($config['interfaces']['opt' . $i]); $i++): ?>
              &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href="/interfaces_opt.php?index=<?=$i;?>" class="navlnk"><?=htmlspecialchars($config['interfaces']['opt' . $i]['descr']);?></a><br>
			  <?php endfor; ?>
              <strong><?=gettext("Disks");?></strong><br>
              &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href="/disks_manage.php" class="navlnk"><?=gettext("Management");?></a><br>
              &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href="/disks_raid_gmirror.php" class="navlnk"><?=gettext("Software RAID");?></a> <br>
              &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href="/disks_init.php" class="navlnk"><?=gettext("Format");?></a><br>
              &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href="/disks_mount.php" class="navlnk"><?=gettext("Mount Point");?></a><br>
 
              <strong><?=gettext("Services");?></strong><br>
			  &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href="/services_samba.php" class="navlnk"><?=gettext("CIFS");?></a><br>
              &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href="/services_ftp.php" class="navlnk"><?=gettext("FTP");?></a><br>
              &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href="/services_nfs.php" class="navlnk"><?=gettext("NFS");?></a><br>
              &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href="/services_rsyncd.php" class="navlnk"><?=gettext("RSYNCD");?></a><br>
              &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href="/services_sshd.php" class="navlnk"><?=gettext("SSHD");?></a><br>
              &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href="/services_unison.php" class="navlnk"><?=gettext("Unison");?></a><br>
              &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href="/services_afp.php" class="navlnk"><?=gettext("AFP");?></a><br>
              &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href="/services_upnp.php" class="navlnk"><?=gettext("UPnP");?></a><br>
              <strong><?=gettext("Access");?></strong><br>
              &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href="/access_users.php" class="navlnk"><?=gettext("Users and Groups");?></a><br>
              &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href="/access_ad.php" class="navlnk"><?=gettext("Active Directory");?></a><br>
              &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href="/notavailable.php" class="navlnk"><?=gettext("NIS");?></a><br>
              &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href="/notavailable.php" class="navlnk"><?=gettext("Radius");?></a><br>
              <strong><?=gettext("Status");?></strong><br>
              &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href="/index.php" class="navlnk"><?=gettext("System");?></a><br>
              &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href="/status_process.php" class="navlnk"><?=gettext("Process");?></a><br>
              &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href="/status_interfaces.php" class="navlnk"><?=gettext("Interfaces");?></a><br>
              &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href="/status_disks.php" class="navlnk"><?=gettext("Disks");?></a><br>
              &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href="/status_wireless.php" class="navlnk"><?=gettext("Wireless");?></a><br>
              &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href="/status_graph.php" class="navlnk"><?=gettext("Graph");?></a><br>
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
			  <?php if (isset($config['system']['webgui']['expanddiags']) || strstr($_SERVER['SCRIPT_FILENAME'], "diag_") || strstr($_SERVER['SCRIPT_FILENAME'], "reboot")): ?>
			  <a href="javascript:showhide('diag','tri_diag')"><img src="/tri_o.gif" id="tri_diag" width="14" height="10" border="0"></a><strong><a href="javascript:showhide('diag','tri_diag')" class="navlnk"><?=gettext("Diagnostics");?></a></strong><br>
			  <span id="diag">
			  <?php else: ?>
			  <a href="javascript:showhide('diag','tri_diag')"><img src="/tri_c.gif" id="tri_diag" width="14" height="10" border="0"></a><strong><a href="javascript:showhide('diag','tri_diag')" class="navlnk"><?=gettext("Diagnostics");?></a></strong><br>
			  <span id="diag" style="display: none">
			  <?php endif; ?>
				  &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href="/diag_logs.php" class="navlnk"><?=gettext("Logs");?></a><br>
				  &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href="/diag_infos.php" class="navlnk"><?=gettext("Information");?></a><br>
				  &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href="/diag_ping.php" class="navlnk"><?=gettext("Ping/Traceroute");?></a><br>
				  &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href="/diag_arp.php" class="navlnk"><?=gettext("ARP table");?></a><br>
				  &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href="/exec.php" class="navlnk"><?=gettext("Command");?></a><br>
				  &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href="/diag_edit.php" class="navlnk"><?=gettext("Edit File");?></a><br>
				  &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href="/diag_backup.php" class="navlnk"><?=gettext("Backup/Restore");?></a><br>
				  &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href="/diag_defaults.php" class="navlnk"><?=gettext("Factory defaults");?></a><br>
				  &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href="/reboot.php" class="navlnk"><?=gettext("Reboot system");?></a>
				  &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href="/shutdown.php" class="navlnk"><?=gettext("Shutdown system");?></a>
			  </span>
			  </font></span>
            </td>
        </tr></table></td>
    <td width="600"><table width="100%" border="0" cellpadding="10" cellspacing="0">
        <tr><td>
<?php if (!$pgtitle_omit): ?>
      <p class="pgtitle"><?=gentitle($pgtitle);?></p>
<?php endif; ?>
