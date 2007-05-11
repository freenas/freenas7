<?php
/*
	util.inc
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

/* kill a process by pid file */
function killbypid($pidfile) {
	sigkillbypid($pidfile, "TERM");
	unlink_if_exists($pidfile);
}

/* sigkill a process by pid file */
function sigkillbypid($pidfile, $sig) {
	if (file_exists($pidfile)) {
		mwexec("/bin/kill -s $sig `/bin/cat " . $pidfile . "`");
	}
}

/* kill a process by name */
function killbyname($procname) {
	return mwexec("/usr/bin/killall " . escapeshellarg($procname));
}

/* Force kill a process by name */
function forcekillbyname($procname) {
	return mwexec("/usr/bin/killall -9 " . escapeshellarg($procname));
}

/* return the subnet address given a host address and a subnet bit count */
function gen_subnet($ipaddr, $bits) {
	if (!is_ipaddr($ipaddr) || !is_numeric($bits))
		return "";
	
	return long2ip(ip2long($ipaddr) & gen_subnet_mask_long($bits));
}

/* return the highest (broadcast) address in the subnet given a host address and a subnet bit count */
function gen_subnet_max($ipaddr, $bits) {
	if (!is_ipaddr($ipaddr) || !is_numeric($bits))
		return "";
	
	return long2ip(ip2long($ipaddr) | ~gen_subnet_mask_long($bits));
}

/* returns a subnet mask (long given a bit count) */
function gen_subnet_mask_long($bits) {
	$sm = 0;
	for ($i = 0; $i < $bits; $i++) {
		$sm >>= 1;
		$sm |= 0x80000000;
	}
	return $sm;
}

/* same as above but returns a string */
function gen_subnet_mask($bits) {
	return long2ip(gen_subnet_mask_long($bits));
}

function is_numericint($arg) {
	return (preg_match("/[^0-9]/", $arg) ? false : true);
}

/* returns true if $ipaddr is a valid dotted IPv4 address */
function is_ipaddr($ipaddr) {
	if (!is_string($ipaddr))
		return false;
		
	$ip_long = ip2long($ipaddr);
	$ip_reverse = long2ip($ip_long);
 
	if ($ipaddr == $ip_reverse)
		return true;
	else
		return false;
}

/* returns true if $ipaddr is a valid dotted IPv4 address or an alias thereof */
function is_ipaddroralias($ipaddr) {
	
	global $aliastable;
	
	if (isset($aliastable[$ipaddr]) && is_ipaddr($aliastable[$ipaddr]))
		return true;
	else
		return is_ipaddr($ipaddr);
}

/* returns true if $ipaddr is a valid dotted IPv4 address or any alias */
function is_ipaddroranyalias($ipaddr) {
	
	global $aliastable;
	
	if (isset($aliastable[$ipaddr]))
		return true;
	else
		return is_ipaddr($ipaddr);
}

/* returns true if $subnet is a valid subnet in CIDR format */
function is_subnet($subnet) {
	if (!is_string($subnet))
		return false;
		
	list($hp,$np) = explode('/', $subnet);
	
	if (!is_ipaddr($hp))
		return false;
	
	if (!is_numeric($np) || ($np < 1) || ($np > 32))
		return false;
		
	return true;
}

/* returns true if $subnet is a valid subnet in CIDR format or an alias thereof */
function is_subnetoralias($subnet) {
	
	global $aliastable;
	
	if (isset($aliastable[$subnet]) && is_subnet($aliastable[$subnet]))
		return true;
	else
		return is_subnet($subnet);
}

/* returns true if $hostname is a valid hostname */
function is_hostname($hostname) {
	if (!is_string($hostname))
		return false;
		
	if (preg_match("/^[a-z0-9\-]+$/i", $hostname))
		return true;
	else
		return false;
}

/* returns true if $desc is a valid description (alphanum and space, _ , - , .)*/
function is_validdesc($desc)
{
	
	if (!is_string($desc))
		return false;
		
	if (preg_match("/^[A-Za-z0-9]([A-Za-z0-9_\-\.\s]*[A-Za-z0-9])*$/", $desc))
		return true;
	else
		return false;
}

/* returns true if $login is a valid login name (alphanum,dot, _ , -)*/
function is_validlogin($login)
{
	
	if (!is_string($login))
		return false;
		
	if (preg_match("/^[A-Za-z0-9]([A-Za-z0-9_\-\.\s]*[A-Za-z0-9])*$/", $login))
		return true;
	
	else
		return false;
}

/* returns true if $password is a valid password (not used of special : character)*/
function is_validpassword($password)
{
	
	if (!is_string($password))
		return false;
	
	if (strstr($password, ':'))
			return false;
	
	else
		return true;
}

/* returns true if $login is a valid share name (alphanum,dot, _ , -)*/
function is_validsharename($sharename)
{
	
	if (!is_string($sharename))
		return false;
		
	if (preg_match("/^[A-Za-z0-9]([A-Za-z0-9_\-\.\s]*[A-Za-z0-9])*$/", $sharename))
		return true;
	
	else
		return false;
}

/* returns true if $domain is a valid domain name */
function is_domain($domain) {
	if (!is_string($domain))
		return false;
		
	if (preg_match("/^([a-z0-9\-]+\.?)*$/i", $domain))
		return true;
	else
		return false;
}

