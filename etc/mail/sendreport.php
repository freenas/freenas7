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
$statusreport->AddArticle(new StatusReportArticleCmd("Version","cat /etc/prd.version"));
$statusreport->AddArticle(new StatusReportArticleCmd("Platform","cat /etc/platform"));
$statusreport->AddArticle(new StatusReportArticleCmd("System uptime","uptime"));
$statusreport->AddArticle(new StatusReportArticleCmd("Interfaces","/sbin/ifconfig -a"));
$statusreport->AddArticle(new StatusReportArticleCmd("Routing tables","netstat -nr"));
$statusreport->AddArticle(new StatusReportArticleCmd("Processes","ps xauww"));
$statusreport->AddArticle(new StatusReportArticleCmd("Swap usage","/usr/sbin/swapinfo"));
$statusreport->AddArticle(new StatusReportArticleCmd("Sensors","/usr/local/bin/chm -I -d 0"));
$statusreport->AddArticle(new StatusReportArticleCmd("ATA disk","/sbin/atacontrol list"));
$statusreport->AddArticle(new StatusReportArticleCmd("SCSI disk","/sbin/camcontrol devlist"));
$disklist = get_physical_disks_list();
foreach ($disklist as $disknamek => $disknamev) {
	$statusreport->AddArticle(new StatusReportArticleCmd("S.M.A.R.T. [/dev/{$disknamek}]","/usr/local/sbin/smartctl -a /dev/{$disknamek}"));
}
$statusreport->AddArticle(new StatusReportArticleCmd("Geom Concat","/sbin/gconcat list"));
$statusreport->AddArticle(new StatusReportArticleCmd("Geom Stripe","/sbin/gstripe list"));
$statusreport->AddArticle(new StatusReportArticleCmd("Geom Mirror","/sbin/gmirror list"));
$statusreport->AddArticle(new StatusReportArticleCmd("Geom RAID5","/sbin/graid5 list"));
$statusreport->AddArticle(new StatusReportArticleCmd("Geom Vinum","/sbin/gvinum list"));
$statusreport->AddArticle(new StatusReportArticleCmd("Mount point","/sbin/mount"));
$statusreport->AddArticle(new StatusReportArticleCmd("Free disk space","/bin/df -h"));
$statusreport->AddArticle(new StatusReportArticleCmd("Encrypted disks","/sbin/geli list"));
$statusreport->AddArticle(new StatusReportArticleCmd("dmesg","/sbin/dmesg"));
$numentries = $config['syslogd']['nentries'];
$reverse = isset($config['syslogd']['reverse']) ? "-r" : "";
$statusreport->AddArticle(new StatusReportArticleCmd("Last {$numentries} System log entries","/usr/sbin/clog /var/log/system.log 2>&1 | tail {$reverse} -n {$numentries}"));
$statusreport->AddArticle(new StatusReportArticleCmd("Last {$numentries} FTP log entries","/usr/sbin/clog /var/log/ftp.log 2>&1 | tail {$reverse} -n {$numentries}"));
$statusreport->AddArticle(new StatusReportArticleCmd("Last {$numentries} RSYNCD log entries","/usr/sbin/clog /var/log/rsyncd.log 2>&1 | tail {$reverse} -n {$numentries}"));
$statusreport->AddArticle(new StatusReportArticleCmd("Last {$numentries} SSHD log entries","/usr/sbin/clog /var/log/sshd.log 2>&1 | tail {$reverse} -n {$numentries}"));
$statusreport->AddArticle(new StatusReportArticleCmd("Last {$numentries} SMARTD log entries","/usr/sbin/clog /var/log/smartd.log 2>&1 | tail {$reverse} -n {$numentries}"));
$statusreport->AddArticle(new StatusReportArticleCmd("Last {$numentries} Daemon log entries","/usr/sbin/clog /var/log/daemon.log 2>&1 | tail {$reverse} -n {$numentries}"));

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
$mail->Subject = $config['statusreport']['subject'];
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
