<?php
/*
	interfaces.inc
	part of FreeNAS (http://www.freenas.org)
	Copyright (C) 2005-2008 Olivier Cochard-Labbe <olivier@freenas.org>.
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
require_once("rc.inc");

function interfaces_loopback_configure()
{
	mwexec("/sbin/ifconfig lo0 127.0.0.1");
	return 0;
}

function interfaces_lan_configure()
{
	global $config;

	$result = 0;

	if (is_booting())
		write_console("Starting network: ");

	$lancfg = $config['interfaces']['lan'];
	$lanif = $config['interfaces']['lan']['if'];

	// Get interface name.
	if (0 == strcmp($lanif, "auto")) {
		$lanif = get_ifname($lanif);
		if (is_booting()) {
			write_console("Using interface {$lanif}\n");
		}
	}

	// Wireless configuration?
	if (is_array($lancfg['wireless'])) {
		interfaces_wireless_configure($lanif, $lancfg['wireless']);
	}

	// Default configuration.
	$cmd = "/sbin/ifconfig " . escapeshellarg($lanif) . " media " . escapeshellarg($lancfg['media']);
	if (0 != strcmp($lancfg['media'], "autoselect")) {
		$cmd .= " mediaopt " . escapeshellarg($lancfg['mediaopt']);
	}
	if (isset($lancfg['polling'])) {
		$cmd .= " polling";
	}
	if ($lancfg['mtu']) {
		$cmd .= " mtu ". escapeshellarg($lancfg['mtu']);
	}
	if (false !== strpos($lanif, "fxp")) {
		$cmd .= " link0";
	}
	mwexec($cmd, true);

	switch ($lancfg['ipaddr']) {
		case 'dhcp':
			interfaces_dhcp_configure($lanif);
			break;

		default:
			mwexec("/sbin/ifconfig " . escapeshellarg($lanif) . " " . escapeshellarg($lancfg['ipaddr'] . "/" . $lancfg['subnet']));

			// Install default route.
			if ($lancfg['gateway']) {
				mwexec("/sbin/route delete default");
				mwexec("/sbin/route add default " . escapeshellarg($lancfg['gateway']));
			}
			break;
	}

	// IPv6: disallow "internal" addresses to appear on the wire.
	// These two line are taken from /etc/rc.d/network_ipv6 script.
	mwexec("/sbin/route add -inet6 ::ffff:0.0.0.0 -prefixlen 96 ::1 -reject");
	mwexec("/sbin/route add -inet6 ::0.0.0.0 -prefixlen 96 ::1 -reject");

	// Disable IPv6 forwarding (should already be done).
	mwexec("/sbin/sysctl net.inet6.ip6.forwarding=0");

	switch ($lancfg['ipv6addr']) {
		case 'auto':
			interfaces_auto_configure($lanif);
			break;

		default:
			mwexec("/sbin/ifconfig " . escapeshellarg($lanif) . " inet6 alias " . escapeshellarg($lancfg['ipv6addr'] . "/" . $lancfg['ipv6subnet']));

			// Install default route.
			if ($lancfg['ipv6gateway']) {
				mwexec("/sbin/route delete -inet6 default");
				mwexec("/sbin/route add -inet6 default " . escapeshellarg($lancfg['ipv6gateway']));
			}
			break;
	}

	// Install multicast route (for UPnP).
	mwexec("/sbin/route add -net 224.0.0.0 -interface " . escapeshellarg($lanif));

	if (!is_booting()) {
		/* Update /etc/rc.conf */
		$result = rc_exec_service("rcconf.sh");

		/* Update /etc/resolv */
		$result |= rc_exec_service("resolv");

		/* Make new /etc/hosts */
		$result |= rc_exec_service("hosts");

		/* Reconfigure static routes */
		$result |= rc_update_service("routing");

		/* Restart Samba */
		$result |= rc_update_service("samba");

		/* Restart FTP */
		$result |= rc_restart_service("pureftpd");

		/* Restart NFS */
		$result |= rc_update_service("rpcbind"); // !!! Do
		$result |= rc_update_service("mountd");  // !!! not
		$result |= rc_update_service("nfsd");    // !!! change
		$result |= rc_update_service("statd");   // !!! this
		$result |= rc_update_service("lockd");   // !!! order

		/* Restart AFP */
		$result |= rc_restart_service("afpd");

		/* Restart SSH */
		$result |= rc_restart_service("sshd");

		/* Restart RSYNC */
		$result |= rc_update_service("rsyncd");

		/* Restart UPnP */
		$result |= rc_restart_service("mediatomb.sh");

		/* Restart mdnsresponder (Zeroconf/Apple Bonjour) */
		$result |= rc_restart_service("mdnsresponder");
	}

	return $result;
}

function interfaces_optional_configure()
{
	global $config;

	for ($i = 1; isset($config['interfaces']['opt' . $i]); $i++) {
		interfaces_optional_configure_if($i);
	}

	if (!is_booting()) {
		/* Reconfigure static routes */
		rc_update_service("routing");
	}

	return 0;
}