/* Returns true if $workgroup is a valid workgroup name */
/* A workgroup name can contain up to 15 characters, including letters, numbers, */
/* and the following characters: ! @ # $ % ^ & ( ) _ - ; : ' " , . */
/* It cannot contain any spaces, and must begin with a letter or number. */
function is_workgroup($workgroup) {
	if (!is_string($workgroup))
		return false;

	if (preg_match("/^[\w\d]+[\w\d\!\@\#\$\%\^\&\(\)\_\-\;\:\'\"\,\.]*$/", $workgroup))
		return true;
	else
		return false;
}

/* returns true if $macaddr is a valid MAC address */
function is_macaddr($macaddr) {
	if (!is_string($macaddr))
		return false;
		
	$maca = explode(":", $macaddr);
	if (count($maca) != 6)
		return false;
	
	foreach ($maca as $macel) {
		if (($macel === "") || (strlen($macel) > 2))
			return false;
		if (preg_match("/[^0-9a-f]/i", $macel))
			return false;
	}
	
	return true;
}

/* returns true if $name is a valid name for an alias */
function is_validaliasname($name) {
	if (!is_string($name))
		return false;
	if (!preg_match("/[^a-zA-Z0-9]/", $name))
		return true;
	else
		return false;
}

/* returns true if $port is a valid TCP/UDP port */
function is_port($port) {
	if (!is_numericint($port))
		return false;
		
	if (($port < 1) || ($port > 65535))
		return false;
	else
		return true;
}

/* returns true if $mtu is a valid MTU size */
function is_mtu($mtu) {
	if (!is_numericint($mtu))
		return false;
		
	if (($mtu < 100) || ($mtu > 9000))
		return false;
	else
		return true;
}

function get_interface_list() {
	/* build interface list with netstat */
	exec("/usr/bin/netstat -inW -f link", $linkinfo);
	array_shift($linkinfo);
	
	$iflist = array();
	
	foreach ($linkinfo as $link) {
		$alink = preg_split("/\s+/", $link);
		$ifname = chop($alink[0]);
		
		if (substr($ifname, -1) == "*")
			$ifname = substr($ifname, 0, strlen($ifname) - 1);
		/* add the plip interface to be excluded too */
		if (!preg_match("/^(ppp|sl|gif|faith|lo|vlan|tun|plip)/", $ifname)) {
			$iflist[$ifname] = array();
			
			$iflist[$ifname]['mac'] = chop($alink[3]);
			$iflist[$ifname]['up'] = false;
			
			/* find out if the link on this interface is up */
			unset($ifinfo);
			exec("/sbin/ifconfig {$ifname}", $ifinfo);
			
			foreach ($ifinfo as $ifil) {
				if (preg_match("/status: (.*)$/", $ifil, $matches)) {
					if ($matches[1] == "active")
						$iflist[$ifname]['up'] = true;
					break;
				}
			}
		}
	}
	
	return $iflist;
}

// Get list of mounted disk
// example: Array
//	[0] => Array
//	    (
//        [mp] => /mnt/sharename
//        [mdisk] => ad0s1
//        [fullname] => /dev/ad0s1
//        [sharename] => sharename
//        [fsid] => file system ID
//      )
function get_mounts_list()
{
	$amountlist = array();
	$i = 0;

	// Get list of mount points.
	exec("/sbin/mount -v", $rawdata);

	foreach ($rawdata as $line) {
		$aline = explode(" ", $line);

		// Ignore various mount points.
		if ((chop($aline[0]) == "/dev/md0") || (chop($aline[0]) == "devfs") || (chop($aline[0]) == "/dev/fd0"))
			continue;

		// Get device path.
		$devicepath = chop($aline[0]);

		// Get the complete name /dev/devicename.
		$adevicepath = explode("/", $devicepath);
		$devicename = $adevicepath[2];

		// Test if it's a gvinum, gmirror, gconcat or gstripe device.
		if ((strcmp($devicename,"gvinum") == 0) ||
				(strcmp($devicename,"mirror") == 0) || (strcmp($devicename,"concat") == 0) || 
				(strcmp($devicename,"stripe") == 0) || (strcmp($devicename,"raid5") == 0)) {
			$devicename = $adevicepath[3];
		}

		// Get the mountpath.
		$mountpath = chop($aline[2]);

		// Get the sharename.
		$amountpath = explode("/", $mountpath);
		$sharename = $amountpath[2];

		// Get file system ID.
		$fsid = strtr(strrchr($line, "fsid"), array( "fsid " => "", ")" => ""));

		$amountlist[$i]['mp'] = $mountpath;
		$amountlist[$i]['mdisk'] = $devicename;
		$amountlist[$i]['fullname'] = $devicepath;
		$amountlist[$i]['sharename'] = $amountpath[2];
		$amountlist[$i]['fsid'] = $fsid;

		$i++;
	}

	return $amountlist;
}

// Return list of mounted use
// example: Array
//	[mount_point_name] => Array
//	    (
//        [capacity] => 48%
//        [used] => 2.4G
//        [avail] => 2.6G
//        [size] => 5.4G
//      )
function get_mount_use()
{
	global $config;

	exec("/bin/df -h",$rawdata);

	$result=array();

	foreach ($rawdata as $line) {
		/* separe the line by space*/
		$aline = preg_split("/[\s,]+/", $line);

		$tmp=explode("/",chop($aline[5]));
		$mounted_on=$tmp[2];
		$capacity = chop($aline[4]);
		$avail = chop($aline[3]);
		$used = chop($aline[2]);
		$size = chop($aline[1]);

		if (is_array($config['mounts']['mount'])) 	{
			foreach ($config['mounts']['mount'] as $mountcfg) {
				if (strcmp($mounted_on,$mountcfg['sharename']) == 0) {
					$result[$mounted_on] = array();
					$result[$mounted_on]['capacity']=$capacity;
					$result[$mounted_on]['avail']=$avail;
					$result[$mounted_on]['used']=$used;
					$result[$mounted_on]['size']=$size;
				}
			}
		}
		else
			return 0;		
	}

	return $result;
}

