<?php
/*
	config.inc
	part of FreeNAS (http://www.freenas.org)
	Copyright (C) 2005-2007 Olivier Cochard-Labbe <olivier@freenas.org>.
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
require_once("globals.inc");
require_once("util.inc");
require_once("xmlparse.inc");

/* Get product information */
$productname = get_product_name();

/* read platform */
if (file_exists("{$g['etc_path']}/platform")) {
	$g['fullplatform'] = chop(file_get_contents("{$g['etc_path']}/platform"));
	$pla = explode("-", $g['fullplatform']);
	$g['arch'] = $pla[0];
	$g['platform'] = $pla[1];
} else {
	$g['arch'] = $g['fullplatform'] = "unknown";
}

if (is_booting()) {
	/* find the device where config.xml resides and write out an fstab */
	unset($cfgdevice);

	/* check if there's already an fstab ('full' release booting?) */
	if (!file_exists("{$g['etc_path']}/fstab")) {
		if (strstr($g['platform'], "liveCD")) 	{
			echo "Booting from liveCD: Looking for configuration file on floppy or USB\n";
			/* Config is on floppy disk/USB stick for CD-ROM version */
			$cfgdevice = $cfgpartition = "fd0";
			$cfgfstype = "msdos";

			/* Check if there is a USB device available. */
			if (ereg("da0", exec("/sbin/sysctl kern.disks")) == true) {
				/* Try to mount device. */
				if (mwexec("/sbin/mount -r -t msdos /dev/da0s1 {$g['cf_path']}") == 0) {
					$cfgdevice = "da0";
					$cfgpartition = $cfgdevice . "s1";

					/* Unmount device. */
					mwexec("/sbin/umount -f /dev/{$cfgpartition}");
				} else if (mwexec("/sbin/mount -r -t msdos /dev/da0 {$g['cf_path']}") == 0){
					$cfgdevice = "da0";
					$cfgpartition = $cfgdevice ;

					/* Unmount device. */
					mwexec("/sbin/umount -f /dev/{$cfgpartition}");
				}
			}
		}
		else 	{
			/* probe kernel known disks until we find one with config.xml */
			$disks = explode(" ", trim(preg_replace("/kern.disks: /", "", exec("/sbin/sysctl kern.disks"))));
			foreach ($disks as $mountdisk) {
				/* skip mfs mounted filesystems */
				if (strstr($mountdisk, "md")) {
					continue;
				}

				/* Search on slice 1 for OS installed on disk with install script*/
				if (mwexec("/sbin/mount -r /dev/{$mountdisk}s1 {$g['cf_path']}") == 0) 	{
					if (file_exists("{$g['cf_conf_path']}/config.xml")) {
						/* found it */
						$cfgdevice = $mountdisk;
						$cfgpartition = $cfgdevice . "s1";
						$cfgfstype = "ufs";
						echo "Found configuration on device=/dev/{$cfgpartition} type={$cfgfstype}\n";
					}

					mwexec("/sbin/umount -f {$g['cf_path']}");

					if ($cfgdevice) {
						break;
					}
				}

				/* Search on partition 'a' for OS IMG image written on disk */
				if (mwexec("/sbin/mount -r /dev/{$mountdisk}a {$g['cf_path']}") == 0) 	{
					if (file_exists("{$g['cf_conf_path']}/config.xml")) {
						/* found it */
						$cfgdevice = $mountdisk;
						$cfgpartition = $cfgdevice . "a";
						$cfgfstype = "ufs";
						echo "Found configuration on device=/dev/{$cfgpartition} type={$cfgfstype}\n";
					}

					mwexec("/sbin/umount -f {$g['cf_path']}");

					if ($cfgdevice) {
						break;
					}
				}
			}
		}

		if (!$cfgdevice) {
			/* no config found on ufs partitions, now search msdos partitions */
			$disks = explode(" ", trim(preg_replace("/kern.disks: /", "", exec("/sbin/sysctl kern.disks"))));
			foreach ($disks as $mountdisk) {
				/* skip mfs mounted filesystems */
				if (strstr($mountdisk, "md"))
					continue;
				/* Search on slice 1 for msdos OS */
				if (mwexec("/sbin/mount -r -t msdos /dev/{$mountdisk}s1 {$g['cf_path']}") == 0)
				{
					if (file_exists("{$g['cf_conf_path']}/config.xml"))
					{
						/* found it */
						$cfgdevice = $mountdisk;
						$cfgpartition = $cfgdevice . "s1";
						$cfgfstype = "msdos";
						echo "Found configuration on device=/dev/{$cfgpartition} type={$cfgfstype}\n";
					}

					mwexec("/sbin/umount -f {$g['cf_path']}");

					if ($cfgdevice)
						break;
				}
			}
		}

		if (!$cfgdevice) {
			/* no device found, print an error and die */
			echo <<<EOD

*******************************************************************************
* FATAL ERROR                                                                 *
* The device that contains the configuration file (config.xml) could not be   *
* found. {$productname} cannot continue booting.                                     *
*******************************************************************************

EOD;

			mwexec("/sbin/halt");
			exit;
		}

		/* write device name to a file for rc.firmware */
		$fd = fopen("{$g['varetc_path']}/cfdevice", "w");
		fwrite($fd, $cfgpartition . "\n");
		fclose($fd);

		/* write out an fstab */
		$fd = fopen("{$g['etc_path']}/fstab", "w");

		$fstab = "/dev/{$cfgpartition} {$g['cf_path']} {$cfgfstype} ro 1 1\n";
		// $fstab .= "proc /proc procfs rw 0 0\n";

		fwrite($fd, $fstab);
		fclose($fd);
	}

	/* mount all filesystems */
	mwexec("/sbin/mount -a");
}

