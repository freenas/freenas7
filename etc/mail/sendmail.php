#!/usr/local/bin/php -n -q
# Copyright (c) 2007 Volker Theile (votdev@gmx.de)
# All rights reserved.
<?php
require_once("config.inc");
require_once("util.inc");
require_once("phpmailer/class.phpmailer.php");

// Define a command, with a title, to be executed later.
function defCmdT($title, $command) {
	global $commands;
	$commands[] = array($title, $command, false);
}

// Execute a command, with a title, and generate command results output.
function doCmdT(&$body, $title, $command, $isstr) {
	// Set title.
	$nWidth = strlen($title) + 1;
	$body .= sprintf("%s:\n%'-{$nWidth}s\n",$title,"");

	// Execute command and retrieve output.
	exec($command . " 2>&1", $execOutput, $execStatus);
	for ($i = 0; isset($execOutput[$i]); $i++) {
		if ($i > 0) {
			$body .= "\n";
		}
		$body .= $execOutput[$i];
	}

	$body .= "\n\n";
}

// Execute all of the commands.
function create_email_body(&$body) {
	global $commands;
	for ($i = 0; isset($commands[$i]); $i++) {
		doCmdT($body, $commands[$i][0], $commands[$i][1], $commands[$i][2]);
	}
}

// Define commands added in status email.
defCmdT("Version","cat /etc/prd.version");
defCmdT("Platform","cat /etc/platform");
defCmdT("System uptime","uptime");
defCmdT("Interfaces","/sbin/ifconfig -a");
defCmdT("Routing tables","netstat -nr");
defCmdT("Processes","ps xauww");
defCmdT("Swap usege","/usr/sbin/swapinfo");
defCmdT("ATA disk","/sbin/atacontrol list");
defCmdT("SCSI disk","/sbin/camcontrol devlist");
defCmdT("Geom Concat","/sbin/gconcat list");
defCmdT("Geom Stripe","/sbin/gstripe list");
defCmdT("Geom Mirror","/sbin/gmirror list");
defCmdT("Geom RAID5","/sbin/graid5 list");
defCmdT("Geom Vinum","/sbin/gvinum list");
defCmdT("Mount point","/sbin/mount");
defCmdT("Free Disk Space","/bin/df -h");
defCmdT("Encrypted disks","/sbin/geli list");
defCmdT("Last 200 system log entries","/usr/sbin/clog /var/log/system.log 2>&1 | tail -n 200");

$mail = new PHPMailer();
$mail->IsSMTP();
$mail->IsHTML(false);
$mail->SetLanguage("en","/etc/inc/phpmailer/");
$mail->SMTPDebug = false;
$mail->Hostname = "{$config['system']['hostname']}.{$config['system']['domain']}";
$mail->Host = $config['email']['server'];
$mail->Port = $config['email']['port'];
$mail->From = $config['email']['from'];
$mail->FromName = get_product_name() . " status";
$mail->Subject = get_product_name() . " status notification";
$mail->AddAddress($config['email']['to']);

// Enable SMTH authentication if set.
if (isset($config['email']['auth'])) {
	$mail->SMTPAuth = true;
	$mail->Username = $config['email']['username'];
	$mail->Password = base64_decode($config['email']['password']);
}

// Create email body.
create_email_body($mail->Body);

// Send email and log result.
if(!$mail->Send()) {
	write_log($mail->ErrorInfo);
} else {
	write_log("Status email successfully sent to {$config['email']['to']}.");
}
?>