/* Return list of ALL software volume: gvinum, gmirror */
function get_sraid_disks_list()
{
	$disklist = array_merge((array)get_gvinum_disks_list(),(array)get_gmirror_disks_list(),(array)get_gconcat_disks_list(),(array)get_gstripe_disks_list(),(array)get_graid5_disks_list());
	return $disklist;
}

// Get list of software gvinum RAID disks.
//[volumename] => Array
//    (
//        [type] => Software RAID - gvinum
//        [desc] => UP
//        [size] => 6149 MB
//        [name] => VolumeName
//        [fullname] => /dev/gvinum/VolumeName
//    )
function get_gvinum_disks_list()
{
	/* Send the shell cmd and get the result */
	exec("/sbin/gvinum list",$rawdata);

	foreach ($rawdata as $line) {
		/* Split the line using the space as separator */
		$aline = preg_split("/\s+/", $line);

		/* Get the line that begin with the letter  'V' */
		if ($aline[0] != "V")
			continue ;

		$diskname = chop($aline[1]);

		$disklist[$diskname] = array();
		$disklist[$diskname]['name'] = $diskname;
		$disklist[$diskname]['fullname'] = "/dev/gvinum/" . $diskname;
		$disklist[$diskname]['type'] = "Software RAID - gvinum";
		$disklist[$diskname]['desc'] = $aline[3];

		/* Get diskinfo to get disk size */
		$diskinfo = disks_get_diskinfo($disklist[$diskname]['fullname']);
		$disklist[$diskname]['size'] = $diskinfo['mediasize_mbytes'] . "MB";
	}

	return $disklist;
}

// Get list of software gmirror RAID disks.
//[volumename] => Array
//    (
//        [type] => Software RAID - gmirror
//        [name] => volumename
//        [desc] => COMPLETE
//        [size] => 6149M
//        [fullname] => /dev/mirror/volumename
//    )
function get_gmirror_disks_list()
{
	/* Display all configured gmirror volume */
	exec("/sbin/gmirror list",$rawdata);

	$foundname = 0 ;

	foreach ($rawdata as $line) {
		/* Use space for break the line */
		$aline = preg_split("/\s+/", $line);

		// First Step: Getting the array name
		// look for this output:
		// Geom name: xxx
		if ( (strcmp($aline[0],"Geom") == 0) && (strcmp($aline[1],"name:") == 0) ) {
			$diskname = $aline[2];

			$disklist[$diskname] = array();
			$disklist[$diskname]['name'] = $diskname;
			$disklist[$diskname]['fullname'] = "/dev/mirror/" . $diskname;
			$disklist[$diskname]['type'] = "Software RAID - gmirror";
			
			/* Get diskinfo to get disk size */
			$diskinfo = disks_get_diskinfo($disklist[$diskname]['fullname']);
			$disklist[$diskname]['size'] = $diskinfo['mediasize_mbytes'] . "MB";

			$foundname = 1 ;
			continue ;
		}
		
		// Second Step: Getting the array status
		// look for this output:
		// State: COMPLETE
		if ( (strcmp($aline[0],"State:") == 0) && $foundname) {
			$disklist[$diskname]['desc'] = $aline[1];

			// init the check variable for the next RAID volume
			$foundname = 0 ;
			continue ;
		}
	}

	return $disklist;
}

// Get list of software gconcat RAID disks.
//[volumename] => Array
//    (
//        [type] => Software RAID - gconcat
//        [desc] => COMPLETE
//        [size] => 6149M
//        [name] => concat1
//        [fullname] => /dev/concat/concat1
//    )
function get_gconcat_disks_list()
{
	/* Display all configured gconcat volume */
	exec("/sbin/gconcat list",$rawdata);

	$foundname = 0 ;

	foreach ($rawdata as $line) {
		/* Use space for break the line */
		$aline = preg_split("/\s+/", $line);

		// First Step: Getting the array name
		// look for this output:
		// Geom name: xxx
		if ( (strcmp($aline[0],"Geom") == 0) && (strcmp($aline[1],"name:") == 0) ) {
			$diskname = $aline[2];

			$disklist[$diskname] = array();
			$disklist[$diskname]['name'] = $diskname;
			$disklist[$diskname]['fullname'] = "/dev/concat/" . $diskname;
			$disklist[$diskname]['type'] = "Software RAID - gconcat";
			
			/* Get diskinfo to get disk size */
			$diskinfo = disks_get_diskinfo($disklist[$diskname]['fullname']);
			$disklist[$diskname]['size'] = $diskinfo['mediasize_mbytes'] . "MB";

			$foundname = 1 ;
			continue ;
		}

		// Second Step: Getting the array status
		// look for this output:
		// State: UP
		if ( (strcmp($aline[0],"State:") == 0) && $foundname) {
			$disklist[$diskname]['desc'] = $aline[1];

			// init the check variable for the next RAID volume
			$foundname = 0 ;
			continue ;
		}
	}

	return $disklist;
}

