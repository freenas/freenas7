<?php
$pgtitle = array("System", "General setup");

require_once("guicore.inc");
require_once("util.inc");

$pconfig['hostname'] = $config['system']['hostname'];
$pconfig['domain'] = $config['system']['domain'];
list($pconfig['dns1'],$pconfig['dns2']) = get_ipv4dnsserver();
list($pconfig['ipv6dns1'],$pconfig['ipv6dns2']) = get_ipv6dnsserver();
$pconfig['username'] = $config['system']['username'];
if (!$pconfig['username'])
	$pconfig['username'] = "admin";
$pconfig['webguiproto'] = $config['system']['webgui']['protocol'];
if (!$pconfig['webguiproto'])
	$pconfig['webguiproto'] = "http";
$pconfig['webguiport'] = $config['system']['webgui']['port'];
$pconfig['language'] = $config['system']['language'];
$pconfig['timezone'] = $config['system']['timezone'];
$pconfig['timeupdateinterval'] = $config['system']['time-update-interval'];
$pconfig['timeservers'] = $config['system']['timeservers'];
$pconfig['language'] = $config['system']['language'];
if (!$pconfig['language'])
	$pconfig['language'] = "English";

if (!isset($pconfig['timeupdateinterval']))
	$pconfig['timeupdateinterval'] = 300;
if (!$pconfig['timezone'])
	$pconfig['timezone'] = "Etc/UTC";
if (!$pconfig['timeservers'])
	$pconfig['timeservers'] = "pool.ntp.org";

