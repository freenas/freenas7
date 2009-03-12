#!/usr/local/bin/php
<?php
/*
	xmlrpc.php
	Copyright © 2009 Volker Theile (votdev@gmx.de)
	All rights reserved.

	part of FreeNAS (http://www.freenas.org)
	Copyright (C) 2005-2009 Olivier Cochard <olivier@freenas.org>.
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
require_once("system.inc");
require_once("XMLRPC/xmlrpc.inc");
require_once("XMLRPC/xmlrpcs.inc");

function xmlrpc_system_getinfo($xmlrpcmsg) {
	$value = system_get_sysinfo();
	return new xmlrpcresp(php_xmlrpc_encode($value));
}

// When an XML-RPC request is sent to this script, it can be found in the
// raw post data.
$request_xml = $HTTP_RAW_POST_DATA;
if (empty($request_xml))
	die;

// Create XMLRPC server.
$xmlrpc_server = new xmlrpc_server(array(
	"system.getInfo" => array(
		"function" => "xmlrpc_system_getinfo",
		"signature" => array(array($xmlrpcStruct)),
		"docstring" => "Get various system informations.")), false);

// Process request.
$xmlrpc_server->service($request_xml);
?>