// Get list of software gstripe RAID disks.
//[volumename] => Array
//    (
//        [type] => Software RAID - gstripe
//        [desc] => COMPLETE
//        [size] => 6149M
//        [name] => raid0
//        [fullname] => /dev/stripe/raid0
//    )
function get_gstripe_disks_list()
{
	/* Display all configured gstripe volume */
	exec("/sbin/gstripe list",$rawdata);

	$foundname = 0 ;

	foreach ($rawdata as $line) {
		/* Use space for break the line */
		$aline = preg_split("/\s+/", $line);

		// First Step: Getting the array name
		// look for this output:
		// Geom name: xxx
		if ( (strcmp($aline[0],"Geom") == 0) && (strcmp($aline[1],"name:") == 0) ) {
			$diskname = $aline[2];

			$disklist[$diskname] = array();
			$disklist[$diskname]['name'] = $diskname;
			$disklist[$diskname]['fullname'] = "/dev/stripe/" . $diskname;
			$disklist[$diskname]['type'] = "Software RAID - gstripe";

			/* Get diskinfo to get disk size */
			$diskinfo = disks_get_diskinfo($disklist[$diskname]['fullname']);
			$disklist[$diskname]['size'] = $diskinfo['mediasize_mbytes'] . "MB";

			$foundname = 1 ;
			continue ;
		}

		// Second Step: Getting the array status
		// look for this output:
		// State: UP
		if ( (strcmp($aline[0],"State:") == 0) && $foundname) {
			$disklist[$diskname]['desc'] = $aline[1];

			// init the check variable for the next RAID volume
			$foundname = 0 ;
			continue ;
		}
	}

	return $disklist;
}

// Get list of software graid5 RAID disks.
//[volumename] => Array
//    (
//        [type] => Software RAID - graid5
//        [desc] => COMPLETE
//        [size] => 6149M
//        [name] => BIG1
//        [fullname] => /dev/raid5/BIG1
//    )
function get_graid5_disks_list()
{
	/* Display all configured graid5 volume */
	exec("/sbin/graid5 list",$rawdata);

	$foundname = 0 ;

	foreach ($rawdata as $line) {
		/* Use space for break the line */
		$aline = preg_split("/\s+/", $line);

		// First Step: Getting the array name
		// look for this output:
		// Geom name: xxx
		if ( (strcmp($aline[0],"Geom") == 0) && (strcmp($aline[1],"name:") == 0) ) {
			$diskname = $aline[2];

			$disklist[$diskname] = array();
			$disklist[$diskname]['name'] = $diskname;
			$disklist[$diskname]['fullname'] = "/dev/raid5/" . $diskname;
			$disklist[$diskname]['type'] = "Software RAID - graid5";

			/* Get diskinfo to get disk size */
			$diskinfo = disks_get_diskinfo($disklist[$diskname]['fullname']);
			$disklist[$diskname]['size'] = $diskinfo['mediasize_mbytes'] . "MB";

			$foundname = 1 ;
			continue ;
		}

		// Second Step: Getting the array status
		// look for this output:
		// State: COMPLETE
		if ( (strcmp($aline[0],"State:") == 0) && $foundname) {
			$disklist[$diskname]['desc'] = $aline[1];

			// init the check variable for the next RAID volume
			$foundname = 0 ;
			continue ;
		}
	}

	return $disklist;
}

// Get list of ATA disks.
//[ad0] => Array
//    (
//        [type] => IDE
//        [desc] => QUANTUM FIREBALL EX6.4A/A0A.0D00
//        [size] => 6149MB
//        [name] => ad0
//        [fullname] => /dev/ad0
//    )
function get_ata_disks_list()
{
	$disklist = array();
	
	/******* Getting IDE disk informations *******/
	exec("/sbin/atacontrol list",$rawdata);

	foreach ($rawdata as $line) {
		/* Separe la ligne par les espace */
		$aline = preg_split("/\s+/", $line);

		/* Si ATA alors NEXT */
		if ($aline[0] == "ATA")
			continue ;

		$diskname = chop($aline[2]);

		/* Exlude CDROM (acdX) and Empty (no) */
		if (!preg_match("/^(acd)/", $diskname ) & $diskname != "no") {
			$disklist[$diskname] = array();
			$disklist[$diskname]['name'] = $diskname;
			$disklist[$diskname]['fullname'] = "/dev/" . $diskname;
			$disklist[$diskname]['type'] = "IDE";

			/* Match the description witch is include between < and > */
			preg_match("/.*\<([^>]*)>.*/",$line,$match);
			$disklist[$diskname]['desc'] = $match[1];

			/* Get diskinfo to get disk size */
			$diskinfo = disks_get_diskinfo($diskname);
			$disklist[$diskname]['size'] = $diskinfo['mediasize_mbytes'] . "MB";
		}
	}

	return $disklist;
}

// Get list of SCSI disks.
//[ad0] => Array
//    (
//        [type] => IDE
//        [desc] => QUANTUM FIREBALL EX6.4A/A0A.0D00
//        [size] => 6149MB
//        [name] => ad0
//        [fullname] => /dev/ad0
//    )
function get_scsi_disks_list()
{
	/* on envoie la commande d'affichage de la liste des disques*/
	exec("/sbin/camcontrol devlist",$rawdata);

	foreach ($rawdata as $line) {
		/* Get information include between parenthese: (pass0,da0) or (da0,pass0)*/
		preg_match("/.*\(([^>]*)\).*/",$line,$match);

		/* Sépare le resultat par la virgule  */
		$temp = preg_split("/,/", $match[1]);

		// Check if diskname is the first (da0,pass0) or the second (pass0,da0) arguement
		$diskname = $temp[1];
		if ($diskname[0] == "p")
			$diskname = $temp[0];

		/* On exlus les lecteurs cd */
		if (!preg_match("/^(cd)/", $diskname  )) 	{
			$disklist[$diskname] = array();
			$disklist[$diskname]['name'] = $diskname;
			$disklist[$diskname]['fullname'] = "/dev/" . $diskname;
			$disklist[$diskname]['type'] = "SCSI";

			/* Match the description witch is include between < and > */
			preg_match("/.*\<([^>]*)>.*/",$line,$match);
			$disklist[$diskname]['desc'] = $match[1];

			/* Get diskinfo to get disk size */
			$diskinfo = disks_get_diskinfo($diskname);
			$disklist[$diskname]['size'] = $diskinfo['mediasize_mbytes'] . "MB";
		}
	}

	return $disklist;
}

