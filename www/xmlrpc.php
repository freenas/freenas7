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

function xmlrpc_system_getinfo($method_name, $params, $app_data) {
	// Get uptime and date.
	$value['uptime'] = system_get_uptime();
	$value['date'] = shell_exec("date");
	// Get RAM usage.
	$raminfo = system_get_ram_info();
	$percentage = round(($raminfo['used'] * 100) / $raminfo['total'], 0);
	$value['memusage']['percentage'] = $percentage;
	$value['memusage']['caption'] = sprintf(gettext("%d%% of %dMB"), $percentage, round($raminfo['physical'] / 1024 / 1024));
	// Get load average.
	exec("uptime", $result);
	$value['loadaverage'] = substr(strrchr($result[0], "load averages:"), 15);
	// Get up-to-date CPU informations.
	$cpuinfo = system_get_cpu_info();
	$value['cputemp'] = $cpuinfo['temperature'];
	$value['cpufreq'] = $cpuinfo['freq'];
	// Get CPU usage.
	$value['cpuusage'] = system_get_cpu_usage();
	// Get disk usage.
	$a_diskusage = get_mount_usage();
	if (is_array($a_diskusage) && (0 < count($a_diskusage))) {
		foreach ($a_diskusage as $diskusagek => $diskusagev) {
			$fsid = get_mount_fsid($diskusagev['filesystem'], $diskusagek);
			$diskinfo = array();
			$diskinfo['id'] = $fsid;
			$diskinfo['percentage'] = rtrim($diskusagev['capacity'],"%");
			$diskinfo['tooltip']['used'] = sprintf(gettext("%sB used of %sB"), $diskusagev['used'], $diskusagev['size']);
			$diskinfo['tooltip']['available'] = sprintf(gettext("%sB available of %sB"), $diskusagev['avail'], $diskusagev['size']);
			$diskinfo['caption'] = sprintf(gettext("%s of %sB"), $diskusagev['capacity'], $diskusagev['size']);
			$value['diskusage'][] = $diskinfo;
		}
	}
	// Get swap info.
	$swapinfo = system_get_swap_info();
	if (is_array($swapinfo) && (0 < count($swapinfo))) {
		$id = 0;
		foreach ($swapinfo as $swap) {
			$id++;
			$devswap = array();
			$devswap['id']  = $id;
			$devswap['percentage']  = rtrim($swap['capacity'],"%");
			$devswap['tooltip']['used'] = sprintf(gettext("%sB used of %sB"), $swap['used'], $swap['total']);
			$devswap['tooltip']['available']  = sprintf(gettext("%sB available of %sB"), $swap['avail'], $swap['total']);
			$devswap['caption'] = sprintf(gettext("%s of %sB"), $swap['capacity'], $swap['total']);
			$value['swapusage'][]= $devswap;
		}
	}

	// Encode to JSON?
	if ("json" === $app_data['encoding'])
		$value = json_encode($value);

	return $value;
}

// When an XML-RPC request is sent to this script, it can be found in the
// raw post data.
$request_xml = $HTTP_RAW_POST_DATA;
if (empty($request_xml))
	die;

// Get misc options.
$userdata = array("encoding" => $_GET['encoding']);

// Create XMLRPC server.
$xmlrpc_server = xmlrpc_server_create();

// Register methods.
$registered = xmlrpc_server_register_method($xmlrpc_server, "System.GetInfo", "xmlrpc_system_getinfo");

// Send request to the server to get the response XML.
$options = array('output_type' => 'xml', 'version' => 'auto');
$response = xmlrpc_server_call_method($xmlrpc_server, $request_xml, $userdata, $options);

// Print the response for the client to read.
print $response;

// Destroy XMLRPC server.
xmlrpc_server_destroy($xmlrpc_server);
?>