/* parse configuration */
if (!$noparseconfig) {
	config_lock();

	/* see if there's a newer cache file */
	if (file_exists("{$g['tmp_path']}/config.cache") &&
		(filemtime("{$g['tmp_path']}/config.cache") >=
		 filemtime("{$g['conf_path']}/config.xml"))) {

		/* read cache */
		$config = unserialize(file_get_contents("{$g['tmp_path']}/config.cache"));
	} else {

		if (!file_exists("{$g['conf_path']}/config.xml")) {
			if (is_booting()) {
				if (strstr($g['platform'], "liveCD")) {
					/* try copying the default config. to the floppy */
					reset_factory_defaults();

					echo "No XML configuration file found - using factory defaults.\n";
					echo "Make sure that the configuration floppy disk with the conf/config.xml\n";
					echo "file is inserted. If it isn't, your configuration changes will be lost\n";
					echo "on reboot.\n";
				} else {
					echo "XML configuration file not found. {$productname} cannot continue booting.\n";
					mwexec("/sbin/halt");
					exit;
				}
			} else {
				config_unlock();
				exit(0);
			}
		}

		$config = parse_xml_config("{$g['conf_path']}/config.xml", $g['xml_rootobj']);

		if ((float)$config['version'] > (float)$g['latest_config']) {
			if (is_booting()) {
				echo <<<EOD

*******************************************************************************
* WARNING!                                                                    *
* The current configuration has been created with a newer version of {$productname}  *
* than this one! This can lead to serious misbehavior and even security       *
* holes! You are urged to either upgrade to a newer version of {$productname} or     *
* revert to the default configuration immediately!                            *
*******************************************************************************

EOD;
			}
		}

		/* write config cache */
		$fd = @fopen("{$g['tmp_path']}/config.cache", "wb");
		if ($fd) {
			fwrite($fd, serialize($config));
			fclose($fd);
		}
	}

	config_unlock();

	/* make alias table (for faster lookups) */
	alias_make_table();
}

/* mount flash card read/write */
function conf_mount_rw() {
	global $g;

	if ($g['platform'] != "full") {
		/* don't use mount -u anymore
		(doesn't sync the files properly and /bin/sync won't help either) */
		mwexec("/sbin/umount -f {$g['cf_path']}");
		mwexec("/sbin/mount -w -o noatime {$g['cf_path']}");
	}
}

/* mount flash card read only */
function conf_mount_ro() {
	global $g;

	if ($g['platform'] != "full") {
		mwexec("/sbin/umount -f {$g['cf_path']}");
		mwexec("/sbin/mount -r {$g['cf_path']}");
	}
}