// Get list of hardware RAID disks.
//[aacd0] => Array
//    (
//        [type] => RAID
//        [desc] => XYZ
//        [size] => xxxMB
//        [name] => aacd0
//        [fullname] => /dev/aacd0
//    )
function get_hraid_disks_list()
{
	/* Recupere la liste des disques RAID */
	$kerneldisks = explode(" ", trim(preg_replace("/kern.disks: /", "", exec("/sbin/sysctl kern.disks"))));
	
	/* Recupere la liste des disques ATA et SCSI */
	$diskdetected = array_merge((array)get_ata_disks_list(),(array)get_scsi_disks_list());

	/* Recupere le dmesg */
	exec("/sbin/dmesg",$rawdmesg);

	foreach ($kerneldisks as $diskname) {
		$allready = 1;

		// Check of this entry is IDE or SCSI (allready detected)
		foreach ($diskdetected as $diskfoundk => $diskfoundv) 	{
			if (strcasecmp($diskfoundk,$diskname) == 0)
				$allready = 0;
		}
		
		if ($allready) 	{
			/* If not an IDE and SCSI disk */
			$disklist[$diskname] = array();
			$disklist[$diskname]['name'] = $diskname;
			$disklist[$diskname]['fullname']= "/dev/" . $diskname;
			$disklist[$diskname]['type'] = "RAID";

			/* Looking for the disk description in the dmesg */
			foreach ($rawdmesg as $dmesgline) {
				/* Separe la ligne par les espace */
				$dmesgtab = explode(" ", $dmesgline);
				$dmesgtab[0] = rtrim($dmesgtab[0],":");
				// si la ligne commence par le nom du disque: attention il y a 2 lignes
				if ($dmesgtab[0]!="" &&(strcasecmp($dmesgtab[0],$diskname) == 0)) {
					// the first line as this example "aacd0: <RAID 5> on aac0"
					if (strcasecmp(substr($dmesgtab[1], 0, 1),"<") == 0) {
						/* Match the description witch is include between < and > */
						preg_match("/.*\<([^>]*)>.*/",$dmesgline,$match);
						$disklist[$diskname]['desc'] = $match[1];
					}
				}
			}

			/* Get diskinfo to get disk size */
			$diskinfo = disks_get_diskinfo($diskname);
			$disklist[$diskname]['size'] = $diskinfo['mediasize_mbytes'] . "MB";
		}
	}

	return $disklist;
}

// Get list of IDE AND SCSI CDROM.
// exemple: Array
// [acd0] => Array
//    (
//        [type] => IDE
//        [desc] => TDK CDRW241040B/57S2
//        [name] => acd0
//        [size] => NA
//        [fullname] => /dev/acd0
//    )
function get_cdrom_list()
{
	$disklist = array();

	/* Recupere la liste des disques IDE */
	exec("/sbin/atacontrol list",$rawdata);

	/* Variable $i utilisé pour l'index du tableau */
	$i=0;
	foreach ($rawdata as $line) 	{
		/* Separe la ligne par les espace */
		$aline = preg_split("/\s+/", $line);

		/* Si ATA alors NEXT */
		if ($aline[0] == "ATA")
			continue ;

		$diskname = chop($aline[2]);

		/* Exlude disk (adX) and Empty (no) */
		if (!preg_match("/^(ad)/", $diskname ) & $diskname != "no") 	{
			$disklist[$diskname] = array();
			$disklist[$diskname]['name'] = $diskname;
			$disklist[$diskname]['fullname'] = "/dev/" . $diskname;
			$disklist[$diskname]['type'] = "IDE";
			$disklist[$diskname]['size'] = "NA";

			/* Match the description witch is include between < and > */
			preg_match("/.*\<([^>]*)>.*/",$line,$match);
			$disklist[$diskname]['desc'] = $match[1];

			$i++;
		}
	}

	/* Cleaning used variable and initialize array */
	unset($rawdata);

	/* Get the SCSI disk list */	
	/* Get the result of the command camcontrol*/
	exec("/sbin/camcontrol devlist",$rawdata);

	foreach ($rawdata as $line) 	{
		/* Get information include between parenthese: (pass0,da0) or (da0,pass0)*/
		preg_match("/.*\(([^>]*)\).*/",$line,$match);

		/* Sépare le resultat par la virgule  */
		$temp = preg_split("/,/", $match[1]);

		// Check if diskname is the first (da0,pass0) or the second (pass0,da0) arguement
		$diskname = $temp[1];
		if ($diskname[0] == "p")
			$diskname = $temp[0];

		/* On exlus les lecteurs disque dur da */
		if (!preg_match("/^(da)/", $diskname  )) 	{
			$disklist[$diskname] = array();
			$disklist[$diskname]['name'] = $diskname;
			$disklist[$diskname]['fullname'] = "/dev/" . $diskname;
			$disklist[$diskname]['type'] = "SCSI";
			$disklist[$diskname]['size'] = "NA";

			/* Match the description witch is include between < and > */
			preg_match("/.*\<([^>]*)>.*/",$line,$match);
			$disklist[$diskname]['desc'] = $match[1];

			$i++;
		}
	}

	return $disklist;
}

