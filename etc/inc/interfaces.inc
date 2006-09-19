<?php
/*
	interfaces.inc
	part of FreeNAS (http://www.freenas.org)
	Copyright (C) 2005-2006 Olivier Cochard-Labbé <olivier@freenas.org>.
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

/* include all configuration functions */
require_once("functions.inc");
/* include utilities */
require_once("util.inc");

function interfaces_loopback_configure()
{
	global $config, $g;

	mwexec("/sbin/ifconfig lo0 127.0.0.1");
	
	return 0;
}

function interfaces_lan_configure()
{
	global $config, $g;
	
	if ($g['booting'])
		echo "Configuring LAN interface... ";
	
	$lancfg = $config['interfaces']['lan'];
	
	$lanif = $config['interfaces']['lan']['if'];
	
	// $config['interfaces']['lan'] is allready set to the first detected NIC... Where ?
	
	if (strcmp($lanif,"auto") == 0) {
		$interfaces = explode(" ", exec("/sbin/ifconfig -l"));
		$lanif = $interfaces[0];
		if ($g['booting'])
			//echo "Using interface $lanif... ";
			echo "Auto-detected LAN interface $lanif and saving config file...\n";
		
		// TEMPO FIX: Try to replace the 'auto' value with the interface detected 
		// It's more easy to code with a real name that using the 'auto' feature)
		
		// $config['interfaces']['lan']['if'] = $lanif;
		// $lancfg['if'] = $lanif;
		
		// Warning: Must test with CDROM if not create problem
		
		// write_config();
		
	}
	
	/* wireless configuration? */
	if (is_array($lancfg['wireless']))
		interfaces_wireless_configure($lanif, $lancfg['wireless']);
	
	/* MAC spoofing? */
	if ($lancfg['spoofmac'])
		mwexec("/sbin/ifconfig " . escapeshellarg($lanif) . 
			" link " . escapeshellarg($lancfg['spoofmac']));
	
	/* media */
	if ($lancfg['media'] || $lancfg['mediaopt'] || $lancfg['polling'] || $lancfg['mtu'] )
	{
		$cmd = "/sbin/ifconfig " . escapeshellarg($lanif);
		
		// If not in autoselect mode, force it:
		if (strcmp($lancfg['media'],"autoselect") != 0) {
			$cmd .= " media " . escapeshellarg($lancfg['media']);
			$cmd .= " mediaopt " . escapeshellarg($lancfg['mediaopt']);
		}
		
		if ($lancfg['polling'])
			$cmd .= " polling";
		if ($lancfg['mtu'])
			$cmd .= " mtu ". escapeshellarg($lancfg['mtu']);
		mwexec($cmd);
	}

  switch ($lancfg['ipaddr']) {
		case 'dhcp':
			// Try to fix the 'auto' interface name and IP address configuration
			// interfaces_lan_dhcp_configure( $lancfg['if']);
			interfaces_lan_dhcp_configure($lanif);
			break;
			
		default:
      $addflags = "";
      /* Remove the link0 options that create some problem */
      /* if (strpos($lanif, "fxp") !== false)
        $addflags .= " link0"; */
      
      mwexec("/sbin/ifconfig " . escapeshellarg($lanif) . " " . 
      escapeshellarg($lancfg['ipaddr'] . "/" . $lancfg['subnet']) . $addflags);
	}
	
	/* install default route */
	mwexec("/sbin/route delete default");
	mwexec("/sbin/route add default " . escapeshellarg($lancfg['gateway']));
	
	/* install multicast route */
	
	mwexec("/sbin/route add -net 224.0.0.0 -interface " . escapeshellarg($lanif));
	
	if (!$g['booting'])
	{
		/* make new hosts file */
		system_hosts_generate();
		
		/* reconfigure static routes (kernel may have deleted them) */
		system_routing_configure();
		
		/* reload Samba */
		services_samba_configure();
		
		/* reload FTP */
		services_ftpd_configure();
		
		/* reload NFS */
		services_nfs_configure();
		
		/* reload AFP */
		services_afpd_configure();
		
		/* reload SSH */
		services_sshd_configure();
		
		/* reload RSYNC */
		services_rsyncd_configure();
		
		/* reload webgui */
		system_webgui_start();
				
	}
	
	if ($g['booting'])
		echo "done\n";
	
	return 0;
}

function interfaces_optional_configure()
{
	global $config, $g;
	
	/* Reset bridge configuration.	Interfaces will add to it. */
	$bridgeconfig = "";
	
	for ($i = 1; isset($config['interfaces']['opt' . $i]); $i++)
	{
		interfaces_optional_configure_if($i);
	}
		
	if (!$g['booting'])
	{
		/* reconfigure static routes (kernel may have deleted them) */
		system_routing_configure();
		
		/* restart dnsmasq */
		/* services_dnsmasq_configure();*/
	}
	
	return 0;
}

