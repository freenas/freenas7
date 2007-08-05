#!/usr/local/bin/php -n -q
# Copyright (c) 2007 Volker Theile (votdev@gmx.de)
# All rights reserved.
<?php
require_once("config.inc");
require_once("util.inc");
require_once("phpmailer/class.phpmailer.php");

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
$mail->Body = "This is the message body";

// Enable SMTH authentication if set.
if (isset($config['email']['auth'])) {
	$mail->SMTPAuth = true;
	$mail->Username = $config['email']['username'];
	$mail->Password = base64_decode($config['email']['password']);
}

// Send email and log result.
if(!$mail->Send()) {
	write_log($mail->ErrorInfo);
} else {
	write_log("Status email successfully sent to {$config['email']['to']}.");
}
?>