/* Return list of ALL disk: physical, hardware RAID and Software RAID disk */
function get_physical_disks_list()
{
	$disklist = array_merge((array)get_ata_disks_list(),(array)get_scsi_disks_list(),(array)get_hraid_disks_list());
	return $disklist;
}

/* Return list of ALL disk: physical, hardware RAID and Software RAID disk */
function get_all_disks_list()
{
	$disklist = array_merge((array)get_ata_disks_list(),(array)get_scsi_disks_list(),(array)get_hraid_disks_list(), (array)get_sraid_disks_list());
	return $disklist;
}

/* Return size of the disk */
function get_disks_size($diskname)
{
	$disklist = get_physical_disks_list();
	$disksize = 0;

	foreach ($disklist as $diskk => $diskv) {
		if (strcmp($diskk,$diskname)==0)
			$disksize=$diskv['size'];	
	}

	return $disksize;
}

/* wrapper for exec() */
function mwexec($command) {
	global $g;

	if ($g['debug']) {
		if (!$_SERVER['REMOTE_ADDR'])
			echo "mwexec(): $command\n";
		passthru($command, $retval);
	} else {
		exec("$command > /dev/null 2>&1", $oarr, $retval);
	}

	return $retval; 
}

/* wrapper for exec() in background */
function mwexec_bg($command) {

	global $g;
	
	if ($g['debug']) {
		if (!$_SERVER['REMOTE_ADDR'])
			echo "mwexec(): $command\n";
	}
	
	exec("nohup $command > /dev/null 2>&1 &");
}

/* unlink a file, if it exists */
function unlink_if_exists($fn) {
	if (file_exists($fn))
		unlink($fn);
}

/* make a global alias table (for faster lookups) */
function alias_make_table() {
	
	global $config, $g, $aliastable;
	
	$aliastable = array();
	
	if (is_array($config['aliases']['alias'])) {
		foreach ($config['aliases']['alias'] as $alias) {
			if ($alias['name'])
				$aliastable[$alias['name']] = $alias['address'];
		}
	}
}

/* check if an alias exists */
function is_alias($name) {
	
	global $aliastable;
	
	return isset($aliastable[$name]);
}

/* expand a host or network alias, if necessary */
function alias_expand($name) {
	
	global $aliastable;
	
	if (isset($aliastable[$name]))
		return $aliastable[$name];
	else if (is_ipaddr($name) || is_subnet($name))
		return $name;
	else
		return null;
}

/* expand a host alias, if necessary */
function alias_expand_host($name) {
	
	global $aliastable;
	
	if (isset($aliastable[$name]) && is_ipaddr($aliastable[$name]))
		return $aliastable[$name];
	else if (is_ipaddr($name))
		return $name;
	else
		return null;
}

/* expand a network alias, if necessary */
function alias_expand_net($name) {
	
	global $aliastable;
	
	if (isset($aliastable[$name]) && is_subnet($aliastable[$name]))
		return $aliastable[$name];
	else if (is_subnet($name))
		return $name;
	else
		return null;
}

/* find out whether two subnets overlap */
function check_subnets_overlap($subnet1, $bits1, $subnet2, $bits2) {

	if (!is_numeric($bits1))
		$bits1 = 32;
	if (!is_numeric($bits2))
		$bits2 = 32;

	if ($bits1 < $bits2)
		$relbits = $bits1;
	else
		$relbits = $bits2;
	
	$sn1 = gen_subnet_mask_long($relbits) & ip2long($subnet1);
	$sn2 = gen_subnet_mask_long($relbits) & ip2long($subnet2);
	
	if ($sn1 == $sn2)
		return true;
	else
		return false;
}

/* compare two IP addresses */
function ipcmp($a, $b) {
	if (ip2long($a) < ip2long($b))
		return -1;
	else if (ip2long($a) > ip2long($b))
		return 1;
	else
		return 0;
}

/* return true if $addr is in $subnet, false if not */
function ip_in_subnet($addr,$subnet) {
	list($ip, $mask) = explode('/', $subnet);
	$mask = 0xffffffff << (32 - $mask);
	return ((ip2long($addr) & $mask) == (ip2long($ip) & $mask));
}

/* verify (and remove) the digital signature on a file - returns 0 if OK */
function verify_digital_signature($fname) {

	global $g;

	return mwexec("/usr/local/bin/verifysig " .
		escapeshellarg("{$g['etc_path']}/pubkey.pem") . " " .
		escapeshellarg($fname));
}

// Obtain MAC address given an IP address by looking at the ARP table.
function arp_get_mac_by_ip($ip) {
	exec("/usr/sbin/arp -n {$ip}", $arpoutput);
	
	if ($arpoutput[0]) {
		$arpi = explode(" ", $arpoutput[0]);
		$macaddr = $arpi[3];
		if (is_macaddr($macaddr))
			return $macaddr;
		else
			return false;
	}
	
	return false;
}

// Get the interface name.
function get_ifname($if)
{
  // Check if 'auto' mode is activated.
  if ($if == "auto")   {
    $interfaces = explode(" ", exec("/sbin/ifconfig -l"));
    $if = $interfaces[0];
  }

  return $if;
}

