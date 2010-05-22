<?php
/*
	zfs.inc
	Copyright (c) 2008-2009 Volker Theile (votdev@gmx.de)
	Copyright (c) 2008 Nelson Silva
	All rights reserved.

	part of FreeNAS (http://freenas.org)
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
	$result = array();

	mwexec2("zpool list -H", $rawdata);
	foreach ($rawdata as $line) {
		$aline = preg_split("/\s+/", $line);
		if (7 > count($aline)) // Ensure we process the correct line data
			continue;

		$poolname = $aline[0];

		$result[$poolname] = array();
		$result[$poolname]['name'] = $aline[0];
		$result[$poolname]['size'] = $aline[1];
		$result[$poolname]['used'] = $aline[2];
		$result[$poolname]['avail'] = $aline[3];
		$result[$poolname]['cap'] = $aline[4];
		$result[$poolname]['health'] = $aline[5];
		$result[$poolname]['altroot'] = $aline[6];

		// Get correct used/available disk space.
		// See http://bugs.opensolaris.org/bugdatabase/view_bug.do?bug_id=6308817
		mwexec2("zfs list -H -o used,available {$poolname}", $rawdata2);
		if (preg_match("/^(\S+)\s+(\S+)$/", $rawdata2[0], $matches)) {
			$result[$poolname]['used'] = $matches[1];
			$result[$poolname]['avail'] = $matches[2];
		}
		// Unset variable, otherwise data will be attached next loop.
		unset($rawdata2);
	}

	return $result;
}

/**
 * Configure, create and start a zpool.
 * @param[in] uuid	The UUID of the zpool.
 * @return Return 0 if successful, otherwise 1
 */
function zfs_zpool_configure($uuid) {
	global $config;

	if (!is_array($config['zfs']['pools']['pool']))
		return 1;

	$index = array_search_ex($uuid, $config['zfs']['pools']['pool'], "uuid");
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
	$cmd = "zpool create {$param} {$pool['name']} ";

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

	$result = mwexec($cmd, true);
	if (0 != $result)
		return 1;

	// Modify access restrictions.
	@chmod($mountpoint, octdec(777));

	return 0;
}

/**
 * Destroy a zpool.
 * @param[in] uuid	The UUID of the zpool.
 * @return 0 if successful, otherwise 1
 */
function zfs_zpool_destroy($uuid) {
	global $config;

	if (!is_array($config['zfs']['pools']['pool']))
		return 1;

	$index = array_search_ex($uuid, $config['zfs']['pools']['pool'], "uuid");
	if (false === $index)
		return 1;

	$pool = $config['zfs']['pools']['pool'][$index];
	if (!is_array($pool))
		 return 1;

	// Destroy the pool
	$result = mwexec("zpool destroy {$pool['name']}", true);

	// Remove mount point.
	$mountpoint = "/mnt/{$pool['name']}";
	if (!empty($pool['mountpoint']))
		$mountpoint = $pool['mountpoint'];

	@rmdir($mountpoint);

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
	$cmd = "zpool {$command} {$param}";

	if (true === $verbose) {
		if (true === $stderr)
			$cmd .= " 2>&1"; // Redirect error message to stdout
		system($cmd, $result);
	}	else {
		if (true === $out) {
			mwexec2($cmd, $output, $result);
		} else {
			$result = mwexec($cmd, true);
		}
	}

	return $result;
}

/**
 * Configure, create and start a ZFS dataset.
 * @param[in] uuid	The UUID of the dataset to be configured.
 * @return Return 0 if successful, otherwise 1.
 */
function zfs_dataset_configure($uuid) {
	global $config;

	if (!is_array($config['zfs']['datasets']['dataset']))
		return 1;

	$index = array_search_ex($uuid, $config['zfs']['datasets']['dataset'], "uuid");
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
	$cmd = "zfs create {$param} {$dataset['pool'][0]}/{$dataset['name']}";

	return mwexec($cmd, true);
}

/**
 * Delete a ZFS dataset.
 * @param[in] uuid	The UUID of the dataset to be deleted.
 * @return Return 0 if successful, otherwise 1.
 */
function zfs_dataset_destroy($uuid) {
	global $config;

	if (!is_array($config['zfs']['datasets']['dataset']))
		return 1;

	$index = array_search_ex($uuid, $config['zfs']['datasets']['dataset'], "uuid");
	if (false === $index)
		return 1;

	$dataset = $config['zfs']['datasets']['dataset'][$index];
	if (!is_array($dataset))
		 return 1;

	// Destroy the dataset
	return mwexec("zfs destroy {$dataset['pool'][0]}/{$dataset['name']}", true);
}

/**
 * Update properties of a ZFS dataset.
 * @param[in] uuid	The UUID of the dataset to be updated.
 * @return Return 0 if successful, otherwise 1.
 */
function zfs_dataset_properties($uuid) {
	global $config;

	if (!is_array($config['zfs']['datasets']['dataset']))
		return 1;

	$index = array_search_ex($uuid, $config['zfs']['datasets']['dataset'], "uuid");
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
			$cmd = "zfs set {$param} {$dataset['pool'][0]}/{$dataset['name']}";
			$result |= mwexec($cmd, true);
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
