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

	packages_ln_files("{$pkgpath}/usr");

	return $result;
}

// Create soft links for each file in the packages.
function packages_ln_files($path) {
	$pkgpath = "{$config['packages']['path']}/packages";

	$handle = @opendir($path);
  while($file = @readdir($handle)) {
		if ($file == "." || $file == "..") {
			continue;
		}

    $realpath = "/usr/local/" . substr(strchr($path,"packages/usr/"),13);

    if(is_dir("{$path}/{$file}")) {
    	// Make sure directory exists.
    	$result = mwexec("mkdir -p {$realpath}/{$file}");
    	// Scan directory recursively.
      packages_ln_files("{$path}/{$file}");
    }
    else if(is_file("{$path}/{$file}")) {
    	// Create a soft link.
    	$result = mwexec("/bin/ln -s {$path}/{$file} {$realpath}/{$file}");
		}
  }
  @closedir($handle);
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
// Return 0 if sucessful, 1 if error
function packages_install($pkgname) {
	global $config;

	$prefix = $config['packages']['path'] . "/packages/usr";

	$result = mwexec("/usr/sbin/pkg_add -P {$prefix} {$pkgname}");
	if (0 == $result) {
		write_log("Successfully installed package '{$pkgname}'");
	} else {
		write_log("Failed to install package '{$pkgname}'");
	}

	return $result;
}

// Uninstall package.
// Return 0 if sucessful, 1 if error
function packages_uninstall($pkgname) {
	$result = mwexec("/usr/sbin/pkg_delete {$pkgname}");
	if (0 == $result) {
		write_log("Successfully deleted package '{$pkgname}'");
	} else {
		write_log("Failed to delete package '{$pkgname}'");
	}

	return $result;
}
?>