function interfaces_optional_configure_if($opti)
{
	global $config;

	$optcfg = $config['interfaces']['opt' . $opti];

	if (is_booting()) {
		$optdescr = "";
		if ($optcfg['descr'])
			$optdescr = " ({$optcfg['descr']})";
		write_console("Initializing OPT{$opti}{$optdescr} interface.\n");
	}

	if (isset($optcfg['enable'])) {
		/* wireless configuration? */
		if (is_array($optcfg['wireless']))
			interfaces_wireless_configure($optcfg['if'], $optcfg['wireless']);

		/* Default configuration. */
		$cmd = "/sbin/ifconfig " . escapeshellarg($optcfg['if']) . " media " . escapeshellarg($optcfg['media']);
		if (0 != strcmp($optcfg['media'], "autoselect")) {
			$cmd .= " mediaopt " . escapeshellarg($optcfg['mediaopt']);
		}
		if (isset($optcfg['polling'])) {
			$cmd .= " polling";
		}
		if ($optcfg['mtu']) {
			$cmd .= " mtu ". escapeshellarg($optcfg['mtu']);
		}
		if (false !== strpos($optcfg['if'], "fxp")) {
			$cmd .= " link0";
		}
		mwexec($cmd, true);

		switch ($optcfg['ipaddr']) {
			case 'dhcp':
				interfaces_dhcp_configure($optcfg['if']);
				break;

			default:
				mwexec("/sbin/ifconfig " . escapeshellarg($optcfg['if']) . " " . escapeshellarg($optcfg['ipaddr'] . "/" . $optcfg['subnet']));
				break;
		}

		// Install multicast route.
		mwexec("/sbin/route add -net 224.0.0.0 -interface " . escapeshellarg($optcfg['if']));
	} else 	{
		mwexec("/sbin/ifconfig " . escapeshellarg($optcfg['if']) . " delete down");
	}

	switch ($optcfg['ipv6addr']) {
		case 'auto':
			interfaces_auto_configure($optcfg['if']);
			break;

		default:
			mwexec("/sbin/ifconfig " . escapeshellarg($optcfg['if']) . " inet6 alias " . escapeshellarg($optcfg['ipv6addr'] . "/" . $optcfg['ipv6subnet']));

			// Install default route.
			if ($optcfg['ipv6gateway']) {
				mwexec("/sbin/route delete -inet6 default");
				mwexec("/sbin/route add -inet6 default " . escapeshellarg($optcfg['ipv6gateway']));
			}
			break;
	}

	return 0;
}

function interfaces_wireless_configure($if, $wlcfg)
{
	global $config;

	/* wireless configuration */
	$ifcargs = escapeshellarg($if) .
		" ssid " . escapeshellarg($wlcfg['ssid']) . " channel " .
		escapeshellarg($wlcfg['channel']) . " ";

	if ($wlcfg['stationname'])
		$ifcargs .= "stationname " . escapeshellarg($wlcfg['stationname']) . " ";

	if (isset($wlcfg['wep']['enable']) && is_array($wlcfg['wep']['key'])) {
		$ifcargs .= "wepmode on ";

		$i = 1;
		foreach ($wlcfg['wep']['key'] as $wepkey) {
			$ifcargs .= "wepkey " . escapeshellarg("{$i}:{$wepkey['value']}") . " ";
			if (isset($wepkey['txkey']))
				$ifcargs .= "weptxkey {$i} ";
			$i++;
		}
	} else 	{
		$ifcargs .= "wepmode off ";
	}

	if (strstr($if, "ath")) {
		if ($wlcfg['standard'])
			$ifcargs .= "mode {$wlcfg['standard']} ";
	}

	switch ($wlcfg['mode']) {
		case 'hostap':
			if (strstr($if, "ath")) {
				$ifcargs .= "-mediaopt adhoc mediaopt hostap ";
			} else if (strstr($if, "wi")) {
				$ifcargs .= "-mediaopt ibss mediaopt hostap ";
			}
			break;
		case 'ibss':
		case 'IBSS':
			if (strstr($if, "ath")) {
				$ifcargs .= "-mediaopt hostap mediaopt adhoc ";
			} else if (strstr($if, "wi")) {
				$ifcargs .= "-mediaopt hostap mediaopt ibss ";
			} else if (strstr($if, "an")) {
				$ifcargs .= "mediaopt adhoc ";
			}
			break;
		case 'bss':
		case 'BSS':
			if (strstr($if, "ath")) {
				$ifcargs .= "-mediaopt hostap -mediaopt adhoc ";
			} else if (strstr($if, "wi")) {
				$ifcargs .= "-mediaopt hostap -mediaopt ibss ";
			} else if (strstr($if, "an")) {
				$ifcargs .= "-mediaopt adhoc ";
			}
			break;
	}

	$ifcargs .= "up";

	mwexec("/sbin/ifconfig {$ifcargs}", true);

	return 0;
}

function interfaces_dhcp_configure($if)
{
    global $config, $g;

    /* generate dhclient.conf */
    $fd = fopen("{$g['varetc_path']}/dhclient_{$if}.conf", "w");
    if (!$fd) {
        write_console("Error: Cannot open dhclient_{$if}.conf in interfaces_dhcp_configure().\n");
        return 1;
    }

    $macaddr = get_macaddr($if);
    $hostname = "{$config['system']['hostname']}";

    $dhclientconf = <<<EOD
send dhcp-client-identifier "{$macaddr}";
interface "{$if}" {
    send host-name "{$hostname}";
    script "/sbin/dhclient-script";
}

EOD;

    fwrite($fd, $dhclientconf);
    fclose($fd);

    // Fire up dhclient.
    mwexec("/sbin/dhclient -c {$g['varetc_path']}/dhclient_{$if}.conf " . escapeshellarg($if));

    return 0;
}

function interfaces_auto_configure($if)
{
	/* Configure the kernel for receiving RA */
	mwexec("/sbin/sysctl net.inet6.ip6.accept_rtadv=1");

	/* fire up rtsol client */
	mwexec("/sbin/rtsol " . escapeshellarg($if));

	return 0;
}
?>
