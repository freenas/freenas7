<?php
/*
	system.inc
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
require_once("util.inc");

/* Mount the CDROM return 0 if OK, 1 if error.
   Called by install script. Return 0 if OK, 1 if not. */
function install_mount_cd($cdrom)
{
	write_console("\nCreating mount point for the CDROM...\n");

	if (is_dir("/mnt/cdrom")) {
		write_console("Error: Temporary directory /mnt/cdrom for CDROM already exists!\n");
		return 1;
	}

	/* Creating tempo directory for cdrom */
	if (mwexec("/bin/mkdir /mnt/cdrom")) {
		write_console("Error: Can't create temporary directory /mnt/cdrom!\n");
		return 1;
	}

	/* Monting the CDROM */
	write_console("Mounting CDROM...\n");
	if (mwexec("/sbin/mount_cd9660 /dev/{$cdrom} /mnt/cdrom")) {
		write_console("Error: Failed to mount device {$cdrom}!\n");
		@rmdir ("/mnt/cdrom");
		return 1;
	}

	return 0;
}

/* Unmount CDROM and destination disk.
   Called by install script. Return 0 if OK, 1 if not. */
function install_unmount_cd()
{
	$result = 0;

	write_console("Unmount CDROM...\n");

	/* Unmounting cdrom */
	if (mwexec("/sbin/umount /mnt/cdrom")) {
		$result = 1;
	}

	// Remove tempo directory
	@rmdir ("/mnt/cdrom");

	return $result;
}

/* Create to partition on the destination hard drive.
   Called by install script. Return 0 if OK, 1 if not. */
function install_init_halfdisk($harddrive)
{
	/* Part of install process: Initialize the destination disk with 2 partitions */
	/* Create two partitions, the first as 32MB */
	if (1 == install_fdisk($harddrive,32)) {
		write_console("Error: Failed to create partitions!\n");
		return 1;
	}

	if (mwexec("(/bin/echo y; /bin/echo y) | /sbin/fdisk -B -b /boot/mbr {$harddrive}")) {
		write_console("Error: Can't fdisk the destination drive '{$harddrive}'!\n");
		return 1;
	}

	/* Must wait that the /dev is upatded with the new information */
	write_console("Waiting for system update...");
	$devtotest="/dev/{$harddrive}s2";
	$i=0;
	while (!file_exists($devtotest)) {
		sleep(1);
		write_console(".");
		$i++;
		if ($i==20) {
			write_console("\nSystem not updated after 20 seconds!\n");
			return 1;
		}
	}
	write_console("\n");

	write_console("Creating BSD label...\n");
	if (mwexec("/sbin/bsdlabel -B -w -b /boot/boot {$harddrive}s1 auto")) {
		write_console("Error: Failed to label '{$harddrive}s1'!\n");
		return 1;
	}
	if (mwexec("/sbin/bsdlabel -w {$harddrive}s2 auto")) {
		write_console("Error: Failed to label '{$harddrive}s2'!\n");
		return 1;
	}

	write_console("Modify BSD label information...\n");
	install_bsdlabel($harddrive, "s1", "4.2BSD");
	install_bsdlabel($harddrive, "s2", "4.2BSD");

	write_console("Creating filesystem...\n");
	if (mwexec("/sbin/newfs -U /dev/{$harddrive}s1")) {
		write_console("Error: Failed to create filesystem on '/dev/{$harddrive}s1'!\n");
		return 1;
	}
	if (mwexec("/sbin/newfs -U /dev/{$harddrive}s2")) {
		write_console("Error: Failed to create filesystem on '/dev/{$harddrive}s2'!\n");
		return 1;
	}

	return 0;
}