if ($_POST) {
	unset($input_errors);
	$pconfig = $_POST;

	/* input validation */
	$reqdfields = split(" ", "hostname domain username");
	$reqdfieldsn = array(gettext("Hostname"),gettext("Domain"),gettext("Username"));

	do_input_validation($_POST, $reqdfields, $reqdfieldsn, &$input_errors);

	if ($_POST['hostname'] && !is_hostname($_POST['hostname'])) {
		$input_errors[] = gettext("The hostname may only contain the characters a-z, 0-9 and '-'.");
	}
	if ($_POST['domain'] && !is_domain($_POST['domain'])) {
		$input_errors[] = gettext("The domain may only contain the characters a-z, 0-9, '-' and '.'.");
	}
	if (($_POST['dns1'] && !is_ipv4addr($_POST['dns1'])) || ($_POST['dns2'] && !is_ipv4addr($_POST['dns2']))) {
		$input_errors[] = gettext("A valid IPv4 address must be specified for the primary/secondary DNS server.");
	}
	if (($_POST['ipv6dns1'] && !is_ipv6addr($_POST['ipv6dns1'])) || ($_POST['ipv6dns2'] && !is_ipv6addr($_POST['ipv6dns2']))) {
		$input_errors[] = gettext("A valid IPv6 address must be specified for the primary/secondary DNS server.");
	}
	if ($_POST['username'] && !preg_match("/^[a-zA-Z0-9]*$/", $_POST['username'])) {
		$input_errors[] = gettext("The username may only contain the characters a-z, A-Z and 0-9.");
	}
	if ($_POST['webguiport'] && (!is_numericint($_POST['webguiport']) ||
			($_POST['webguiport'] < 1) || ($_POST['webguiport'] > 65535))) {
		$input_errors[] = gettext("A valid TCP/IP port must be specified for the webGUI port.");
	}
	if (($_POST['password']) && ($_POST['password'] != $_POST['password2'])) {
		$input_errors[] = gettext("The passwords do not match.");
	}

	$t = (int)$_POST['timeupdateinterval'];
	if (($t < 0) || (($t > 0) && ($t < 6)) || ($t > 1440)) {
		$input_errors[] = gettext("The time update interval must be either 0 (disabled) or between 6 and 1440.");
	}

	foreach (explode(' ', $_POST['timeservers']) as $ts) {
		if (!is_domain($ts)) {
			$input_errors[] = gettext("A NTP Time Server name may only contain the characters a-z, 0-9, '-' and '.'.");
		}
	}

	if (!$input_errors) {
		$config['system']['hostname'] = strtolower($_POST['hostname']);
		$config['system']['domain'] = strtolower($_POST['domain']);
		$oldwebguiproto = $config['system']['webgui']['protocol'];
		$config['system']['username'] = $_POST['username'];
		$config['system']['webgui']['protocol'] = $pconfig['webguiproto'];
		$oldwebguiport = $config['system']['webgui']['port'];
		$config['system']['webgui']['port'] = $pconfig['webguiport'];
		$config['system']['language'] = $_POST['language'];
		$config['system']['timezone'] = $_POST['timezone'];
		$config['system']['timeservers'] = strtolower($_POST['timeservers']);
		$config['system']['time-update-interval'] = $_POST['timeupdateinterval'];

		unset($config['system']['dnsserver']);
		// Only store IPv4 DNS servers when using static IPv4.
		if ("dhcp" !== $config['interfaces']['lan']['ipaddr']) {
			if ($_POST['dns1'])
				$config['system']['dnsserver'][] = $_POST['dns1'];
			if ($_POST['dns2'])
				$config['system']['dnsserver'][] = $_POST['dns2'];
		}
		// Only store IPv6 DNS servers when using static IPv6.
		if ("auto" !== $config['interfaces']['lan']['ipv6addr']) {
			if ($_POST['ipv6dns1'])
				$config['system']['ipv6dnsserver'][] = $_POST['ipv6dns1'];
			if ($_POST['ipv6dns2'])
				$config['system']['ipv6dnsserver'][] = $_POST['ipv6dns2'];
		}

		$olddnsallowoverride = $config['system']['dnsallowoverride'];
		$config['system']['dnsallowoverride'] = $_POST['dnsallowoverride'] ? true : false;

		if ($_POST['password']) {
			$config['system']['password'] = crypt($_POST['password']);
		}

		write_config();

		if (($oldwebguiproto != $config['system']['webgui']['protocol']) ||
			($oldwebguiport != $config['system']['webgui']['port']))
			touch($d_sysrebootreqd_path);

		$retval = 0;

		if (!file_exists($d_sysrebootreqd_path)) {
			config_lock();
			$retval |= rc_exec_service("rcconf.sh"); // Update /etc/rc.conf
			$retval |= rc_exec_service("resolv"); // Update /etc/resolv
			$retval |= rc_exec_service("hosts"); // Update /etc/hosts
			$retval |= rc_restart_service("hostname"); // Set hostname
			$retval |= rc_exec_service("userdb");
			$retval |= rc_exec_service("htpasswd");
			$retval |= rc_exec_service("timezone");
 			$retval |= rc_update_service("msntp");
 			$retval |= rc_update_service("mdnsresponder");
			config_unlock();
		}

		if (($pconfig['systime'] != "Not Set") && ($pconfig['systime'] != "")) {
			$timefields = split(" ", $pconfig['systime']);
			$dateparts = split("/", $timefields[0]);
			$timeparts = split(":", $timefields[1]);
			$newsystime = substr($dateparts[2],-2).substr("0".$dateparts[0],-2).substr("0".$dateparts[1],-2);
			$newsystime = $newsystime.substr("0".$timeparts[0],-2).substr("0".$timeparts[1],-2);

			// The date utility exits 0 on success, 1 if unable to set the date,
			// and 2 if able to set the local date, but unable to set it globally.
			$retval |= mwexec("/bin/date -n {$newsystime}");

			$pconfig['systime']="Not Set";
		}

		$savemsg = get_std_save_message($retval);

		// Update DNS server controls.
		list($pconfig['dns1'],$pconfig['dns2']) = get_ipv4dnsserver();
		list($pconfig['ipv6dns1'],$pconfig['ipv6dns2']) = get_ipv6dnsserver();
	}
}

$webgui->assign("pagedata_hostname", $pconfig['hostname']);
$webgui->assign("pagedata_domain", $pconfig['domain']);
$webgui->assign("pagedata_dns1", $pconfig['dns1']);
$webgui->assign("pagedata_dns2", $pconfig['dns2']);
$webgui->assign("pagedata_dns_enable", "dhcp" != $config['interfaces']['lan']['ipaddr']);
$webgui->assign("pagedata_ipv6dns1", $pconfig['ipv6dns1']);
$webgui->assign("pagedata_ipv6dns2", $pconfig['ipv6dns2']);
$webgui->assign("pagedata_ipv6dns_enable", "auto" != $config['interfaces']['lan']['ipv6addr']);
$webgui->assign("pagedata_username", $pconfig['username']);
$webgui->assign("pagedata_webguiproto", $pconfig['webguiproto']);

$webgui->display('system.tpl');
?>
