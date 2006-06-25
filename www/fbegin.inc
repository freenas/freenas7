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
    <td width="150" height="65" align="center" valign="middle"> <strong><a href="http://www.freenas.org" target="_blank"><img src="/logo.gif" width="150" height="47" border="0"></a></strong></td>
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
          <td><span class="navlnk"><font color="#FFFFFF"> <strong><?=_MENULEFT_SYSTEM;?></strong> 
              <br>
              &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href="/system.php" class="navlnk"><?=_MENULEFT_SYSGENSETUP;?></a><br>
              &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href="/system_routes.php" class="navlnk"><?=_MENULEFT_SYSSTATROUTES;?></a><br>
              &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href="/system_firmware.php" class="navlnk"><?=_MENULEFT_SYSFIRMWARE;?></a><br>
              &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href="/system_advanced.php" class="navlnk"><?=_MENULEFT_SYSADVANCED;?></a><br>
              <strong><?=_MENULEFT_INTERFACES;?></strong>
			  <?php if (!isset($config['system']['webgui']['noassigninterfaces'])): ?>
			  <a href="/interfaces_assign.php" class="navlnks">(<?=_MENULEFT_INTASSIGN;?>)</a>
			  <?php endif; ?>
			  <br>
              &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href="/interfaces_lan.php" class="navlnk"><?=_MENULEFT_INTLAN;?></a><br>
              <?php for ($i = 1; isset($config['interfaces']['opt' . $i]); $i++): ?>
              &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href="/interfaces_opt.php?index=<?=$i;?>" class="navlnk"><?=htmlspecialchars($config['interfaces']['opt' . $i]['descr']);?></a><br>
			  <?php endfor; ?>
              <strong>Disks</strong><br>
              &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href="/disks_manage.php" class="navlnk"><?=_MENULEFT_DISKMANAG;?></a><br>
              &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href="/disks_raid_gmirror.php" class="navlnk"><?=_MENULEFT_DISKMNGSOFTRAID;?></a> <br>
              &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href="/disks_mount.php" class="navlnk"><?=_MENULEFT_DISKMNGMOUNTPOINT;?></a><br>
 
              <strong>Services</strong><br>
			  &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href="/services_samba.php" class="navlnk"><?=_MENULEFT_SRVCIFS;?></a><br>
              &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href="/services_ftp.php" class="navlnk"><?=_MENULEFT_SRVFTP;?></a><br>
              &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href="/services_nfs.php" class="navlnk"><?=_MENULEFT_SRVNFS;?></a><br>
              &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href="/services_rsyncd.php" class="navlnk"><?=_MENULEFT_SRVRSYNCD;?></a><br>
              &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href="/services_sshd.php" class="navlnk"><?=_MENULEFT_SRVSSHD;?></a><br>
              &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href="/services_afp.php" class="navlnk"><?=_MENULEFT_SRVAFP;?></a><br>
              <strong>Access</strong><br>
              &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href="/access_users.php" class="navlnk"><?=_MENULEFT_ACUSERGROUPS;?></a><br>
              &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href="/access_ad.php" class="navlnk"><?=_MENULEFT_ACMSAD;?></a><br>
              &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href="/notavailable.php" class="navlnk"><?=_MENULEFT_ACNIS;?></a><br>
              &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href="/notavailable.php" class="navlnk"><?=_MENULEFT_ACRADIUS;?></a><br>
              <strong>Status</strong><br>
              &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href="/index.php" class="navlnk"><?=_MENULEFT_STSYSTEM;?></a><br>
              &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href="/status_process.php" class="navlnk"><?=_MENULEFT_STPROCESS;?></a><br>
              &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href="/status_interfaces.php" class="navlnk"><?=_MENULEFT_STINTERFACES;?></a><br>
              &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href="/status_disks.php" class="navlnk"><?=_MENULEFT_STDISKS;?></a><br>
              &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href="/status_wireless.php" class="navlnk"><?=_MENULEFT_STWIRELESS;?></a><br>
			  
<?php
/* extensions section */
if (is_dir("{$g['www_path']}/ext")):
?>
              <strong><?=_MENULEFT_EXTENSIONS;?></strong><br>
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
			  <a href="javascript:showhide('diag','tri_diag')"><img src="/tri_o.gif" id="tri_diag" width="14" height="10" border="0"></a><strong><a href="javascript:showhide('diag','tri_diag')" class="navlnk"><?=_MENULEFT_DIAGNOSTICS;?></a></strong><br>
			  <span id="diag">
			  <?php else: ?>
			  <a href="javascript:showhide('diag','tri_diag')"><img src="/tri_c.gif" id="tri_diag" width="14" height="10" border="0"></a><strong><a href="javascript:showhide('diag','tri_diag')" class="navlnk"><?=_MENULEFT_DIAGNOSTICS;?></a></strong><br>
			  <span id="diag" style="display: none">
			  <?php endif; ?>
				  &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href="/diag_logs.php" class="navlnk"><?=_MENULEFT_DIAGLOGS;?></a><br>
				  &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href="/diag_infos.php" class="navlnk"><?=_MENULEFT_DIAGINF;?></a><br>
				  &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href="/diag_ping.php" class="navlnk"><?=_MENULEFT_DIAGPINGTRACE;?></a><br>
				  &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href="/diag_arp.php" class="navlnk"><?=_MENULEFT_DIAGARPTAB;?></a><br>
				  &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href="/exec.php" class="navlnk"><?=_MENULEFT_DIAGEXEC;?></a><br>
				  &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href="/diag_backup.php" class="navlnk"><?=_MENULEFT_CMDBACKUPRESTORE;?></a><br>
				  &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href="/diag_defaults.php" class="navlnk"><?=_MENULEFT_CMDFACTORYDEF;?></a><br>
				  &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href="/reboot.php" class="navlnk"><?=_MENULEFT_CMDREBOOTSYS;?></a>
				  &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href="/shutdown.php" class="navlnk"><?=_MENULEFT_CMDSHUTDOWNSYS;?></a>
			  </span>
			  </font></span>
            </td>
        </tr></table></td>
    <td width="600"><table width="100%" border="0" cellpadding="10" cellspacing="0">
        <tr><td>
<?php if (!$pgtitle_omit): ?>
      <p class="pgtitle"><?=gentitle($pgtitle);?></p>
<?php endif; ?>
