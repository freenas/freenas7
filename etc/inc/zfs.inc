<?php
/*
	zfs.inc
	Copyright (c) 2008-2009 Volker Theile (votdev@gmx.de)
	Copyright (c) 2008 Nelson Silva
	All rights reserved.

	part of FreeNAS (http://www.freenas.org)
	Copyright (C) 2005-2009 Olivier Cochard-Labbe <olivier@freenas.org>.
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

/**
 * Get list of available zpools
 * @return An array containg the requested informations:
 * @code
 * [poolname] => array(
 *   [name] => pool
 *   [size] => 4.66G
 *   [used] => 112K
 *   [avail] => 4.66G
 *   [cap] => 0%
 *   [health] => ONLINE
 *   [altroot] => -)
 * @endcode
 */
function zfs_get_pool_list() {
	$poollist = array();

	exec("/sbin/zpool list -H", $rawdata);

	foreach ($rawdata as $line) {
		$aline = preg_split("/\s+/", $line);

		$poolname = $aline[0];

		$poollist[$poolname] = array();
		$poollist[$poolname]['name'] = $aline[0];
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

	$pool = $config['zfs']['pools']['pool'][$index];
	if (!is_array($pool))
		 return 1;

	// Set the default mount point.
	$mountpoint = "/mnt/{$pool['name']}";
	if (!empty($pool['mountpoint']))
		$mountpoint = $pool['mountpoint'];

	// Additional parameter
	$param = "-m {$mountpoint} ";

	if (!empty($pool['root']))
		$param .= "-R {$pool['root']} ";

	// Create the pool
	$cmd = "/sbin/zpool create {$param} {$pool['name']} ";

	foreach ($pool['vdevice'] as $vdevicev) {
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

	$result = mwexec($cmd);

	// Modify access restrictions.
	@chmod($mountpoint, octdec(777));

	return $result;
}

/**
 * Delete a zpool.
 * @param pool	Name of the pool to be destroyed.
 * @return 0 if successful, otherwise 1
 */ 
function zfs_zpool_destroy($pool) {
	// Destroy the pool
	$result = mwexec2("zpool destroy {$pool}");

	return $result;
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

// Configure, create and start a dataset
// $name - Name of the dataset to be configured.
// Return 0 if successful, 1 if error
function zfs_dataset_configure($name) {
	global $config;

	if (!is_array($config['zfs']['datasets']['dataset']))
		return 1;

	$index = array_search_ex($name, $config['zfs']['datasets']['dataset'], "name");
	if (false === $index)
		return 1;

	$dataset = $config['zfs']['datasets']['dataset'][$index];
	if (!is_array($dataset))
		 return 1;

	// Additional parameter
	$param = "";

	if (!empty($dataset['compression']))
		$param .= "-o compression={$dataset['compression']} ";

	if (!isset($dataset['canmount']))
		$param .= "-o canmount=off ";

	if (isset($dataset['readonly']))
		$param .= "-o readonly=on ";

	if (!empty($dataset['quota']))
		$param .= "-o quota={$dataset['quota']} ";

	if (!isset($dataset['xattr']))
		$param .= "-o xattr=off ";

	// Create the dataset
	$cmd = "/sbin/zfs create {$param} {$dataset['pool'][0]}/{$dataset['name']}";

	return mwexec($cmd);
}

// Delete a dataset given in parameter.
// $name - Name of the dataset to be deleted.
// Return 0 if successful, 1 if error
function zfs_dataset_destroy($name) {
	global $config;

	if (!is_array($config['zfs']['datasets']['dataset']))
		return 1;

	$index = array_search_ex($name, $config['zfs']['datasets']['dataset'], "name");
	if (false === $index)
		return 1;

	$dataset = $config['zfs']['datasets']['dataset'][$index];
	if (!is_array($dataset))
		 return 1;

	// Destroy the dataset
	return mwexec("/sbin/zfs destroy {$dataset['pool'][0]}/{$dataset['name']}");
}

// Update properties of a dataset
// $name - Name of the dataset to be updated.
// Return 0 if successful, 1 if error
function zfs_dataset_properties($name) {
	global $config;

	if (!is_array($config['zfs']['datasets']['dataset']))
		return 1;

	$index = array_search_ex($name, $config['zfs']['datasets']['dataset'], "name");
	if (false === $index)
		return 1;

	$dataset = $config['zfs']['datasets']['dataset'][$index];
	if (!is_array($dataset))
		 return 1;

	$result = 0;

	foreach (explode(" ", "compression canmount readonly quota") as $attr) {
		$param = "";

		switch ($attr) {
			case "compression":
				if (!empty($dataset['compression']))
					$param = "compression={$dataset['compression']}";
				break;

			case "canmount":
				if (!isset($dataset['canmount']))
					$param = "canmount=off";
				else
					$param = "canmount=on";
				break;

			case "readonly":
				if (isset($dataset['readonly']))
					$param = "readonly=on";
				else
					$param = "readonly=off";
				break;

			case "quota":
				if (!empty($dataset['quota']))
					$param = "quota={$dataset['quota']}";
				else
					$param = "quota=none";
				break;

			case "xattr":
				if (!isset($dataset['xattr']))
					$param = "xattr=off";
				else
					$param = "xattr=on";
				break;
		}

		// Update dataset properties
		if (!empty($param)) {
			$cmd = "/sbin/zfs set {$param} {$dataset['pool'][0]}/{$dataset['name']}";
			$result |= mwexec($cmd);
		}
	}

	return $result;
}

// Returns true if $poolname is valid.
function zfs_is_valid_poolname($poolname) {
	if (!is_string($poolname))
		return false;

	// Empty pool names are not allowed.
	if (empty($poolname))
		return false;

	// The pool names "mirror", "raidz" and "spare" are reserved, as are
	// names beginning with the pattern "c[0-9]".
	if (preg_match("/^(mirror|raidz|spare|c[0-9]).*$/", $poolname))
		return false;

	// The pool name must begin with a letter, and can only contain
	// alphanumeric characters as well as underscore ("_"), dash ("-") and
	// period (".").
	if (preg_match("/^[a-zA-Z]([a-zA-Z0-9\_\-\.\s])*$/", $poolname))
		return true;

	return false;
}
?>
