<?php
/*
	disks.inc
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
require_once("functions.inc");

// Mount all configured disks.
// Return 0 if sucessful, 1 if error
function disks_mount_all()
{
	global $config, $g;

	// For each device configured:
	if (is_array($config['mounts']['mount'])) {
		// Mount real disks first.
		foreach ($config['mounts']['mount'] as $mount) {
			if ("disk" !== $mount['type'])
				continue;

			// Advanced umount filesystem if not in booting mode (mount edition).
			// Background: 'disks_umount' can't be used in the case the mount point
			// has been renamed by the user because it uses the 'sharename' (which is
			// now the new name of the share, but we need the old name) for unmounting.
			if (!is_booting())
				disks_umount_ex($mount);

			disks_mount($mount);
		}

		// Then mount ISOs.
		foreach ($config['mounts']['mount'] as $mount) {
			if ("iso" !== $mount['type'])
				continue;

			// Advanced umount filesystem if not in booting mode (mount edition).
			if (!is_booting())
				disks_umount($mount);

			disks_mount($mount);
		}
	}

	return 0;
}

// This function umount all configured mount points.
// Return 0 if successful, otherwise 1
function disks_umount_all()
{
	global $config;

	// Sync disks first.
	mwexec("/bin/sync");

	if (is_array($config['mounts']['mount'])) {
		foreach ($config['mounts']['mount'] as $mountent) {
			disks_umount($mountent);
		}
	}

	return 0;
}

// Mount using the configured mount given in parameter
// Return 0 if sucessful, 1 if error
function disks_mount($mount)
{
	global $config, $g;

	// Set PATH (otherwise ntfs won't be mounted via WebGUI).
	putenv("PATH=/bin:/sbin:/usr/bin:/usr/sbin:/usr/local/bin:/usr/local/sbin");

	// Set mount name.
	$mountname = "/mnt/{$mount['sharename']}";

	// Changing the PHP file creation mask to 0777
	$oldumask = umask(0);

	// Create one directory for each device under /mnt.
	if (! @mkdir($mountname, 0777)) {
		write_log("Error: Can't create directory {$mountname} in disks_mount().");
	}

	// Restoring PHP file creation mask to old value.
	umask($oldumask);

	// Check
	if ($oldumask != umask()) {
		write_log("Error: Failed to change back umask in disks_mount().");
	}

	/* Mount the filesystem. */
	/* In booting mode, skip encrypted disk (need to enter the passphrase on the webgui) */
	if (is_booting()) {
		if (is_array($config['geli']['vdisk'])) {
			$a_geli = $config['geli']['vdisk'];
			if (array_search_ex($mount['mdisk'], $a_geli, "fullname")) {
				write_console("\nSkip mounting {$mount['sharename']}: Passphrase needed.");
				write_log("Passphrase needed for '{$mountname}'");
				return 0;
			}
		}
	}

	switch($mount['type']) {
		case "disk":
			$devname = $mount['fullname'];
			break;

		case "iso":
			// Create memory disk. 'mdconfig' returns the ID of this disk.
			exec("/sbin/mdconfig -a -t vnode -f '{$mount['filename']}'", $mdid, $result);
			if (0 !== $result) {
				write_log("Error: Failed to create memory disk for '{$mount['filename']}'");
				return 1;
			}
			$devname = "/dev/{$mdid[0]}";
			break;
	}

	/* Check if filesystem should be mounted read-only. */
	$readonly = (isset($mount['readonly'])) ? "-r" : "";

	/* Check the fileystem only if there is a problem. */
	/* This part is too stupid: I must read the FreeBSD start script for use the same intelligent method for checking hard drive */
	switch ($mount['fstype']) {
		case "ufs":
			$result = mwexec("/sbin/mount -t ufs -o acls {$readonly} {$devname} '{$mountname}'");
			if (0 == $result) {
				write_log("Device {$devname} mounted using UFS on '{$mountname}'");
				$result = 0;
			} else {
				write_log("Error: Failed to detect disk {$devname}, starting a UFS fsck");
				// Do a filesystem check.
				mwexec("/sbin/fsck -y -t ufs {$devname}");
				// Retry to mount the partition.
				$result = mwexec("/sbin/mount -t ufs -o acls {$readonly} {$devname} '{$mountname}'");
				if (0 == $result) {
					write_log("Device {$devname} mounted using UFS on '{$mountname}'");
					$result = 0;
				} else {
					@rmdir("{$mountname}");
					write_log("Error: Failed to mount {$devname} using UFS");
					$result = 1;
				}
			}
			break;

		case "msdosfs":
			$result = mwexec("/sbin/mount -t msdosfs -o -u=ftp,-g=ftp,-m=777 {$readonly} {$devname} '{$mountname}'");
			if (0 == $result) {
				write_log("Device {$devname} mounted using FAT on '{$mountname}'");
				$result = 0;
			} else {
				write_log("Error: Failed to detect disk {$devname}, starting a FAT fsck");
				// Do a filesystem check.
				mwexec("/sbin/fsck -y -t msdosfs {$devname}");
				// Retry to mount the partition.
				$result = mwexec("/sbin/mount -t msdosfs -o -u=ftp,-g=ftp,-m=777 {$readonly} {$devname} '{$mountname}'");
				if (0 == $result) {
					write_log("Device {$devname} mounted using FAT on '{$mountname}'");
					$result = 0;
				} else {
					@rmdir("{$mountname}");
					write_log("Error: Failed to mount {$devname} using FAT");
					$result = 1;
				}
			}
			break;

		case "cd9660":
			$result = mwexec("/sbin/mount -t cd9660 {$devname} '{$mountname}'");
			if (0 == $result) {
				write_log("Device {$devname} mounted as CD/DVD on '{$mountname}'");
				$result = 0;
			} else {
				@rmdir("{$mountname}");
				write_log("Error: Failed to mount {$devname} as CD/DVD");
				$result = 1;
			}
			break;

		case "ntfs":
			// Load the kernel fuse module.
			mwexec("/sbin/kldload /boot/kernel/fuse.ko");
			$result = mwexec("/sbin/mount -t ntfs-3g -o force {$readonly} {$devname} '{$mountname}'"); // default options: silent,allow_other
			if (0 == $result) {
				write_log("Disk {$devname} mounted using NTFS on '{$mountname}'");
				$result = 0;
			} else {
				@rmdir("{$mountname}");
				write_log("Error: Failed to mount {$devname} using NTFS.");
				$result = 1;
			}
			break;

		case "ext2fs":
			$result = mwexec("/sbin/mount -t ext2fs {$readonly} {$devname} '{$mountname}'");
			if (0 == $result)	{
				write_log("Disk {$devname} mounted using EXT2 on '{$mountname}'");
				$result = 0;
			} else {
				write_log("Error: Failed to detect disk {$devname}, starting a e2fsck");
				// Do a filesystem check.
				mwexec("/usr/local/sbin/e2fsck -f -p {$devname}");
				// Retry to mount the partition.
				$result = mwexec("/sbin/mount -t ext2fs {$readonly} {$devname} '{$mountname}'");
				if (0 == $result) {
					write_log("Disk {$devname} mounted using EXT2 on '{$mountname}'");
					$result = 0;
				} else {
					@rmdir("{$mountname}");
					write_log("Error: Can't mount {$devname} using EXT2");
					$result = 1;
				}
			}
			break;
	}

	// Modify directory mode to 0777. Check if target directory is writable,
	// otherwise we will get an error message.
	// For ISO's this may be 'chmod: /mnt/xxx: Read-only file system)'.
	if (file_exists($mountname) && is_writable($mountname)) {
		if (!@chmod($mountname, 0777)) {
			write_log("Error: Can't reset mode 0777 on directory {$mountname}");
		}
	}

	return $result;
}

