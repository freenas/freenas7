<?php
/*
	rc.inc
	Copyright © 2007 Volker Theile (votdev@gmx.de)
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
require_once("util.inc");

// !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
// !! ATTENTION:
// !! It seems to be impossible to call a rc init script that
// !! creates a conf file using 'create_conf_file' command.
// !! A workaround is to call start/restart a service with
// !! the additional parameter 'noconf'. The configuration file
// !! has to be created by hand. Example for WebGUI:
// !!
// !! $retval |= services_create_pureftpd_conf();
// !! $retval |= rc_update_service("pureftpd",isset($config['ftp']['enable']));
// !!
// !! It seems to be a problem with the lighttpd cgi modul.
// !!
// !! Scenarios:
// !! OK    => console -> rc init script (/etc/rc.d/xxx) ->
// !!          rc init php wrapper (/etc/rc.d.php/xxx)
// !!
// !! FAILS => WebGUI (lighttpd/mod_cgi) -> rc init script (/etc/rc.d/xxx) ->
// !!          rc init php wrapper (/etc/rc.d.php/xxx)
// !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!

// Services settings.
// Register new services by adding them to this array:
// start_func      - Function executed before service is started.
// stop_func       - Function executed before service is stopped.
// is_enabled_func - Function to test if service is enabled
$g_rcservices = array();
$g_rcservices['afpd']            = array("start_func" => "services_create_afpd_conf", "stop_func"  => "", "is_enabled_func" => "global \$config; \$enabled = isset(\$config['afp']['enable']);", "set_rcvar" => "true");
$g_rcservices['inadyn']          = array("start_func" => "services_create_inadyn_conf", "stop_func"  => "", "is_enabled_func" => "global \$config; \$enabled = isset(\$config['dynamicdns']['enable']);", "set_rcvar" => "true");
$g_rcservices['iscsi_target']    = array("start_func" => "", "stop_func"  => "", "is_enabled_func" => "global \$config; \$enabled = is_array(\$config['iscsitarget']['vdisk']);", "set_rcvar" => "true");
$g_rcservices['iscsi_initiator'] = array("start_func" => "", "stop_func"  => "", "is_enabled_func" => "global \$config; \$enabled = is_array(\$config['iscsiinit']['vdisk']);", "set_rcvar" => "true");
$g_rcservices['lighttpd']        = array("start_func" => "services_create_lighttpd_conf", "stop_func"  => "", "is_enabled_func" => "\$enabled = true;", "set_rcvar" => "true");
$g_rcservices['mdnsresponder']   = array("start_func" => "services_create_mdnsresponder_conf", "stop_func"  => "", "is_enabled_func" => "global \$config; \$enabled = isset(\$config['system']['zeroconf']);", "set_rcvar" => "true");
$g_rcservices['pureftpd']        = array("start_func" => "services_create_pureftpd_conf", "stop_func"  => "", "is_enabled_func" => "global \$config; \$enabled = isset(\$config['ftp']['enable']);", "set_rcvar" => "true");
$g_rcservices['snmpd']           = array("start_func" => "services_create_snmpd_conf", "stop_func"  => "", "is_enabled_func" => "global \$config; \$enabled = isset(\$config['snmpd']['enable']);", "set_rcvar" => "true");
$g_rcservices['ushare']          = array("start_func" => "services_create_ushare_conf", "stop_func"  => "", "is_enabled_func" => "global \$config; \$enabled = isset(\$config['upnp']['enable']);", "set_rcvar" => "true");
$g_rcservices['sshd']            = array("start_func" => "services_create_sshd_conf", "stop_func"  => "", "is_enabled_func" => "global \$config; \$enabled = isset(\$config['sshd']['enable']);", "set_rcvar" => "true");
$g_rcservices['rsyncd']          = array("start_func" => "services_create_rsyncd_conf", "stop_func"  => "", "is_enabled_func" => "global \$config; \$enabled = isset(\$config['rsyncd']['enable']);", "set_rcvar" => "true");
$g_rcservices['samba']           = array("start_func" => "services_create_samba_conf", "stop_func"  => "", "is_enabled_func" => "global \$config; \$enabled = isset(\$config['samba']['enable']);", "set_rcvar" => "true");
$g_rcservices['hosts']           = array("start_func" => "system_create_hosts_conf", "stop_func"  => "", "is_enabled_func" => "\$enabled = true;", "set_rcvar" => "false");
$g_rcservices['rcconf']          = array("start_func" => "system_create_rc_conf", "stop_func"  => "", "is_enabled_func" => "\$enabled = true;", "set_rcvar" => "false");
$g_rcservices['resolv']          = array("start_func" => "system_create_resolv_conf", "stop_func"  => "", "is_enabled_func" => "\$enabled = true;", "set_rcvar" => "false");
$g_rcservices['smartd']          = array("start_func" => "", "stop_func"  => "", "is_enabled_func" => "global \$config; \$enabled = isset(\$config['system']['smart']);", "set_rcvar" => "true");
$g_rcservices['powerd']          = array("start_func" => "", "stop_func"  => "", "is_enabled_func" => "global \$config; \$enabled = isset(\$config['system']['powerd']);", "set_rcvar" => "true");
$g_rcservices['systune']         = array("start_func" => "", "stop_func"  => "", "is_enabled_func" => "global \$config; \$enabled = isset(\$config['system']['tune']);", "set_rcvar" => "true");

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

// Check if service is enabled.
// Looks for xxx_enabled="YES|NO" in rc.conf.
// Return 0 if service is enabled, otherwise 1.
function rc_is_service_enabled($name) {
	$retval = mwexec("/usr/local/sbin/rconf service rcvar {$name}");
	return $retval;
}

// Check if service is running.
// Return 0 if service is running, otherwise 1.
function rc_is_service_running($name) {
	$retval = rc_exec_script("/etc/rc.d/{$name} status");
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
	global $g_rcservices;

	// Execute service function.
	if ($g_rcservices[$name] && is_callable($g_rcservices[$name]['start_func']))
		call_user_func($g_rcservices[$name]['start_func']);

	// Execute script.
	$retval = rc_exec_script("/etc/rc.d/{$name} start noconf");
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
	global $g_rcservices;

	// Execute service function.
	if ($g_rcservices[$name] && is_callable($g_rcservices[$name]['start_func']))
		call_user_func($g_rcservices[$name]['start_func']);

	// Execute script.
	$retval = rc_exec_script("/etc/rc.d/{$name} restart noconf");
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
	global $g_rcservices;

	// Execute service function.
	if ($g_rcservices[$name] && is_callable($g_rcservices[$name]['stop_func']))
		call_user_func($g_rcservices[$name]['stop_func']);

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
	global $g_rcservices;

	$retval = 0;

	// Check if service is running.
	$running = rc_is_service_running($name);

	// Check if service is enabled.
	$enabled = false;
	if ($g_rcservices[$name])
		eval($g_rcservices[$name]['is_enabled_func']);

	// Update rc.conf and execute rc script.
	if ($enabled) {
		// Update rc.conf.
		mwexec("/usr/local/sbin/rconf service enable {$name}");

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

		// Update rc.conf.
		mwexec("/usr/local/sbin/rconf service disable {$name}");
	}

	return $retval;
}
?>
