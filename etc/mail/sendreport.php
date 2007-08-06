#!/usr/local/bin/php -n -q
# status_report.php
# Copyright (c) 2007 Volker Theile (votdev@gmx.de)
# All rights reserved.
<?php
require_once("config.inc");
require_once("util.inc");
require_once("disks.inc");
require_once("report.inc");
require_once("phpmailer/class.phpmailer.php");

$statusreport = new StatusReport();
$statusreport->AddCommand("Version","cat /etc/prd.version");
$statusreport->AddCommand("Platform","cat /etc/platform");
$statusreport->AddCommand("System uptime","uptime");
$statusreport->AddCommand("Interfaces","/sbin/ifconfig -a");
$statusreport->AddCommand("Routing tables","netstat -nr");
$statusreport->AddCommand("Processes","ps xauww");
$statusreport->AddCommand("Swap usege","/usr/sbin/swapinfo");
$statusreport->AddCommand("Sensors","/usr/local/bin/chm -I -d 0");
$statusreport->AddCommand("ATA disk","/sbin/atacontrol list");
$statusreport->AddCommand("SCSI disk","/sbin/camcontrol devlist");
$statusreport->AddTitle("S.M.A.R.T.");
$disklist=get_physical_disks_list();
foreach ($disklist as $disknamek => $disknamev) {
	$statusreport->AddCommand("/usr/local/sbin/smartctl -a /dev/{$disknamek}");
}
$statusreport->AddCommand("Geom Concat","/sbin/gconcat list");
$statusreport->AddCommand("Geom Stripe","/sbin/gstripe list");
$statusreport->AddCommand("Geom Mirror","/sbin/gmirror list");
$statusreport->AddCommand("Geom RAID5","/sbin/graid5 list");
$statusreport->AddCommand("Geom Vinum","/sbin/gvinum list");
$statusreport->AddCommand("Mount point","/sbin/mount");
$statusreport->AddCommand("Free Disk Space","/bin/df -h");
$statusreport->AddCommand("Encrypted disks","/sbin/geli list");
$statusreport->AddCommand("Last 200 system log entries","/usr/sbin/clog /var/log/system.log 2>&1 | tail -n 200");

$mail = new PHPMailer();
$mail->IsSMTP();
$mail->IsHTML(false);
$mail->SetLanguage("en","/etc/inc/phpmailer/");
$mail->SMTPDebug = false;
$mail->Hostname = "{$config['system']['hostname']}.{$config['system']['domain']}";
$mail->Host = $config['statusreport']['server'];
$mail->Port = $config['statusreport']['port'];
$mail->From = $config['statusreport']['from'];
$mail->FromName = get_product_name() . " status";
$mail->Subject = get_product_name() . " status report";
$mail->AddAddress($config['statusreport']['to']);

// Enable SMTH authentication if set.
if (isset($config['statusreport']['auth'])) {
	$mail->SMTPAuth = true;
	$mail->Username = $config['statusreport']['username'];
	$mail->Password = base64_decode($config['statusreport']['password']);
}

// Create report.
$mail->Body = $statusreport->Generate();

// Send email and log result.
if(!$mail->Send()) {
	write_log($mail->ErrorInfo);
} else {
	write_log("Status report successfully sent to {$config['statusreport']['to']}.");
}
?>