// Umount the specified configured mount point.
// Return 0 is successfull, 1 if error
function disks_umount($mount)
{
	// Get memory disk unit for ISOs.
	if ("iso" === $mount['type']) {
		// Get current mount list.
		$amountlist = get_mounts_list();

		foreach ($amountlist as $amountk => $amountv) {
			if (0 == strcmp($amountv['sharename'], $mount['sharename'])) {
				$mdunit = substr($amountv['mdisk'], 2); // Remove 'md' to get unit id.
				break;
			}
		}
	}

	// Umount mount point.
	$mountname = "/mnt/{$mount['sharename']}";

	if (mwexec("/sbin/umount '{$mountname}'") == 0) {
		if(@rmdir("{$mountname}")) {
			write_log("Sucessfull umounted '{$mountname}'");
			$result = 0;
		} else {
			write_log("Error: Failed to umount '{$mountname}'");
			$result = 1;
		}
	} else {
		write_log("Error: Failed to umount '{$mountname}'");
		$result = 1;
	}

	// Detach memory disk for ISOs.
	if (0 == $result && "iso" === $mount['type']) {
		$result = mwexec("/sbin/mdconfig -d -u {$mdunit}");
	}

	return $result;
}

// Advanced unmount the specified mount point without using the sharename,
// e.g. used when the 'sharename' has been modified/changed. Use the device name instead.
// Return 0 if successful, 1 if error
function disks_umount_ex($mount)
{
	// Get current mount list.
	$amountlist = get_mounts_list();

	foreach ($amountlist as $amountk => $amountv) {
		if (0 == strcmp($amountv['fullname'], $mount['fullname'])) {
			$mountname = $amountv['mp'];
			break;
		}
	}

	if ($mountname) {
		if (mwexec("/sbin/umount '{$mountname}'") == 0) {
			if(@rmdir("{$mountname}")) {
				write_log("Sucessfull umounted '{$mountname}'");
				$result = 0;
			} else {
				write_log("Error: Failed to umount '{$mountname}'");
				$result = 1;
			}
		} else {
			write_log("Error: Failed to umount '{$mountname}'");
			$result = 1;
		}
	}

	return $result;
}