// Get local IP address.
function get_ipaddr($if)
{
  $if = get_ifname($if);

  unset($ipaddr);
  exec("/usr/bin/netstat -inW -f inet -I {$if}", $inetinfo);
  array_shift($inetinfo);

  foreach($inetinfo as $inet)
  {
    $ainet = preg_split("/\s+/", $inet);
    $ifname = chop($ainet[0]);

    if (strncmp($ifname,$if,strlen($if)) == 0)
    {
      $ipaddr = chop($ainet[3]);
      break;
    }
  }

  if (strcmp($ipaddr,"") == 0)
    write_console("Failed to get IP address for interface {$if}. ");

  return $ipaddr;
}

// Get local MAC address.
function get_macaddr($if)
{
  $if = get_ifname($if);

  unset($macaddr);
  exec("/usr/bin/netstat -inW -f link -I {$if}", $linkinfo);
  array_shift($linkinfo);

  foreach($linkinfo as $link)  {
    $alink = preg_split("/\s+/", $link);
    $ifname = chop($alink[0]);
    
    if (strncmp($ifname,$if,strlen($if)) == 0) {
      $macaddr = chop($alink[3]);
      break;
    }
  }

  if (strcmp($macaddr,"") == 0) {
    write_console("Failed to get MAC address for interface {$if}. ");
  }

  return $macaddr;
}

// Get DNS servers.
// Parse /etc/resolv.conf when using DHCP (will be updated
// when 'dhclient' is executed), otherwise use configured
// DNS servers from config.xml for static IP.
// Returns array of DNS servers.
function get_dnsserver() {
	global $config;

	$result = array();

	switch ($config['interfaces']['lan']['ipaddr']) {
		case 'dhcp':
			exec("cat /etc/resolv.conf", $rawdata);
			foreach($rawdata as $data) {
				$adata = preg_split("/\s+/", $data);
				if (0 == strcmp(strtolower(trim($adata[0])),"nameserver")) {
					$result[] = trim($adata[1]);
					break;
				}
			}
			break;

		default:
			$result = $config['system']['dnsserver'];
			break;
	}

	return $result;
}

// Get the default gateway.
// Returns default gateway IP
function get_defaultgateway() {
	global $config;

	$result = "";

	switch ($config['interfaces']['lan']['ipaddr']) {
		case 'dhcp':
			exec("/sbin/route -n get default", $rawdata);
			foreach($rawdata as $data) {
				$adata = preg_split("/\s+/", trim($data));
				if (false !== strstr($adata[0],"gateway")) {
					$result = $adata[1];
					break;
				}
			}
			break;

		default:
			$result = $config['interfaces']['lan']['gateway'];
			break;
	}

	return $result;
}

// Get list of disks from given type of filesystem.
// Return list containing disks of specified filesystem type
function get_fstype_disks_list($fstype) {
	global $config;

	$result = array();

	// Scan all physical disk */
	if (is_array($config['disks']['disk'])) {
		foreach ($config['disks']['disk'] as $disk) {
			if (strcmp($disk['fstype'],$fstype) == 0) {
				$result[] = $disk;
			}
   	}
 	}

	// Scan all vdisk volumes.
	$avdisktype = explode(" ","gmirror gconcat gstripe gvinum graid5 geli");
	foreach ($avdisktype as $vdisktypev) {
		if (is_array($config[$vdisktypev]['vdisk'])) {
			foreach ($config[$vdisktypev]['vdisk'] as $disk) {
				if (0 == strcmp($disk['fstype'],$fstype)) {
					$result[] = $disk;
				}
			}
		}
	}

	return $result;
}

/* Get list of supported filesystem types */
function get_fstype_list() {
	global $g_filesystems;
	return $g_filesystems;
}

function get_fstype_shortdesc($fstype) {
  $shortdesc = "";

  switch($fstype)
  {
    case "":              $shortdesc = gettext("Unformated");
                          break;
    case "ufs":
    case "ufs_no_su":
    case "ufsgpt":
    case "ufsgpt_no_su":  $shortdesc = "UFS";
                          break;
    case "msdos":         $shortdesc = "FAT32";
                          break;
    case "cd9660":				$shortdesc = "CD/DVD";
                          break;
    case "ntfs":          $shortdesc = "NTFS";
                          break;
    case "gmirror":       $shortdesc = "gmirror";
                          break;
    case "gconcat":       $shortdesc = "gconcat";
                          break;
    case "gstripe":       $shortdesc = "gstripe";
                          break;
    case "graid5":				$shortdesc = "graid5";
                          break;
    case "gvinum":        $shortdesc = "gvinum";
                          break;
    case "softraid":      $shortdesc = gettext("SoftRaid");
                          break;
		case "geli":					$shortdesc = gettext("Encrypted");
                          break;
    case "ext2":					$shortdesc = "EXT2";
    											break;
  }
  
  return $shortdesc;
}

/* Search in a multidimensional array */		
function array_search_ex($needle, $haystack, $key)
{
  foreach($haystack as $haystackval => $value) {
    $found = false;
    if(is_array($needle) && is_array($key)) {
      foreach($needle as $n => $needlev) {
        $found = ($value[$key[$n]] == $needlev);        
        if(false == $found) {
          break;
        }
      }
    } else {
      $found = ($value[$key] == $needle);
    }
    if(true == $found) {
      return $haystackval;
    }
  }
  return false;
}

