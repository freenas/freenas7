<?php
/*
	xmlparse.inc
	functions to parse/dump configuration files in XML format

	part of FreeNAS (http://www.freenas.org)
	Copyright (C) 2005-2007 Olivier Cochard-Labbe <olivier@freenas.org>.
	All rights reserved.

	Based on m0n0wall (http://m0n0.ch/wall)
	Copyright (C) 2003-2006 Manuel Kasper <mk@neon1.net>.
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
require_once("util.inc");
require_once("XML/Unserializer.php");
require_once("XML/Serializer.php");

/* tags that are always to be handled as lists */
/* This are TAGS that can be multiple in the XML config file */
$listtags = explode(" ", "key dnsserver winsserver disk vdisk diskr sharetosync " .
	"encryption-algorithm-option hash-algorithm-option hosts onetoone hidemount " .
	"staticmap route pipe queue shellcmd cacert earlyshellcmd user group " .
	"servernat nfsnetworks passthrumac allowedip mount vlan domainoverrides " .
	"minute hour day month weekday content rsyncclient rsynclocal package " .
	"ipv6dnsserver share");

// Load configuration XML file and unserialize it into an array.
// Returns configuration array if successful, otherwise false.
function parse_xml_config($config, $rootobj) {
	$options = array(
										XML_UNSERIALIZER_OPTION_COMPLEXTYPE => 'array',
										XML_UNSERIALIZER_OPTION_FORCE_ENUM  => array($listtags)
									);

	$unserializer = &new XML_Unserializer($options);
	$status = $unserializer->unserialize($config, true);

	if (PEAR::isError($status)) {
		write_console("Error: Failed to unserialize data.\n");
		write_console($status->getMessage());
		write_console("\n");
		return false;
	}

	return $unserializer->getUnserializedData();
}

// Serialize configuration array as XML data.
// Returns configuration as XML string if successful, otherwise false.
function dump_xml_config($arr, $rootobj) {
	$options = array(
										XML_SERIALIZER_OPTION_XML_DECL_ENABLED => true,
										XML_SERIALIZER_OPTION_INDENT           => "\t",
										XML_SERIALIZER_OPTION_LINEBREAKS       => "\n",
										XML_SERIALIZER_OPTION_ROOT_NAME        => $rootobj,
										XML_SERIALIZER_OPTION_MODE             => XML_SERIALIZER_MODE_SIMPLEXML,
										XML_SERIALIZER_OPTION_IGNORE_FALSE     => true,
										XML_SERIALIZER_OPTION_CONDENSE_BOOLS   => true
									);

	$serializer = new XML_Serializer($options);
	$result = $serializer->serialize($arr);

	if (true !== $result) {
		write_console("Error: Failed to serialize data.\n");
		print_r($result);
		write_console("\n");
		return false;
	}

	return $serializer->getSerializedData();
}
?>