function install_init_harddrive($harddrive)
{
	/* Part of install process: Initialize the destination disk with 2 partitions */
	/* Create two partitions, the first as 128MB */
	if (1 == install_fdisk($harddrive,128)) {
		write_console("Error: Failed to create partitions!\n");
		return 1;
	}

	if (mwexec("(/bin/echo y; /bin/echo y) | /sbin/fdisk -B -b /boot/mbr {$harddrive}")) {
		write_console("Error: Can't fdisk the destination drive '{$harddrive}'!\n");
		return 1;
	}

	/* Must wait that the /dev is upatded with the new information */
	write_console("Waiting for system update...");
	$devtotest="/dev/{$harddrive}s2";
	$i=0;
	while (!file_exists($devtotest)) {
		sleep(1);
		write_console(".");
		$i++;
		if ($i==20) {
			write_console(" System not updated after 20 seconds!\n");
			return 1;
		}
	}
	write_console("\n");

	write_console("Creating BSD label...\n");
	if (mwexec("/sbin/bsdlabel -B -w -b /boot/boot {$harddrive}s1 auto")) {
		write_console("Error: Failed to label '{$harddrive}s1'!\n");
		return 1;
	}
	if (mwexec("/sbin/bsdlabel -w {$harddrive}s2 auto")) {
		write_console("Error: Failed to label '{$harddrive}s2'!\n");
		return 1;
	}

	write_console("Modify BSD label information...\n");
	install_bsdlabel($harddrive, "s1", "4.2BSD");
	install_bsdlabel($harddrive, "s2", "4.2BSD");

	write_console("Creating filesystem...\n");
	if (mwexec("/sbin/newfs /dev/{$harddrive}s1")) {
		write_console("Error: Failed to create filesystem on '/dev/{$harddrive}s1'!\n");
		return 1;
	}
	if (mwexec("/sbin/newfs /dev/{$harddrive}s2")) {
		write_console("Error: Failed to create filesystem on '/dev/{$harddrive}s2'!\n");
		return 1;
	}

	return 0;
}

/* Install the IMG on the destination harddrive.
   Called by install script. Return 0 if OK, 1 if not. */
function install_dd_image($harddrive)
{
	global $g;

	/* Part of install process: dd image file on the destination disk */
	// It's perhaps this brut method 'dd' that create some problem with BIOS configured on LBA or not mode....
	write_console("Installation...\n");
	$imgfilename=get_product_name(). "-{$g['arch']}-embedded.gz";
	if (mwexec("/usr/bin/gzip -cd /mnt/cdrom/{$imgfilename} | /bin/dd of=/dev/{$harddrive} > /dev/null 2>&1")) {
		write_console("Error: Failed to dd image on '/dev/{$harddrive}'!\n");
		return 1;
	}

	return 0;
}

/* Format the two partitions for 'full' install */
function install_format_full($harddrive)
{
	write_console("Formatting {$harddrive}s1a\n");
	$result = mwexec("/sbin/newfs -U {$harddrive}s1a");
	$result |= mwexec("/sbin/newfs -U {$harddrive}s2");
	return $result;
}


/* Install FreeNAS on the destination harddrive.
   Called by install script. Return 0 if OK, 1 if not. */