// Check if mount point is mounted.
// $mount - Mount point config array
// Return 0 if not mounted, otherwise 1
function disks_ismounted($mount)
{
	// MUST CHECK IF IT'S NTFS filesystem.
	if (0 == strcmp($mount['fstype'],"ntfs")) {
		return disks_ismounted_ex($mount['sharename'],"sharename");
	} else {
		return disks_ismounted_ex($mount['fullname'],"fullname");
	}
}

// Check if mount point is mounted.
// $var - The attribute value to search for
// $key - The attribute name to search for
// e.g.:  disks_ismounted_ex("/dev/ad0s1","fullname");
//        disks_ismounted_ex("data","sharename");
//        disks_ismounted_ex("ad0","mdisk");
// Return 0 if not mounted, otherwise 1
function disks_ismounted_ex($var,$key)
{
	$result = 0;

	// Get current mount list.
	$amountlist = get_mounts_list();

	foreach ($amountlist as $amountk => $amountv) {
		switch ($key) {
			case "mdisk":
			case "fullname":
				// Must found the $val (e.g. ad0) in 'mdisk' (e.g. ad0s1).
				// Must found the $val (e.g. /dev/ad1) in 'fullname' (e.g. /dev/ad1s1).
				if (false !== strpos($amountv[$key], $var)) {
					$result = 1;
					break;
				}
				break;

			default:
				if (0 == strcmp($amountv[$key], $var)) {
					$result = 1;
					break;
				}
				break;
		}
	}

	return $result;
}

// This option check if the configured disk is online (detected by the system).
// $disk - Disk array
//    (
//        [type] => IDE
//        [desc] => QUANTUM FIREBALL EX6.4A/A0A.0D00
//        [size] => 6149MB
//        [name] => ad0
//        [fullname] => /dev/ad0
//    )
// Return: "MISSING", disk isn't detected by OS
//         "ONLINE", disk is online
function disks_status($disk)
{
	$result = "MISSING";

	// Get list of detected disks (ATA,SCSI,CDROM,...).
	$adetectedlist = array_merge((array)get_physical_disks_list(),(array)get_cdrom_list());

	foreach ($adetectedlist as $detectedv) {
		if (0 == strcmp($detectedv['name'], $disk['name'])) {
			$result = "ONLINE";
			break;
		}
	}

	return $result;
}

// Configure, create and start gvinum volume.
// Return 0 if successful, 1 if error
function disks_raid_gvinum_configure()
{
	global $config, $g;

	/* Generate the raid.conf file */
	if ($config['gvinum']['vdisk']) 	{
		foreach ($config['gvinum']['vdisk'] as $a_raid_conf) 		{
			if (file_exists($g['varrun_path'] . "/raid.conf.dirty") && !in_array("{$a_raid_conf['name']}\n",file($g['varrun_path'] . "/raid.conf.dirty")))
				continue;

			/* generate raid.conf */
			$fd = fopen("{$g['varetc_path']}/raid-{$a_raid_conf['name']}.conf", "w");
			if (!$fd) 			{
				write_console("Error: Failed to open raid.conf in services_raid_configure().\n");
				return 1;
			}

			$raidconf="";
			foreach ($a_raid_conf['diskr'] as $diskrk => $diskrv) 	{
				$raidconf .= <<<EOD
drive disk_{$diskrv} device {$diskrv}

EOD;
			}

			$raidconf .= <<<EOD
volume {$a_raid_conf['name']}

EOD;
			switch ($a_raid_conf['type']) 	{
					case "0":
						$raidconf .= <<<EOD
plex org striped 256k

EOD;
						foreach ($a_raid_conf['diskr'] as $diskrk => $diskrv) 	{
							/* Get the disksize */
							$disksize=get_disks_size($diskrv);
							/* Remove the ending 'B' in 'MB' */
							$disksize=rtrim($disksize, 'B');
							/* $raidconf .= <<<EOD
sd length {$disksize} drive disk_{$diskrv}

EOD; */
							$raidconf .= <<<EOD
sd length 0 drive disk_{$diskrv}

EOD;
						}
						break;
					case "1":
						foreach ($a_raid_conf['diskr'] as $diskrk => $diskrv) 	{
							$raidconf .= <<<EOD
plex org concat

EOD;
							/* Get the disksize */
							$disksize=get_disks_size($diskrv);
							/* Remove the ending 'B' in 'MB' */
							$disksize=rtrim($disksize, 'B');
							/* $raidconf .= <<<EOD
sd length {$disksize} drive disk_{$diskrv}

EOD; */

							$raidconf .= <<<EOD
sd length 0 drive disk_{$diskrv}

EOD;
						}

						break;
					case "5":
					$raidconf .= <<<EOD
plex org raid5 256k

EOD;
						foreach ($a_raid_conf['diskr'] as $diskrk => $diskrv)	{
							/* Get the disksize */
							$disksize=get_disks_size($diskrv);
							/* Remove the ending 'B' in 'MB' */
							$disksize=rtrim($disksize, 'B');
							/* $raidconf .= <<<EOD
sd length {$disksize} drive disk_{$diskrv}

EOD; */

							$raidconf .= <<<EOD
sd length 0 drive disk_{$diskrv}

EOD;
						}
						break;
			}
			fwrite($fd, $raidconf);
			fclose($fd);

			/* Create each volume */
			mwexec("/sbin/gvinum create {$g['varetc_path']}/raid-{$a_raid_conf['name']}.conf");
		}

		/* start each volume */
		foreach ($config['gvinum']['vdisk'] as $a_raid_conf)	{
			exec("/sbin/gvinum lv $raidname", $rawdata);
			if (strpos($rawdata[0],"State: up" )>= 0)
				continue;
			mwexec("/sbin/gvinum start {$a_raid_conf['name']}");
		}
	}

	return 0;
}