/* save the system configuration */
function write_config() {
	global $config, $g;

	config_lock();

	conf_mount_rw();

	if (time() > mktime(0, 0, 0, 9, 1, 2004))	/* make sure the clock settings is plausible */
		$config['lastchange'] = time();

	/* generate configuration XML */
	$xmlconfig = dump_xml_config($config, $g['xml_rootobj']);

	/* write configuration */
	$fd = fopen("{$g['cf_conf_path']}/config.xml", "w");

	if (!$fd)
		die("Unable to open config.xml for writing in write_config()\n");

	fwrite($fd, $xmlconfig);
	fclose($fd);

	conf_mount_ro();

	/* re-read configuration */
	$config = parse_xml_config("{$g['conf_path']}/config.xml", $g['xml_rootobj']);

	/* write config cache */
	$fd = @fopen("{$g['tmp_path']}/config.cache", "wb");
	if ($fd) {
		fwrite($fd, serialize($config));
		fclose($fd);
	}

	config_unlock();
}

function reset_factory_defaults() {
	global $g;

	config_lock();

	conf_mount_rw();

	/* create conf directory, if necessary */
	if (!file_exists("{$g['cf_conf_path']}"))
		@mkdir("{$g['cf_conf_path']}");

	/* clear out /conf */
	$dh = opendir($g['conf_path']);
	while ($filename = readdir($dh)) {
		if (($filename != ".") && ($filename != "..")) {
			unlink($g['conf_path'] . "/" . $filename);
		}
	}
	closedir($dh);

	/* copy default configuration */
	@copy("{$g['conf_default_path']}/config.xml", "{$g['conf_path']}/config.xml");

	conf_mount_ro();

	config_unlock();

	return 0;
}

function config_install($conffile) {
	global $config, $g;

	if (!file_exists($conffile))
		return 1;

	config_lock();
	conf_mount_rw();

	copy($conffile, "{$g['conf_path']}/config.xml");

	conf_mount_ro();
	config_unlock();

	return 0;
}

/* lock configuration file, decide that the lock file is stale after
   10 seconds */
function config_lock() {
	global $g;

	$lockfile = "{$g['varrun_path']}/config.lock";

	$n = 0;
	while ($n < 10) {
		/* open the lock file in append mode to avoid race condition */
		if ($fd = @fopen($lockfile, "x")) {
			/* succeeded */
			fclose($fd);
			return;
		} else {
			/* file locked, wait and try again */
			sleep(1);
			$n++;
		}
	}
}