function install_harddrive_image($harddrive)
{
	global $g;

	/* Part of install process: dd image file on the destination disk */
	// It's perhaps this brut method 'dd' that create some problem with BIOS configured on LBA or not mode....
	write_console("Installation...\n");
	mwexec("/bin/mkdir /destdisk");
	mwexec("/sbin/mount /dev/{$harddrive}s1a /destdisk");

	write_console("Copying system files to disk {$harddrive}.\n");
	mwexec("/usr/bin/tar --exclude /destdisk --exclude /mnt --exclude /dev --exclude /var -cf - -C / ./ | tar -xvpf - -C /destdisk");
	mwexec("/bin/mkdir /destdisk/var");
	mwexec("/bin/mkdir /destdisk/dev");
	mwexec("/bin/mkdir /destdisk/mnt");
	mwexec("/bin/mkdir /destdisk/boot/defaults");
	mwexec("/bin/cp -v /mnt/cdrom/boot/* /destdisk/boot");
	mwexec("/bin/cp -v /mnt/cdrom/boot/defaults/* /destdisk/boot/defaults");
	mwexec("/bin/cp -v /mnt/cdrom/boot/kernel/* /destdisk/boot/kernel");

	// Generate new loader.conf file
	$fd = fopen("/destdisk/boot/loader.conf", "w");
	if (!$fd) {
		write_console("Error: Can't open boot/loader.conf in install_harddrive_image().\n");
		return 1;
	}

	$loaderconf .= <<<EOD
kernel="kernel"
bootfile="kernel"
kernel_options=""
kern.hz="100"
splash_bmp_load="YES"
bitmap_load="YES"
bitmap_name="/boot/splash.bmp"

EOD;

	fwrite($fd, $loaderconf);
	fclose($fd);

	// Set the release type.
	$fd = fopen("/destdisk/etc/platform", "w");
	if (!$fd) {
		write_console("Error: Failed to create '/etc/platform'.\n");
		return 1;
	}

	$platform .= <<<EOD
{$g['arch']}-full

EOD;

	fwrite($fd, $platform);
	fclose($fd);

	//Unzipping the Kernel
	mwexec("/usr/bin/gzip -d /destdisk/boot/kernel/kernel.gz");

	//Generating the FSTAB
	$fd = fopen("/destdisk/etc/fstab", "w");
	if (!$fd) {
		write_console("Error: Can't open fstab in install_harddrive_image().\n");
		return 1;
	}

	$fstab .= <<<EOD
# Device                Mountpoint      FStype  Options         Dump    Pass#
/dev/{$harddrive}s1a             /       ufs     rw              1       1

EOD;

	fwrite($fd, $fstab);
	fclose($fd);
	
	// Generating the /etc/cfdevice (this file is linked in /var/etc at bootup)
	// This file (more exatly /var/etc/cfdevice) is used by the firmware and mount check
	// This file is normally generated with 'liveCD' and 'embedded' during startup, but need to be created during install of 'full'

	$fd = fopen("/destdisk/etc/cfdevice", "w");
	if (!$fd) {
		write_console("Error: Can't open etc/cfdevice in install_harddrive_image().\n");
		return 1;
	}
	$cfdevice .= <<<EOD
{$harddrive}s1a

EOD;

	fwrite($fd, $cfdevice);
	fclose($fd);

	//unmounting destination disk
	mwexec("/sbin/umount /destdisk");

	return 0;
}

/* Upfrade a FreeNAS 'full' releaseon the destination harddrive.
   Called by install script. Return 0 if OK, 1 if not. */
function install_upgrade_full($harddrive)
{
	global $g;

	
	write_console("Upgrading...\n");
	
	mwexec("/bin/mkdir /destdisk");
	mwexec("/sbin/mount /dev/{$harddrive}s1a /destdisk");
	
	// Start pre-upgrade script
	mwexec("/etc/preupgrade.sh");
	
	// Install upgrade
	mwexec("/usr/bin/tar --exclude /destdisk --exclude /mnt --exclude /dev --exclude /var -cf - -C / ./ | tar -xvpf - -C /destdisk");
	mwexec("/bin/cp -v /mnt/cdrom/boot/* /destdisk/boot");
	mwexec("/bin/cp -v /mnt/cdrom/boot/defaults/* /destdisk/boot/defaults");
	mwexec("/bin/cp -v /mnt/cdrom/boot/kernel/* /destdisk/boot/kernel");
	
	// Start post-upgrade script
	mwexec("/etc/postupgrade.sh");
	
	//unmounting destination disk
	mwexec("/sbin/umount /destdisk");

	return 0;
}

/* Backup the configuration file on the hard drive/CF and unmount this drive
   Called by install script. Return 0 if OK, 1 if not. */
function install_backupconfig($disk)
{
	/* Creating tempo directory for the config file*/
	if (mwexec("/bin/mkdir /tmp/configbak")) {
		write_console("Error: Can't create /tmp/configbak!\n");
		return 1;
	}

	if (mwexec("/bin/mkdir /tmp/installdrive")) {
		write_console("Error: Can't create /tmp/installdrive!\n");
		return 1;
	}
	/* mounting the drive to be upgraded */
	if (mwexec("/sbin/mount /dev/{$disk} /tmp/installdrive")) {
		write_console("Error: Can't mount the drive to be upgraded!\n");
		$result = 1;
	}

	/* Backuping the config file */
	if (mwexec("cp -p /tmp/installdrive/conf/* /tmp/configbak")) {
		write_console("Error: Can't backup the config file!\n");
		return 1;
	}

	/* unmounting the drive to be upgraded */
	if (mwexec("/sbin/umount -f /dev/{$disk}")) {
		write_console("Error: Can't unmount the drive to be upgraded during config backup process!\n");
		$result = 1;
	}

	return 0;
}