// Configure, create and start gmirror volume.
// Return 0 if successful, 1 if error
function disks_raid_gmirror_configure()
{
	global $config;

	if ($config['gmirror']['vdisk']) {
		// Load gmirror
		mwexec("/sbin/gmirror load");

		foreach ($config['gmirror']['vdisk'] as $a_raid_conf) {
			/* Create each volume */
			$cmd = "/sbin/gmirror label -b {$a_raid_conf['balance']} {$a_raid_conf['name']} ";
			foreach ($a_raid_conf['diskr'] as $diskrk => $diskrv) 	{
				$cmd .= "{$diskrv} ";
			}
			mwexec($cmd);
		}
	}

	return 0;
}

// Configure, create and start gconcat volume.
// Return 0 if successful, 1 if error
function disks_raid_gconcat_configure()
{
	global $config;

	if ($config['gconcat']['vdisk']) {
		// Load gconcat
		mwexec("/sbin/gconcat load");

		foreach ($config['gconcat']['vdisk'] as $a_raid_conf) {
			/* Create each volume */
			$cmd = "/sbin/gconcat label {$a_raid_conf['name']} ";
			foreach ($a_raid_conf['diskr'] as $diskrk => $diskrv) 	{
				$cmd .= "{$diskrv} ";
			}
			mwexec($cmd);
		}
	}

	return 0;
}

// Configure, create and start gstripe volume.
// Return 0 if successful, 1 if error
function disks_raid_gstripe_configure()
{
	global $config;

	if ($config['gstripe']['vdisk']) {
		// Load gstripe
		mwexec("/sbin/gstripe load");

		foreach ($config['gstripe']['vdisk'] as $a_raid_conf) {
			/* Create each volume */
			$cmd = "/sbin/gstripe label {$a_raid_conf['name']} ";
			foreach ($a_raid_conf['diskr'] as $diskrk => $diskrv) 	{
				$cmd .= "{$diskrv} ";
			}
			mwexec($cmd);
		}
	}

	return 0;
}

// Configure, create and start graid5 volume.
// Return 0 if successful, 1 if error
function disks_raid_graid5_configure()
{
	global $config;

	if ($config['graid5']['vdisk']) {
		// Load graid5
		mwexec("/sbin/graid5 load");

		foreach ($config['graid5']['vdisk'] as $a_raid_conf) 	{
			/* Create each volume */
			$cmd = "/sbin/graid5 label -s 131072 {$a_raid_conf['name']} ";
			foreach ($a_raid_conf['diskr'] as $diskrk => $diskrv) {
				$cmd .= "{$diskrv} ";
			}
			mwexec($cmd);
		}

		//Tune graid5
		disks_raid_graid5_tune();
	}

	return 0;
}

// Start all geom RAID volumes.
// Return 0 if successful, 1 if error
function disks_raid_start()
{
	/* WARNING:  Must change this code for advanced RAID configuration ex: RAID1+0
	Geom RAID volume must be started in 'intelligent' sort, for RAID1+0, gmirror must
	be started before gstripe, etc... */
	disks_raid_gvinum_start();
	disks_raid_gmirror_start();
	disks_raid_gstripe_start();
	disks_raid_gconcat_start();
	disks_raid_graid5_start();

	return 0;
}

