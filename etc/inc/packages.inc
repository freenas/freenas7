<?php
/*
	packages.inc
	Copyright © 2007 Volker Theile (votdev@gmx.de)
	All rights reserved.

	part of FreeNAS (http://www.freenas.org)
	Copyright (C) 2005-2007 Olivier Cochard-Labbé <olivier@freenas.org>.
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
require_once("functions.inc");
require_once("util.inc");

// Initialize packages.
// Return 0 if sucessful, 1 if error
function packages_configure() {
	global $config;
	$result = 1;

	$pkgpath = "{$config['packages']['path']}/packages";

	// Create packages directory (if necessary).
	$result = mwexec("mkdir -p {$pkgpath}");
	if (1 == $result) {
		write_log("Failed to create packages directory on '{$config['packages']['path']}'");
	}

	// Install packages.
	$apackage = $config['packages']['package'];
	foreach ($apackage as $packagev) {
		packages_install("{$pkgpath}/{$packagev['filename']}");
	}

	return $result;
}

// Get list of installed packages.
// Result is in the form:
// [1] => Array
// (
// 	[name] => "xyz"
// 	[desc]  => "xyz"
// )
// Return array of installed packages
function packages_get_installed() {
	$packages = array();
	$id = 0;

	exec("/usr/sbin/pkg_info", $rawdata);
	foreach($rawdata as $line) {
		$pos = strpos($line," ");
		$pkgname = substr($line,0,$pos);
		$pkgdesc = substr($line,$pos+1);

		$packages[$id] = array();
		$packages[$id]['name'] = $pkgname;
		$packages[$id]['desc'] = $pkgdesc;
		
		$id++;
  }
  unset($line);

	return $packages; 
}

// Install package.
// $pkgname - The package filename to be installed (e.g. xyz.tbz)
// Return 0 if sucessful, 1 if error
function packages_install($pkgname) {
	global $config;

	$prefix = $config['packages']['path'] . "/packages/usr";

	$result = mwexec("/usr/sbin/pkg_add -P {$prefix} {$pkgname}");	
	if (0 == $result) {
		// Create a softlink for each installed file.
		$apackinglist = packages_get_packinglist($pkgname);
		foreach ($apackinglist as $filev) {
			// Check if we need to create a directory first.
			$dirname = dirname("/usr/local/{$filev}");
			if (!file_exists($dirname))
				mkdir($dirname);
			// Create link.
			mwexec("/bin/ln -s {$prefix}/{$filev} /usr/local/{$filev}");
		}

		write_log("Successfully installed package '{$pkgname}'");
	} else {
		write_log("Failed to install package '{$pkgname}'");
	}

	return $result;
}

// Uninstall package.
// $pkgname - The package name or filename (e.g. xyz or xyz.tbz)
// Return 0 if sucessful, 1 if error
function packages_uninstall($pkgname) {
	// Remove '.tbz' if necessary.
	$pkgname = basename($pkgname, ".tbz");

	// Remove softlink for each installed file.
	$apackinglist = packages_get_packinglist($pkgname);
	foreach ($apackinglist as $filev) {
		mwexec("/bin/rm -f /usr/local/{$filev}");
	}

	$result = mwexec("/usr/sbin/pkg_delete {$pkgname}");
	if (0 == $result) {
		write_log("Successfully uninstalled package '{$pkgname}'");
	} else {
		write_log("Failed to uninstall package '{$pkgname}'");
	}

	return $result;
}

// Check whether package is already configured.
// $pkgname - The package filename (e.g. xyz.tbz)
// Return 0 if already configured, otherwise 1
function packages_is_configured($pkgname) {
	global $config;

	$result = 1;

	$apackage = $config['packages']['package'];
	foreach ($apackage as $packagev) {
		if (0 == strcmp($packagev['filename'], $pkgname)) {
			$result = 0;
			break;
		}
	}

	return $result;
}

// Check whether package is already installed.
// $pkgname - The package name or filename (e.g. xyz or xyz.tbz)
// Return 0 if already installed, otherwise 1
function packages_is_installed($pkgname) {
	// Remove '.tbz' if necessary.
	$pkgname = basename($pkgname, ".tbz");

	// Check whether package is already installed.
	$result = mwexec("/usr/sbin/pkg_info -e {$pkgname}");

	return $result;
}

// Get packing list (list installed by package).
// $pkgname - The package name or filename (e.g. xyz or xyz.tbz)
// Return array of files installed by package
function packages_get_packinglist($pkgname) {
	$result = array();
	$id = 0;

	// Remove '.tbz' if necessary.
	$pkgname = basename($pkgname, ".tbz");

	exec("/usr/sbin/pkg_info -f {$pkgname}", $rawdata);
	foreach($rawdata as $line) {
		// Parse lines like: 'File: xxxx', ignore 'File: +xxxx'. 
		if (false === strpos($line, "File: ") || 0 === strpos($line, "File: +")) {
			continue;
		}

		$aline = preg_split("/\s+/", $line);

		$result[$id] = $aline[1];
		$id++;
  }
  unset($line);

	return $result;
}
?>