function install_restoreconfig($disk)
{
	/* Mount the upgraded drive in RW mode */
	if (mwexec("/sbin/mount /dev/{$disk} /tmp/installdrive")) {
		write_console("Error: Can't mount the drive just upgraded in RW mode!\n");
		$result = 1;
	}

	/* Restoring the config file*/
	if (mwexec("cp -p /tmp/configbak/* /tmp/installdrive/conf")) {
		write_console("Error: Can't restore the config file!\n");
		return 1;
	}

	/* unmount the upgraded drive */
	if (mwexec("/sbin/umount -f /dev/{$disk}")) {
		write_console("Error: Can't unmount the drive just upgraded!\n");
		$result = 1;
	}

	return 0;
}

// Initialise HARD DRIVE for installation (creating 2 partition).
//Function need two parameters:
// - disk name ((ad0)
// - disk size in MB
// Return 0 if successful, 1 if error
function install_fdisk($harddrive,$size)
{
	global $config, $g;

	// Get disk informations.
	$diskinfo = disks_get_diskinfo($harddrive);

	// Check available disk space.
	if ($diskinfo['mediasize_mbytes'] <= $size) {
		write_console("Error: Not enough space available on disk {$diskinfo['name']}.\n");
		return 1;
	}

	// Setting partition size to $size (in MB).
	$part1_size_mbytes = $size;
	$part1_size_bytes = $part1_size_mbytes * 1024 * 1024;

	// Calculate partition sector sizes.
	$part1_size_sectors = $part1_size_bytes / $diskinfo['sectorsize'];
	$part2_size_sectors = $diskinfo['mediasize_sectors'] - $part1_size_sectors;

	// Create fdisk config file (fdisk.conf).
	$fd = fopen("{$g['varetc_path']}/fdisk.conf", "w");
	if (!$fd) {
		write_console("Error: Failed to create '{$g['varetc_path']}/fdisk.conf'.\n");
		return 1;
	}

	$fdiskconf .= <<<EOD
g c{$diskinfo['cylinders']} h{$diskinfo['heads']} s{$diskinfo['sectors']}
p 1 165 1 $part1_size_sectors
p 2 165 $part1_size_sectors $part2_size_sectors
p 3 0 0 0
p 4 0 0 0
a 1

EOD;

	fwrite($fd, $fdiskconf);
	fclose($fd);

	// Fdisk the disk.
	mwexec("/sbin/fdisk -f {$g['varetc_path']}/fdisk.conf /dev/{$harddrive}");

	return 0;
}

// Create disk label.
// Return none
function install_bsdlabel($harddrive,$partition,$type)
{
	global $config, $g;

	// Generating BSD Label table
	passthru("/sbin/bsdlabel {$harddrive}{$partition} > {$g['tmp_path']}/label.tmp");

	// put this file on a array
	$tableau = file("{$g['tmp_path']}/label.tmp");

	// Open this file in add mode
	$handle = fopen("{$g['tmp_path']}/label.tmp", 'a');

	while(list(,$val) = each($tableau)) {
		// If the line contain the word "unused"
		// Why did I replace 'a' with 'c' ? Must found the URL where I found this information
		if (ereg ("unused",$val)) {
			// Replacing c: by a:
			// Why ??? Must found the web page where I see this method
			$val = ereg_replace ("c:","a:", $val);
			// Peplacing unused by $type
			$val = ereg_replace ("unused",$type, $val);
			// Adding this line add the end of the file
			fwrite($handle, $val);
		}
	}

	// Closing file
	fclose($handle);

	// Injecting this new partition table
	passthru("/sbin/bsdlabel -R -B {$harddrive}{$partition} {$g['tmp_path']}/label.tmp");
}
?>