// Start geom vinum volumes.
// Return 0 if successful, 1 if error
function disks_raid_gvinum_start()
{
	global $config, $g;

	if ($config['gvinum']['vdisk']) {
		if (is_booting())
			write_console("Start gvinum raid... ");

		/* start each volume */
		foreach ($config['gvinum']['vdisk'] as $a_raid_conf) 	{
			mwexec("/sbin/gvinum start {$a_raid_conf['name']}");
		}

		if (is_booting())
			write_console("done\n");
	}

	return 0;
}

// Start geom mirror volumes.
// Return 0 if successful, 1 if error
function disks_raid_gmirror_start()
{
	global $config, $g;

	/* Start Geom mirror */
	if ($config['gmirror']['vdisk']) {
		if (is_booting())
			write_console("Start gmirror raid... ");

		// Load geom mirror module
		mwexec("/sbin/gmirror load");

		if (is_booting())
			write_console("done\n");
	}

	return 0;
}

// Start geom concat volumes.
// Return 0 if successful, 1 if error
function disks_raid_gconcat_start()
{
	global $config, $g;

	/* Start Geom concat */
	if ($config['gconcat']['vdisk']) {
		if (is_booting())
			write_console("Start gconcat raid... ");

		// Load geom concat module
		mwexec("/sbin/gconcat load");

		if (is_booting())
			write_console("done\n");
	}

	return 0;
}

// Start geom stripe volumes.
// Return 0 if successful, 1 if error
function disks_raid_gstripe_start()
{
	global $config, $g;

	/* Start Geom stripe */
	if ($config['gstripe']['vdisk']) {
		if (is_booting())
			write_console("Start gstripe raid... ");

		// Load geom stripe module
		mwexec("/sbin/gstripe load");

		if (is_booting())
			write_console("done\n");
	}

	return 0;
}

// Start geom raid5 volumes.
// Return 0 if successful, 1 if error
function disks_raid_graid5_start()
{
	global $config, $g;

	/* Start Geom RAID5 */
	if ($config['graid5']['vdisk']) {
		if (is_booting())
			write_console("Start graid5 raid... ");

		// Load geom raid5 module
		mwexec("/sbin/graid5 load");

		//Tune graid5
		disks_raid_graid5_tune();

		if (is_booting()) {
			write_console("done\n");
		}

	}

	return 0;
}

// Optimize geom raid5 RAM use.
// Return none
function disks_raid_graid5_tune()
{
	/* Get RAM informations. */
	$raminfo = get_ram_info();
	/* Calculate real RAM in MB. */
	$realram = round($raminfo['real'] / 1024 / 1024);

	if ($realram <= 128) {
		write_log("Optimizing graid5 for systems with <= 128MB of RAM");
		mwexec('sysctl value kern.geom.raid5.maxwql=10');
		mwexec('sysctl value kern.geom.raid5.maxmem=1100000');
	} else if ($realram <= 256) {
		write_log("Optimizing graid5 for systems with <= 256MB of RAM");
		mwexec('sysctl value kern.geom.raid5.maxwql=20');
		mwexec('sysctl value kern.geom.raid5.maxmem=2000000');
	} else {
		write_log("Detected >= 512MB of RAM... No need to optimize RAM usage for graid5");
	}

	// Modify ATA timeout to 15 seconds.
	mwexec('sysctl hw.ata.to=15');
}

// Stop all geom RAID volumes.
// Return 0 if successful, 1 if error
function disks_raid_stop()
{
	/* WARNING:  Must change this code for advanced RAID configuration ex: RAID1+0
	Geom RAID volume must be started in 'intelligent' sort, for RAID1+0, gmirror must
	be started before gstripe, etc... */

	disks_raid_gvinum_stop();
	disks_raid_graid5_stop();
	disks_raid_gstripe_stop();
	disks_raid_gconcat_stop();
	disks_raid_gmirror_stop();

	return 0;
}

// Stop all geom gvinum volumes.
// Return 0 if successful, 1 if error
function disks_raid_gvinum_stop()
{
	global $config;

	/* Stop geom vinum */
	if ($config['gvinum']['vdisk']) {
		foreach ($config['gvinum']['vdisk'] as $a_raid_conf) 	{
			mwexec("/sbin/gvinum stop {$a_raid_conf['name']}");
		}
	}

	return 0;
}

// Stop all geom mirror volumes.
// Return 0 if successful, 1 if error
function disks_raid_gmirror_stop()
{
	global $config;

	/* Stop geom mirror */
	if ($config['gmirror']['vdisk']) {
		foreach ($config['gmirror']['vdisk'] as $a_raid_conf) 	{
			mwexec("/sbin/gmirror stop {$a_raid_conf['name']}");
		}
	}

	return 0;
}