function interfaces_optional_configure_if($opti)
{
	global $config, $g;
	global $bridgeconfig;
	
	$optcfg = $config['interfaces']['opt' . $opti];
	
	if ($g['booting'])
	{
		$optdescr = "";
		if ($optcfg['descr'])
			$optdescr = " ({$optcfg['descr']})";
		echo "Configuring OPT{$opti}{$optdescr} interface... ";
	}
	
	if (isset($optcfg['enable']))
	{
		/* wireless configuration? */
		if (is_array($optcfg['wireless']))
			interfaces_wireless_configure($optcfg['if'], $optcfg['wireless']);
		
		/* MAC spoofing? */
		if ($optcfg['spoofmac'])
			mwexec("/sbin/ifconfig " . escapeshellarg($optcfg['if']) . 
				" link " . escapeshellarg($optcfg['spoofmac']));
		
		/* media */
		if ($optcfg['media'] || $optcfg['mediaopt'] || $optcfg['polling'] || $optcfg['mtu'])
		{
			$cmd = "/sbin/ifconfig " . escapeshellarg($optcfg['if']);
							
			
			// If not in autoselect mode, force it:
			if (strcmp($optcfg['media'],"autoselect") != 0) {
				$cmd .= " media " . escapeshellarg($optcfg['media']);
				$cmd .= " mediaopt " . escapeshellarg($optcfg['mediaopt']);
			}
		
			if ($optcfg['polling'])
				$cmd .= " polling ";
			if ($optcfg['mtu'])
				$cmd .= " mtu ". escapeshellarg($optcfg['mtu']);
			mwexec($cmd);
		}
	
		$addflags = "";
		if (strpos($optcfg['if'], "fxp") !== false)
			$addflags .= " link0";
		
				
		mwexec("/sbin/ifconfig " . escapeshellarg($optcfg['if']) . " " . 
		escapeshellarg($optcfg['ipaddr'] . "/" . $optcfg['subnet']) . $addflags);
		
		/* install multicast route */
	
		mwexec("/sbin/route add -net 224.0.0.0 -interface " . escapeshellarg($optcfg['if']));
		
	}
	else
	{
		mwexec("/sbin/ifconfig " . escapeshellarg($optcfg['if']) . 
			" delete down");
	}
	
	if ($g['booting'])
		echo "done\n";
	
	return 0;
}

function interfaces_wireless_configure($if, $wlcfg)
{
	global $config, $g;
	
	/* wireless configuration */
	$ifcargs = escapeshellarg($if) . 
		" ssid " . escapeshellarg($wlcfg['ssid']) . " channel " . 
		escapeshellarg($wlcfg['channel']) . " ";
	
	if ($wlcfg['stationname'])
		$ifcargs .= "stationname " . escapeshellarg($wlcfg['stationname']) . " ";
	
	if (isset($wlcfg['wep']['enable']) && is_array($wlcfg['wep']['key']))
	{
		$ifcargs .= "wepmode on ";
		
		$i = 1;
		foreach ($wlcfg['wep']['key'] as $wepkey)
		{
			$ifcargs .= "wepkey " . escapeshellarg("{$i}:{$wepkey['value']}") . " ";
			if (isset($wepkey['txkey']))
				$ifcargs .= "weptxkey {$i} ";
			$i++;
		}
	}
	else
	{
		$ifcargs .= "wepmode off ";
	}
	
	if (strstr($if, "ath"))
	{
		if ($wlcfg['standard'])
			$ifcargs .= "mode {$wlcfg['standard']} ";
	}
	
	switch ($wlcfg['mode'])
	{
		case 'hostap':
			if (strstr($if, "ath"))
				$ifcargs .= "-mediaopt adhoc mediaopt hostap ";
			else if (strstr($if, "wi"))
				$ifcargs .= "-mediaopt ibss mediaopt hostap ";
			break;
		case 'ibss':
		case 'IBSS':
			if (strstr($if, "ath"))
				$ifcargs .= "-mediaopt hostap mediaopt adhoc ";
			else if (strstr($if, "wi"))
				$ifcargs .= "-mediaopt hostap mediaopt ibss ";
			else if (strstr($if, "an"))
				$ifcargs .= "mediaopt adhoc ";
			break;
		case 'bss':
		case 'BSS':
			if (strstr($if, "ath"))
				$ifcargs .= "-mediaopt hostap -mediaopt adhoc ";
			else if (strstr($if, "wi"))
				$ifcargs .= "-mediaopt hostap -mediaopt ibss ";
			else if (strstr($if, "an"))
				$ifcargs .= "-mediaopt adhoc ";
			break;
	}
	
	$ifcargs .= "up";
	
	mwexec("/sbin/ifconfig " . $ifcargs);
	
	return 0;
}

function interfaces_lan_dhcp_configure($if)
{
    global $config, $g;

    /* generate dhclient.conf */
    $fd = fopen("{$g['varetc_path']}/dhclient.conf", "w");
    if (!$fd) {
        printf("Error: cannot open dhclient.conf in interfaces_lan_dhcp_configure().\n");
        return 1;
    }

     unset($dhclientconf);
     $macaddr = get_macaddr($if);

     if ($config['system']['hostname']) {
        $dhclientconf .= <<<EOD
send dhcp-client-identifier "{$macaddr}";
interface "{$if}" {
    send host-name "{$config['system']['hostname']}.{$config['system']['domain']}";
}

EOD;
    }

    fwrite($fd, $dhclientconf);
    fclose($fd);

    /* fire up dhclient */
    mwexec("/sbin/dhclient -c {$g['varetc_path']}/dhclient.conf " .
        escapeshellarg($if));

    return 0;
}
?>
