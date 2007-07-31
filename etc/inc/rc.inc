<?php
/*
	rc.inc
	Copyright (c) 2007 Volker Theile (votdev@gmx.de)
  All rights reserved.

	part of FreeNAS (http://www.freenas.org)
	Copyright (C) 2005-2007 Olivier Cochard-Labbe <olivier@freenas.org>.
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
require_once("globals.inc");
require_once("util.inc");

// Execute rc script.
// Return 0 if successful, otherwise 1.
function rc_exec_script($scriptname) {
	exec("{$scriptname} >/dev/null 2>&1", $output, $retval);
	return $retval;
}

// Execute rc script asynchron.
// Return 0 if successful, otherwise 1.
function rc_exec_script_async($scriptname) {
	exec("nohup {$scriptname} >/dev/null 2>&1 &", $output, $retval);
	return $retval;
}

// Check if service is running.
// Return 0 if service is running, otherwise 1.
function rc_is_service_running($name) {
	$retval = rc_exec_script("/etc/rc.d/{$name} onestatus");
	return $retval;
}

// Execute service.
// Return 0 if successful, otherwise 1.
function rc_exec_service($name) {
	$retval = rc_exec_script("/etc/rc.d/{$name}");
	if (0 == $retval) {
		write_log("{$name} service executed");
	}	else {
		write_log("Failed to execute service {$name}");
	}
	return $retval;
}

// Start service.
// Return 0 if successful, otherwise 1.
function rc_start_service($name) {
	// Execute script.
	$retval = rc_exec_script("/etc/rc.d/{$name} start");
	if (0 == $retval) {
		write_log("{$name} service started");
	}	else {
		write_log("Failed to start service {$name}");
	}
	return $retval;
}

// Restart service.
// Return 0 if successful, otherwise 1.
function rc_restart_service($name) {
	// Execute script.
	$retval = rc_exec_script("/etc/rc.d/{$name} restart");
	if (0 == $retval) {
		write_log("{$name} service restarted");
	}	else {
		write_log("Failed to restart service {$name}");
	}
	return $retval;
}

// Stop service.
// Return 0 if successful, otherwise 1.
function rc_stop_service($name) {
	// Execute script.
	$retval = rc_exec_script("/etc/rc.d/{$name} stop");
	if (0 == $retval) {
		write_log("{$name} service stopped");
	}	else {
		write_log("Failed to stop service {$name}");
	}
	return $retval;
}

// Update service (Modify rc.conf and execute rc script).
// Return 0 if successful, otherwise 1.
function rc_update_service($name) {
	$retval = 0;

	// Check if service is running.
	$running = rc_is_service_running($name);

	// Check if service is enabled.
	$enabled = rc_is_service_enabled($name);

	// Update rc.conf and execute rc script.
	if (0 == $enabled) {
		rc_update_rcconf($name, "enable");

		switch ($running) {
			case 0:
				$retval = rc_restart_service($name);
				break;
			case 1:
				$retval = rc_start_service($name);
				break;
		}
	} else {
		// Stop service if necessary.
		if (0 == $running) {
			$retval = rc_stop_service($name);
		}

		rc_update_rcconf($name, "disable");
	}

	return $retval;
}

// Update /etc/rc.conf file.
// Check if KEYWORD 'RCVAR' is defined (e.g. '# RCVAR: xxxx').
function rc_update_rcconf($name,$state) {
	$data = file_get_contents("/etc/rc.d/$name");
	$search = "/RCVAR: (.*)/";
	if (!preg_match($search, $data, $rcvar)) {
		return 0;
	}

	// Update /etc/rc.conf.
	$retval = mwexec("/usr/local/sbin/rconf service {$state} {$rcvar[1]}");

	return $retval;
}

// Check if service is enabled.
// Use the KEYWORD 'XQUERY' to determine if service is enabled or not.
// Return 0 if service is enabled (default), otherwise 1.
function rc_is_service_enabled($name) {
	global $g;

	$data = file_get_contents("/etc/rc.d/$name");
	$search = "/XQUERY: (.*)/";
	if (!preg_match($search, $data, $xquery)) {
		return 0;
	}

	// Execute query.
	exec("/usr/local/bin/xml sel -t {$xquery[1]} {$g['conf_path']}/config.xml",$result);

	return ("0" === $result[0]) ? 0 : 1;
}
?>