// Stop all geom concat volumes.
// Return 0 if successful, 1 if error
function disks_raid_gconcat_stop()
{
	global $config;

	/* Stop geom concat */
	if ($config['gconcat']['vdisk']) {
		foreach ($config['gconcat']['vdisk'] as $a_raid_conf) 	{
			mwexec("/sbin/gconcat stop {$a_raid_conf['name']}");
		}
	}

	return 0;
}

// Stop all geom stripe volumes.
// Return 0 if successful, 1 if error
function disks_raid_gstripe_stop()
{
	global $config;

	/* Stop geom stripe */
	if ($config['gstripe']['vdisk']) {
		foreach ($config['gstripe']['vdisk'] as $a_raid_conf) 	{
			mwexec("/sbin/gstripe stop {$a_raid_conf['name']}");
		}
	}

	return 0;
}

// Stop all geom raid5 volumes.
// Return 0 if successful, 1 if error
function disks_raid_graid5_stop()
{
	global $config;

	/* Stop geom raid5 */
	if ($config['graid5']['vdisk']) {
		foreach ($config['graid5']['vdisk'] as $a_raid_conf) 	{
			mwexec("/sbin/graid5 stop {$a_raid_conf['name']}");
		}
	}

	return 0;
}

// Delete geom gvinum volume given in parameter.
// Return 0 if successful, 1 if error
function disks_raid_gvinum_delete($raidname)
{
	global $config;

	exec("/sbin/gvinum lv {$raidname}", $rawdata);

	if (strpos($rawdata[0],"State: up") === false) {
		return 0;
	}

	mwexec("/sbin/gvinum rm -r {$raidname}");

	foreach ($config['gvinum']['vdisk'] as $a_raid) {
		if ($a_raid['name'] === $raidname) {
			foreach ($a_raid['diskr'] as $disk){
				mwexec("/sbin/gvinum rm -r disk_{$disk}");
			}
		}
	}

	return 0;
}

// Delete geom mirror volume given in parameter.
// Return 0 if successful, 1 if error
function disks_raid_gmirror_delete($raidname)
{
	global $config;

	// Stop the volume
	mwexec("/sbin/gmirror stop {$raidname}");

	// Clear the gmirror information on the hard drive
	foreach ($config['gmirror']['vdisk'] as $a_raid) {
		if ($a_raid['name'] === $raidname) {
			foreach ($a_raid['diskr'] as $disk) {
				mwexec("/sbin/gmirror clear {$disk}");
			}
		}
	}

	return 0;
}

// Delete geom concat volume given in parameter.
// Return 0 if successful, 1 if error
function disks_raid_gconcat_delete($raidname)
{
	global $config;

	// Stop the volume
	mwexec("/sbin/gconcat stop {$raidname}");

	// Clear the gconcat information on the hard drive
	foreach ($config['gconcat']['vdisk'] as $a_raid) {
		if ($a_raid['name'] === $raidname) {
			foreach ($a_raid['diskr'] as $disk) {
				mwexec("/sbin/gconcat clear {$disk}");
			}
			mwexec("/sbin/gconcat destroy {$raidname}");
		}
	}

	return 0;
}

// Delete geom stripe volume given in parameter.
// Return 0 if successful, 1 if error
function disks_raid_gstripe_delete($raidname)
{
	global $config;

	// Stop the volume
	mwexec("/sbin/gstripe stop {$raidname}");

	// Clear the gconcat information on the hard drive
	foreach ($config['gstripe']['vdisk'] as $a_raid) {
		if ($a_raid['name'] === $raidname) 	{
			foreach ($a_raid['diskr'] as $disk) {
				mwexec("/sbin/gstripe clear {$disk}");
			}
			mwexec("/sbin/gstripe destroy {$raidname}");
		}
	}

	return 0;
}

// Delete geom raid5 volume given in parameter.
// Return 0 if successful, 1 if error
function disks_raid_graid5_delete($raidname)
{
	global $config;

	// Stop the volume
	// Arne Woerner Advice: Don't stop the volume before deleting it.
	// mwexec("/sbin/graid5 stop {$raidname}");

	// The volume can disapear a few second after stop
	//sleep(2);

	// Clear the graid5 information on the hard drive
	foreach ($config['graid5']['vdisk'] as $a_raid) {
		if ($a_raid['name'] === $raidname) 	{
			foreach ($a_raid['diskr'] as $disk){
				// Get the name of the provider.
				$id = array_search_ex($disk, $config['disks']['disk'], "fullname");
				$provider = $config['disks']['disk'][$id]['name'];
				// Remove provider.
				mwexec("/sbin/graid5 remove {$raidname} {$provider}");
			}
			mwexec("/sbin/graid5 destroy {$raidname}");
		}
	}

	return 0;
}

