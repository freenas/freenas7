<?php
/*
	email.inc
	Copyright (c) 2008 Volker Theile (votdev@gmx.de)
	All rights reserved.

	part of FreeNAS (http://www.freenas.org)
	Copyright (C) 2005-2008 Olivier Cochard-Labbé <olivier@freenas.org>.
	All rights reserved.

	Redistribution and use in source and binary forms, with or without
	modification, are permitted provided that the following conditions are met:

	1. Redistributions of source code must retain the above copyright notice,
	   this list of conditions and the following disclaimer.

	2. Redistributions in binary form must reproduce the above copyright
	   notice, this list of conditions and the following disclaimer in the
	   documentation and/or other materials provided with the distribution.

	THIS SOFTWARE IS PROVIDED ``AS IS'' AND ANY EXPRESS OR IMPLIED WARRANTIES,
	INCLUDING, BUT NOT LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY
	AND FITNESS FOR A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE
	AUTHOR BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY,
	OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF
	SUBSTITUTE GOODS OR SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS
	INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN
	CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE)
	ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE
	POSSIBILITY OF SUCH DAMAGE.
*/
require_once("config.inc");
require_once("util.inc");
require_once("phpmailer/class.phpmailer.php");

// Checks if Email settings are valid.
// Return 0 if successful, otherwise 1.
function email_validate_settings() {
	global $config;

	$email = $config['system']['email'];

	if (empty($email['server']) || empty($email['port']) || empty($email['security']) || empty($email['from']))
		return 1;

	if (isset($email['auth']) && (empty($email['username']) || empty($email['password'])))
		return 1;

	return 0;
}

// Send an email.
// $address - The email adress
// $subject - The email subject
// $message - The message
// $error - Contains an error message if submission fails.
// Return 0 if successful, otherwise 1.
function email_send($address, $subject, $message, &$error) {
	global $config;

	$mail = new PHPMailer();
	$mail->IsSMTP();
	$mail->IsHTML(false);
	$mail->SetLanguage("en","/etc/inc/phpmailer/");
	$mail->SMTPDebug = false;
	$mail->Hostname = "{$config['system']['hostname']}.{$config['system']['domain']}";
	$mail->Host = $config['system']['email']['server'];
	$mail->Port = $config['system']['email']['port'];
	$mail->From = $config['system']['email']['from'];
	$mail->FromName = get_product_name();
	$mail->Subject = $subject;
	foreach (explode(";", $address) as $addressv) {
		$mail->AddAddress($addressv);
	}

	// Enable SMTH authentication if set.
	if (isset($config['system']['email']['auth'])) {
		$mail->SMTPAuth = true;
		$mail->Username = $config['system']['email']['username'];
		$mail->Password = $config['system']['email']['password'];
	}

	// Enable TLS/SSL SMTP support if set.
	if ("none" !== $config['system']['email']['security']) {
		$mail->SMTPSecure = $config['system']['email']['security'];
	}

	// Create report.
	$mail->Body = $message;

	// Send email and log result.
	if(!$mail->Send()) {
		$error = $mail->ErrorInfo;
		return 1;
	}

	return 0;
}
?>