// Convert the config file.
// Return 0 if successful, otherwise 1
function convert_config() {
	global $config, $g;

	if ($config['version'] == $g['latest_config'])
		return 0;		/* already at latest version */

	if (is_booting())
		echo "Converting configuration.";

	/* convert 1 -> 1.1 */
	/* changing partition name, and initializing old bad user parameters */
	if ($config['version'] === "1") {
		/* change the partitions name "1" to "s1" */
		for ($i = 0; isset($config['mounts']['mount'][$i]); $i++)
		{
			if (isset($config['mounts']['mount'][$i]['partition']))
			{
				switch ($config['mounts']['mount'][$i]['partition'])
				{
					case "1":
					   $config['mounts']['mount'][$i]['partition']="s1";
					   break;
					case "2":
					   $config['mounts']['mount'][$i]['partition']="s2";
					   break;
					case "3":
					   $config['mounts']['mount'][$i]['partition']="s3";
					   break;
					case "4":
					   $config['mounts']['mount'][$i]['partition']="s4";
					   break;
				}
			}
		}

		if (is_array($config['nfs']['network'])) 	{
			$config['nfs']['nfsnetwork'] = $config['nfs']['network'];
			unset($config['nfs']['network']);
		}

		$config['access']['groupid']=1002;
		$config['access']['userid']=1002;
		unset($config['access']['group']);
		unset($config['access']['user']);

		$config['version'] = "1.1";
	}

	/* convert 1.1 -> 1.2 */
	/* change the gvnium mount point type 'sraid' to 'gvinum' */
	if ($config['version'] === "1.1") {
		for ($i = 0; isset($config['mounts']['mount'][$i]); $i++) {
			if (isset($config['mounts']['mount'][$i]['partition'])) {
				if (strcmp($config['mounts']['mount'][$i]['partition'],"sraid") == 0)
					$config['mounts']['mount'][$i]['partition'] = "gvinum";
			}
		}

		$config['version'] = "1.2";
	}

	/* convert 1.2 -> 1.3 */
	/* change the FTP anonymous and local user authentication YES/NO value to right/flase type */
	if ($config['version'] === "1.2") {

		if ($config['ftp']['anonymous'] === "YES")
			$config['ftp']['anonymous'] = true;
		else
			$config['ftp']['anonymous'] = false;

		if ($config['ftp']['localuser'] === "YES")
			$config['ftp']['localuser'] = true;
		else
			$config['ftp']['localuser'] = false;

		$config['version'] = "1.3";
	}

	/* convert 1.3 -> 1.4 */
	/* change the gvnium disk fstype attribute 'raid' to 'gvinum' */
	/* change <raid> to <gvinum> */
	if ($config['version'] === "1.3") {
		for ($i = 0; isset($config['disks']['disk'][$i]); $i++) {
			if (0==strcmp($config['disks']['disk'][$i]['fstype'],"raid")) {
				$config['disks']['disk'][$i]['fstype'] = "gvinum";
			}
		}

    if(is_array($config['raid'])) {
      $config['gvinum'] = $config['raid'];
    }
    unset($config['raid']);

		$config['version'] = "1.4";
	}

	/* convert 1.4 -> 1.5 */
	/* Add [fullname] information on mount,disk, gmirror, gvinum and graid5 */
	if ($config['version'] === "1.4") {
		/* Add fullname (/dev/ad0) under disk */
		for ($i = 0; isset($config['disks']['disk'][$i]); $i++) {
			if (isset($config['disks']['disk'][$i]['name'])) 	{
				$config['disks']['disk'][$i]['fullname']="/dev/{$config['disks']['disk'][$i]['name']}";
			}
		}

		/* Add fullname (/dev/ad0s1) under mount point */
		for ($i = 0; isset($config['mounts']['mount'][$i]); $i++) {
			switch ($config['mounts']['mount'][$i]['partition']) {
				case "gvinum":
					$config['mounts']['mount'][$i]['fullname'] = "/dev/gvinum/{$config['mounts']['mount'][$i]['mdisk']}";
					break;
				case "gmirror":
					$config['mounts']['mount'][$i]['fullname'] = "/dev/mirror/{$config['mounts']['mount'][$i]['mdisk']}";
					break;
				case "gconcat":
					$config['mounts']['mount'][$i]['fullname'] = "/dev/concat/{$config['mounts']['mount'][$i]['mdisk']}";
					break;
				case "gstripe":
					$config['mounts']['mount'][$i]['fullname'] = "/dev/stripe/{$config['mounts']['mount'][$i]['mdisk']}";
					break;
				case "graid5":
					$config['mounts']['mount'][$i]['fullname'] = "/dev/raid5/{$config['mounts']['mount'][$i]['mdisk']}";
					break;
				default:
					$config['mounts']['mount'][$i]['fullname'] = "/dev/{$config['mounts']['mount'][$i]['mdisk']}{$config['mounts']['mount'][$i]['partition']}";
			}
		}

		// Add fullname (/dev/mirror/toto) under gmirror
		for ($i = 0; isset($config['gmirror']['vdisk'][$i]); $i++) {
			if (isset($config['gmirror']['vdisk'][$i]['name'])) 	{
				$config['gmirror']['vdisk'][$i]['fullname']="/dev/mirror/{$config['gmirror']['vdisk'][$i]['name']}";
			}
		}

		// Add fullname (/dev/gvinum/toto) under gvinum
		for ($i = 0; isset($config['gvinum']['vdisk'][$i]); $i++) {
			if (isset($config['gvinum']['vdisk'][$i]['name'])) 	{
				$config['gvinum']['vdisk'][$i]['fullname']="/dev/gvinum/{$config['gvinum']['vdisk'][$i]['name']}";
			}
		}

		// Add fullname (/dev/raid5/toto) under graid5
		for ($i = 0; isset($config['graid5']['vdisk'][$i]); $i++) {
			if (isset($config['graid5']['vdisk'][$i]['name'])) 	{
				$config['graid5']['vdisk'][$i]['fullname']="/dev/raid5/{$config['graid5']['vdisk'][$i]['name']}";
			}
		}

		$config['version'] = "1.5";
	}

	/* convert 1.5 -> 1.6 */
	/* Upgrade iSCSI and NFS configurations
	Upgrade RSYNC client configuration */
	if ($config['version'] === "1.5") {
		if (isset($config['nfs']['enable'])) 	{
			$config['nfs']['nfsnetworks']=array();
			$config['nfs']['nfsnetworks'][]=$config['nfs']['nfsnetwork'];
			unset($config['nfs']['nfsnetwork']);
		}

		if (isset($config['iscsi']['enable'])) 	{
			$iscsivdisk = array();
			$iscsivdisk['targetname'] = $config['iscsi']['targetname'];
			$iscsivdisk['targetaddress'] = $config['iscsi']['targetaddress'];
			$iscsivdisk['name'] = "target0";
			$iscsivdisk['initiatorname'] = "iqn.2005-01.il.ac.huji.cs";
			$config['iscsiinit']['vdisk'] = array();
			$config['iscsiinit']['vdisk'][] = $iscsivdisk;

			unset($config['iscsi']['enable']);
			unset($config['iscsi']['targetaddress']);
			unset($config['iscsi']['targetname']);

		}
		/* Convert 'rsyncclient' part
		Special note: rsyncclient is now an array, then the xml parse will create an array.
		We must use an array
		*/
		if (isset($config['rsyncclient'][0]['enable'])) {
			if (!is_array($config['rsync'])) {
				$config['rsync'] = array();
				if (!is_array($config['rsync']['rsyncclient'])) {
					$config['rsync']['rsyncclient'] = array();
				}
			} else if (!is_array($config['rsync']['rsyncclient'])) {
				$config['rsync']['rsyncclient'] = array();
			}

			foreach ($config['rsyncclient'][0]['sharetosync'] as $sharek => $sharev) 	{

				$rsyncclient = array();
				$rsyncclient['opt_delete'] = isset($config['rsyncclient'][0]['opt_delete']);
				$rsyncclient['rsyncserverip'] = $config['rsyncclient'][0]['rsyncserverip'];
				$rsyncclient['localshare'] = $sharev;
				$rsyncclient['remoteshare'] = $sharev;
				$rsyncclient['minute'] = $config['rsyncclient'][0]['minute'];
				$rsyncclient['hour'] = $config['rsyncclient'][0]['hour'];
				$rsyncclient['day'] = $config['rsyncclient'][0]['day'];
				$rsyncclient['month'] = $config['rsyncclient'][0]['month'];
				$rsyncclient['weekday'] = $config['rsyncclient'][0]['weekday'];
				$rsyncclient['all_mins'] = $config['rsyncclient'][0]['all_mins'];
				$rsyncclient['all_hours'] = $config['rsyncclient'][0]['all_hours'];
				$rsyncclient['all_days'] = $config['rsyncclient'][0]['all_days'];
				$rsyncclient['all_months'] = $config['rsyncclient'][0]['all_months'];
				$rsyncclient['all_weekdays'] = $config['rsyncclient'][0]['all_weekdays'];

				$config['rsync']['rsyncclient'][] = $rsyncclient;

				unset($rsyncclient);
			}

		unset ($config['rsyncclient']);

		} else if (is_array($config['rsyncclient'])) {
			// Clean old unused code
			unset ($config['rsyncclient']);
		}
		if (isset($config['rsync_local']['enable'])) {

			if (!is_array($config['rsync'])) {
				$config['rsync'] = array();
				if (!is_array($config['rsync']['rsynclocal'])) {
					$config['rsync']['rsynclocal'] = array();
				}
			} else if (!is_array($config['rsync']['rsynclocal'])) {
				$config['rsync']['rsynclocal'] = array();
			}

			$rsynclocal['opt_delete'] = isset($config['rsync_local']['opt_delete']);
			$rsynclocal['source'] = $config['rsync_local']['source'];
			$rsynclocal['destination'] = $config['rsync_local']['destination'];
			$rsynclocal['minute'] = $config['rsync_local']['minute'];
			$rsynclocal['hour'] = $config['rsync_local']['hour'];
			$rsynclocal['day'] = $config['rsync_local']['day'];
			$rsynclocal['month'] = $config['rsync_local']['month'];
			$rsynclocal['weekday'] = $config['rsync_local']['weekday'];
			$rsynclocal['all_mins'] = $config['rsync_local']['all_mins'];
			$rsynclocal['all_hours'] = $config['rsync_local']['all_hours'];
			$rsynclocal['all_days'] = $config['rsync_local']['all_days'];
			$rsynclocal['all_months'] = $config['rsync_local']['all_months'];
			$rsynclocal['all_weekdays'] = $config['rsync_local']['all_weekdays'];

			$config['rsync']['rsynclocal'][] = $rsynclocal;

			unset ($config['rsync_local']);
		}

		$config['version'] = "1.6";
	}

	/* Convert 1.6 -> 1.7 */
	/* - Change ['system']['howl_disable'] to ['system']['zeroconf']. */
	/* - Delete ['system']['polling']. */
	/* - Delete ['filter']['bypassstaticroutes']. */
	/* - Delete ['bridge'], ['bridge']['filteringbridge']. */
	if ($config['version'] === "1.6") {
		if (isset($config['system']['howl_disable'])) {
			unset($config['system']['howl_disable']);
		} else {
			$config['system']['zeroconf'] = true;
		}

		if (isset($config['system']['polling'])) {
			unset($config['system']['polling']);
		}

		if (isset($config['filter']['bypassstaticroutes'])) {
			unset($config['filter']['bypassstaticroutes']);
		}

		if (isset($config['bridge'])) {
			unset($config['bridge']);
		}

		$config['version'] = "1.7";
	}

	/* Convert 1.7 -> 1.8 */
	/* Bug Fix: Forget to confert old fstype value with new one */
	/* Create bug "You must add disks first" with older configured disk */
	/* - Change ['fstype']='gmirror' to 'softraid' */
	/* - Change ['fstype']='gconcat' to 'softraid' */
	/* - Change ['fstype']='gstripe' to 'softraid' */
	/* - Change ['fstype']='graid5' to 'softraid' */
	if ($config['version'] === "1.7") {
		for ($i = 0; isset($config['disks']['disk'][$i]); $i++) {
			if (isset($config['disks']['disk'][$i]['name'])) 	{
				switch ($config['disks']['disk'][$i]['fstype']) {
				case "gmirror":
					$config['disks']['disk'][$i]['fstype']="softraid";
					break;
				case "gconcat":
					$config['disks']['disk'][$i]['fstype']="softraid";
					break;
				case "gstripe":
					$config['disks']['disk'][$i]['fstype']="softraid";
					break;
				case "graid5":
					$config['disks']['disk'][$i]['fstype']="softraid";
					break;
				}
			}
		}

		$config['version'] = "1.8";
	}

	/* Convert 1.8 -> 1.9 */
	/* Add type "disk" to existing mountpoint */
	/* Add fullname (/dev/ad0s1) under mount point */
	if ($config['version'] === "1.8") {
		for ($i = 0; isset($config['mounts']['mount'][$i]); $i++) {
			$config['mounts']['mount'][$i]['type']= "disk";
		}

		$config['version'] = "1.9";
	}

	/* Convert 1.9 -> 2.0 */
	/* Add ipv6 address type */
	if ($config['version'] === "1.9") {
		$config['interfaces']['lan']['ipv6addr']="auto";
		$config['interfaces']['lan']['ipv6subnet']=64;

		for ($i = 1; isset($config['interfaces']['opt' . $i]); $i++) {
			$optcfg = $config['interfaces']['opt' . $i];
			if (isset($optcfg['enable'])) {
				$config['interfaces']['opt' . $i]['ipv6addr']="auto";
				$config['interfaces']['opt' . $i]['ipv6subnet']=64;
			}
		}

		$config['version'] = "2.0";
	}

	write_config();

	if (is_booting())
		echo "done\n";

	return 0;
}

/* unlock configuration file */
function config_unlock() {
	global $g;

	$lockfile = "{$g['varrun_path']}/config.lock";

	if (file_exists($lockfile))
		unlink($lockfile);
}
?>