// Initialize provider which needs to be encrypted.
// Return 0 if successful, 1 if error
function disks_geli_init($provider, $aalgo, $ealgo, $passphrase, $verbose = false)
{
	$param = "-t";
	$result = 1;

	// Add additional parameters.
	if (true == $verbose) $param .= " -v";
	if (0 !== strcmp($aalgo,"none")) $param .= " -a {$aalgo}";
	if (!empty($ealgo)) $param .= " -e {$ealgo}";

	// Init disk.
	system("(/bin/echo {$passphrase}; /bin/echo {$passphrase}) | /sbin/geli init {$param} {$provider} 2>&1", $result);

	return $result;
}

// Check the status of a geli encrypted disk
// Return 0 if successful, 1 if error
function disks_geli_check($gelifullname)
{
	$result = 1;
	if (file_exists($gelifullname)) {
		$result = 0;
	}
	return $result;
}

// Attach the given provider. The master key will be decrypted
// using the given passphrase/keyfile and a new GEOM provider
// will be created using the given provider's name with an ".eli"
// suffix.
// Return 0 if successful, 1 if error
function disks_geli_attach($provider, $passphrase, $verbose = false)
{
	$param = "-t";
	$result = 1;

	// Add additional parameters.
	if (true == $verbose) $param .= " -v";

	// Attach disk.
	system("(/bin/echo {$passphrase}; /bin/echo {$passphrase}) | /sbin/geli attach {$param} {$provider} 2>&1", $result);

	return $result;
}

// Detach the given providers, which means remove the devfs entry
// and clear the keys from memory.
// Return 0 if successful, 1 if error
function disks_geli_detach($provider, $verbose = false)
{
	if (true == $verbose)
		$result = system("/sbin/geli detach {$provider} 2>&1", $result);
	else
		$result = mwexec("/sbin/geli detach {$provider}");

	return $result;
}

// Kill (destroy) a geli encrypted volume.
// Return 0 if successful, 1 if error
function disks_geli_kill($provider)
{
	$result = mwexec("/sbin/geli kill {$provider}");
	return $result;
}

// Detach all geli encrypted disks
// Return 0 if successful, 1 if error
function disks_geli_detach_all()
{
	global $config;

	$result = 0;

	if (!is_array($config['geli']['vdisk']))
		$config['geli']['vdisk'] = array();

	$a_geli = &$config['geli']['vdisk'];

	foreach($a_geli as $geli) {
		$result |= disks_geli_detach($geli['name']) ;
	}

	return $result;
}

// Change or setup (if not yet initialized) selected key.
// The passphrase can always be changed: for an attached provider and
// a detached provider. When a provider is attached, the user does not
// have to provide an old passphrase.
// provider: Name of the provider, e.g. da0, ad3, ...
// oldpassphrase: Old passphrase
// passphrase: New passphrase
// verbose: Display additional informations
// Return 0 if successful, 1 if error
function disks_geli_setkey($provider, $oldpassphrase, $passphrase, $verbose = false)
{
	$param = "-t";
	$result = 1;

	// Add additional parameters.
	if (true == $verbose) $param .= " -v";

	if (0 == disks_geli_check("/dev/{$provider}.eli")) {
		// Provider is already attached.
		system("(/bin/echo {$passphrase}; /bin/echo {$passphrase}) | /sbin/geli setkey {$param} {$provider} 2>&1", $result);
	} else {
		// Provider has not already been attached.
		system("(/bin/echo {$oldpassphrase}; /bin/echo {$passphrase}; /bin/echo {$passphrase}) | /sbin/geli setkey {$param} {$provider} 2>&1", $result);
	}

	return $result;
}

// Format disk.
// Return none
function disks_format($disk,$type,$notinitmbr,$minspace,$volumelabel)
{
	if (!$notinitmbr) {
		echo gettext("Erasing MBR and all partitions").".\n";
		system("dd if=/dev/zero of={$disk} bs=32k count=640");
	}

	switch ($type) {
		case "ufsgpt":
			// Create GPT partition table
			echo sprintf(gettext("Destroying old %s information"), "GPT").":\n";
			system("/sbin/gpt destroy {$disk}");
			echo sprintf(gettext("Creating %s partition"), "GPT").":\n";
			system("/sbin/gpt create -f {$disk}");
			system("/sbin/gpt add -t ufs {$disk}");

			// Create filesystem
			echo gettext("Creating filesystem with 'Soft Updates'").":\n";
			$param = "-U";
			if (!empty($volumelabel))
				$param .= " -L " . escapeshellarg($volumelabel);
			if (!empty($minspace))
				$param .= " -m {$minspace}";
			system("/sbin/newfs {$param} {$disk}p1");
			echo gettext("Done")."!\n";
			break;

		case "softraid":
			// Initialise disk
			echo gettext("Formating disk").".\n";
			system("/bin/dd if=/dev/zero of={$disk} bs=1m count=16");
			echo gettext("Done")."!\n";
			break;

		case "msdos":
			// Initialize disk
			echo gettext("Creating partition").":\n";
			system("/sbin/fdisk -I -v {$disk}");

			// Initialize the partition
			echo gettext("Initializing partition").".\n";
			system("/bin/dd if=/dev/zero of={$disk}s1 bs=32k count=16");

			// Create filesystem
			echo gettext("Creating filesystem").":\n";
			$param = "-F 32";
			if (!empty($volumelabel))
				$param .= " -L " . escapeshellarg($volumelabel);
			system("/sbin/newfs_msdos {$param} {$disk}s1");
			echo "Done!\n";
			break;

		case "ext2":
			// Initialize disk
			echo gettext("Creating partition").":\n";
			system("/sbin/fdisk -I -v {$disk}");

			// Initialize the partition
			echo gettext("Initializing partition").".\n";
			system("/bin/dd if=/dev/zero of={$disk}s1 bs=32k count=16");

			// Create filesystem
			echo gettext("Creating filesystem").":\n";
			system("/usr/local/sbin/mke2fs {$disk}s1");
			echo "Done!\n";
			break;
	}
}