/*
  Get interface informations. 
  $iftype - interface name (e.g. de0, ...)
*/
function get_interface_info($if)
{
	global $config, $g;

	$ifinfo = array();

	$ifinfo['hwif'] = get_ifname($if);
	$ifinfo['if'] = $ifinfo['hwif'];

	/* run netstat to determine link info */
	unset($linkinfo);
	exec("/usr/bin/netstat -I " . $ifinfo['hwif'] . " -nWb -f link", $linkinfo);
	$linkinfo = preg_split("/\s+/", $linkinfo[1]);
	if (preg_match("/\*$/", $linkinfo[0]) || preg_match("/^$/", $linkinfo[0])) {
		$ifinfo['status'] = "down";
	} else {
		$ifinfo['status'] = "up";
	}

	if (!strstr($ifinfo['if'],'tun')) {
		$ifinfo['macaddr'] = $linkinfo[3];
		$ifinfo['inpkts'] = $linkinfo[4];
		$ifinfo['inerrs'] = $linkinfo[5];
		$ifinfo['inbytes'] = $linkinfo[6];
		$ifinfo['outpkts'] = $linkinfo[7];
		$ifinfo['outerrs'] = $linkinfo[8];
		$ifinfo['outbytes'] = $linkinfo[9];
		$ifinfo['collisions'] = $linkinfo[10];
	} else {
		$ifinfo['inpkts'] = $linkinfo[3];
		$ifinfo['inbytes'] = $linkinfo[5];
		$ifinfo['outpkts'] = $linkinfo[6];
		$ifinfo['outbytes'] = $linkinfo[8];
	}

	if ($ifinfo['status'] == "up") {
		/* try to determine media with ifconfig */
		unset($ifconfiginfo);
		exec("/sbin/ifconfig " . $ifinfo['hwif'], $ifconfiginfo);

		foreach ($ifconfiginfo as $ici) {
			if (!isset($config['interfaces'][$iftype]['wireless'])) {
				/* don't list media/speed for wireless cards, as it always
				   displays 2 Mbps even though clients can connect at 11 Mbps */
				if (preg_match("/media: .*? \((.*?)\)/", $ici, $matches)) {
					$ifinfo['media'] = $matches[1];
				} else if (preg_match("/media: Ethernet (.*)/", $ici, $matches)) {
					$ifinfo['media'] = $matches[1];
				}
			}
			if (preg_match("/status: (.*)$/", $ici, $matches)) {
				if ($matches[1] != "active")
					$ifinfo['status'] = $matches[1];
			}
			if (preg_match("/channel (\S*)/", $ici, $matches)) {
				$ifinfo['channel'] = $matches[1];
			}
			if (preg_match("/ssid (\".*?\"|\S*)/", $ici, $matches)) {
				if ($matches[1][0] == '"')
					$ifinfo['ssid'] = substr($matches[1], 1, -1);
				else
					$ifinfo['ssid'] = $matches[1];
			}
		}

		if ($ifinfo['pppoelink'] != "down" && $ifinfo['pptplink'] != "down") {
			/* try to determine IP address and netmask with ifconfig */
			unset($ifconfiginfo);
			exec("/sbin/ifconfig " . $ifinfo['if'], $ifconfiginfo);

			foreach ($ifconfiginfo as $ici) {
				if (preg_match("/inet (\S+)/", $ici, $matches)) {
					$ifinfo['ipaddr'] = $matches[1];
				}
				if (preg_match("/netmask (\S+)/", $ici, $matches)) {
					if (preg_match("/^0x/", $matches[1]))
						$ifinfo['subnet'] = long2ip(hexdec($matches[1]));
				}
			}

			if ($iftype == "lan") {
				/* run netstat to determine the default gateway */
				unset($netstatrninfo);
				exec("/usr/bin/netstat -rnf inet", $netstatrninfo);
				
				foreach ($netstatrninfo as $nsr) {
					if (preg_match("/^default\s*(\S+)/", $nsr, $matches)) {
						$ifinfo['gateway'] = $matches[1];
					}
				}
			}
		}
	}

	return $ifinfo;
}

/*
  Get interface informations. 
  $iftype - interface type (e.g. lan, opt[n], ...)
*/
function get_interface_info_ex($iftype)
{
  global $config;
  
  /* find out interface name */
	$if = $config['interfaces'][$iftype]['if'];
	return get_interface_info($if);
}

/* Get the product name */
function get_product_name() {
	global $g;
	return chop(file_get_contents("{$g['etc_path']}/prd.name"));
}

/* Get the product URL */
function get_product_url() {
	global $g;
	return chop(file_get_contents("{$g['etc_path']}/prd.url"));
}

/* Get the product copyright note */
function get_product_copyright() {
	global $g;
	return chop(file_get_contents("{$g['etc_path']}/prd.copyright"));
}

/* Get the product version */
function get_product_version() {
	global $g;
	return chop(file_get_contents("{$g['etc_path']}/prd.version"));
}

/* Get the product version */
function get_product_buildtime() {
	global $g;
	return chop(file_get_contents("{$g['etc_path']}/prd.version.buildtime"));
}

/* Get the RAM information. Returns an array listing the amount of memory installed in the hardware. */
function get_ram_info() {
	exec("/sbin/sysctl -n vm.stats.vm.v_active_count vm.stats.vm.v_inactive_count vm.stats.vm.v_wire_count vm.stats.vm.v_cache_count vm.stats.vm.v_free_count hw.physmem", $memory);
	exec("/sbin/sysctl -n hw.realmem", $hwmemory);

	$raminfo = array();
	$raminfo['real'] = $hwmemory[0];
	$raminfo['physical'] = $memory[5];
	$raminfo['total'] = $memory[0] + $memory[1] + $memory[2] + $memory[3] + $memory[4];
	$raminfo['free'] = $memory[4] + $memory[1];
	$raminfo['used'] = $raminfo['total'] - $raminfo['free'];

	return $raminfo;
}

function write_log($message) {
	$cmd="/usr/bin/logger \"{$message}\"";
	mwexec($cmd);
}

function write_console($message) {
	echo($message);
}
?>
