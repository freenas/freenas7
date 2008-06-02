<?php
/*
	zfs.inc
	Copyright (c) 2008 Volker Theile (votdev@gmx.de)
	Copyright (c) 2008 Nelson Silva
	All rights reserved.

	part of FreeNAS (http://www.freenas.org)
	Copyright (C) 2005-2008 Olivier Cochard-Labbe <olivier@freenas.org>.
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

// Get list of zpools
//[poolname] => Array
//    (
//        [name] => pool
//        [size] => 4.66G
//        [used] => 112K
//        [avail] => 4.66G
//        [cap] => 0%
//        [health] => ONLINE
//        [altroot] => -
//    )
function zfs_get_pool_list() {
	$poollist = array();

	exec("/sbin/zpool list -H", $rawdata);

	foreach ($rawdata as $line) {
		$aline = preg_split("/\s+/", $line);

		$poolname = $aline[0];
		$poollist[$poolname] = array();
		$disklist[$poolname]['name'] = $aline[0];
		$poollist[$poolname]['size'] = $aline[1];
		$poollist[$poolname]['used'] = $aline[2];
		$poollist[$poolname]['avail'] = $aline[3];
		$poollist[$poolname]['cap'] = $aline[4];
		$poollist[$poolname]['health'] = $aline[5];
		$poollist[$poolname]['altroot'] = $aline[6];
	}

	return $poollist;
}

// Configure, create and start a zpool
// $name - Name of the pool to be configured.
// Return 0 if successful, 1 if error
function zfs_zpool_configure($name) {
	global $config;

	if (!is_array($config['zfs']['pools']['pool']))
		return 1;

	$index = array_search_ex($name, $config['zfs']['pools']['pool'], "name");
	if (false === $index)
		return 1;

	$vpool = $config['zfs']['pools']['pool'][$index];
	if (!is_array($vpool))
		 return 1;

	// Additional parameter
	$param = "";

	if (!empty($vpool['root']))
		$param .= "-R {$vpool['root']} ";

	if (!empty($vpool['mountpoint'])) {
		$param .= "-m {$vpool['mountpoint']} ";
	} else {
		$param .= "-m /mnt/{$vpool['name']} ";
	}

	// Create pool
	$cmd = "/sbin/zpool create {$param} {$vpool['name']} ";

	foreach ($vpool['vdevice'] as $vdevicev) {
		$index = array_search_ex($vdevicev, $config['zfs']['vdevices']['vdevice'], "name");
		if (false === $index)
			continue;

		$vdevice = $config['zfs']['vdevices']['vdevice'][$index];

		switch ($vdevice['type']) {
			case "stripe":
				break;

			default:
				$cmd .= "{$vdevice['type']} ";
				break;
		}

		if (is_array($vdevice['device'])) {
			foreach ($vdevice['device'] as $vdevicev) {
				$cmd .= "{$vdevicev} ";
			}
		}
	}

	return mwexec($cmd);
}

// Delete zpool given in parameter.
// $name - Name of the pool to be deleted.
// Return 0 if successful, 1 if error
function zfs_zpool_destroy($name) {
	global $config;

	if (!is_array($config['zfs']['pools']['pool']))
		return 1;

	$index = array_search_ex($name, $config['zfs']['pools']['pool'], "name");
	if (false === $index)
		return 1;

	$vpool = $config['zfs']['pools']['pool'][$index];
	if (!is_array($vpool))
		 return 1;

	// destroy the pool
	mwexec("/sbin/zpool destroy {$name}");

	return 0;
}

// Wrapper to execute zpool commands.
// $command - Command to execute (e.g. upgrade).
// $param   - The command parameter.
// $verbose - Display command results or hide them.
// $stderr  - Redirect stderr to stdout to display error messages too.
// Return 0 if successful, 1 if error.
function zfs_zpool_cmd($command, $param, $verbose = false, $stderr = true, $out = false, &$output = array()) {
	$result = 1;
	$cmd = "/sbin/zpool {$command} {$param}";

	if (true === $verbose) {
		if (true === $stderr)
			$cmd .= " 2>&1"; // Redirect error message to stdout
		system($cmd, $result);
	}	else {
		if (true === $out) {
			exec($cmd, $output, $result);
		} else {
			$result = mwexec($cmd);
		}
	}

	return $result;
}
?>