// File system consistency check and interactive repair.
// The filesystem type will be get from the mount configuration information.
// Parameter:
// device - Name of the device (e.g. /dev/ad1s1).
// umount - Unmount disk if necessary.
// Return 0 if successful, 1 if error
function disks_fsck($device,$umount = false)
{
	global $config;

	// Check if there exits any mounts.
	if(!is_array($config['mounts']['mount']))
		return 1;

	// Get the id of the disk.
	$id = array_search_ex($device, $config['mounts']['mount'], "fullname");

	// Get the mount configuration.
	$mount = $config['mounts']['mount'][$id];

	// Check if disk is mounted.
	$ismounted = disks_ismounted($mount);

	// Umount disk temporary if necessary.
	if($umount && $ismounted) {
		echo("<strong class='red'>" . gettext("Note") . ":</strong> " . gettext("The disk is currently mounted! The mount point will be removed temporary to perform selected command.") . "<br><br>");
		disks_umount($mount);
	}

	switch($mount['fstype']) {
		case "":
		case "ufs":
			system("/sbin/fsck -t ufs -y -f " . escapeshellarg($device));
			break;
		case "msdosfs":
			system("/sbin/fsck -t msdos -y -f " . escapeshellarg($device));
			break;
	}

	// Remount disk again if necessary.
	if($umount && $ismounted) {
		disks_mount($mount);
	}

	return 0;
}

// Get list of partition information from disk.
// $disk - Name of the disk (e.g. ad0)
// Result is in the form:
// [1] => Array
// (
// 	[start] => 31
// 	[size]  => 409169
// 	[type]  => 0xa5
// 	[flags] => 0x80
// )
//
// Called in disks_manage_tools.php
// Return array of partition info
function disks_get_partition_info($disk) {
  exec("/sbin/fdisk -s {$disk}", $rawdata);

  array_shift($rawdata);
  array_shift($rawdata);

  $result = array();

  foreach($rawdata as $partinfo)   {
    $apartinfo = preg_split("/\s+/", $partinfo);
    $partid = chop($apartinfo[1],":");

    $result[$partid] = array();
		$result[$partid]['start'] = chop($apartinfo[2]);
		$result[$partid]['size']  = chop($apartinfo[3]);
		$result[$partid]['type']  = chop($apartinfo[4]);
		$result[$partid]['flags'] = chop($apartinfo[5]);
  }

  return $result;
}

// Get disk informations.
// $disk - Name of the disk (e.g. ad0)
// Result is in the form:
// [1] => Array
// (
// 	[name] => ad0
// 	[sectorsize]  => 512
// 	[mediasize_bytes]  => 5242503168
// 	[mediasize_mbytes]  => 4999
// 	[mediasize_sectors]  => 10239264
// 	[cylinders] => 10158
// 	[heads] => 16
// 	[sectors] => 63
// )
// Return array containing diskinfo.
function disks_get_diskinfo($disk)
{
	exec("/usr/sbin/diskinfo {$disk}", $rawdata);

	$diskinfo = preg_split("/\s+/", $rawdata[0]);

	$result = array();
	$result[name] = chop($diskinfo[0]);
	$result[sectorsize] = chop($diskinfo[1]);
	$result[mediasize_bytes] = chop($diskinfo[2]);
	$result[mediasize_mbytes] = ceil(chop($diskinfo[2]) / 1024 / 1024);
	$result[mediasize_sectors] = chop($diskinfo[3]);
	$result[cylinders] = chop($diskinfo[4]);
	$result[heads] = chop($diskinfo[5]);
	$result[sectors] = chop($diskinfo[6]);

	return $result;
}
?>
