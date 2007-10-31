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

// Mount the CDROM.
// Return 0 if successful, otherwise 1
function install_mount_cd($cdrom)
{
	write_console("Mount CDROM.\n");

	// Check if there already exists directory used to mount CDROM.
	if (is_dir("/mnt/cdrom")) {
		write_console("Error: Temporary directory '/mnt/cdrom' for CDROM already exists!\n");
		return 1;
	}

	/* Creating tempo directory for cdrom */
	if (mwexec("/bin/mkdir -p /mnt/cdrom")) {
		write_console("Error: Failed to create directory '/mnt/cdrom'!\n");
		return 1;
	}

	/* Monting the CDROM */
	if (mwexec("/sbin/mount_cd9660 /dev/{$cdrom} /mnt/cdrom")) {
		write_console("Error: Failed to mount device '{$cdrom}'!\n");
		@rmdir ("/mnt/cdrom");
		return 1;
	}

	return 0;
}

// Unmount CDROM.
// Return 0 if successful, otherwise 1
function install_unmount_cd()
{
	$result = 0;

	write_console("Unmount CDROM.\n");

	/* Unmounting cdrom */
	if (mwexec("/sbin/umount /mnt/cdrom")) {
		$result = 1;
	}

	// Remove tempo directory
	@rmdir ("/mnt/cdrom");

	return $result;
}

// Create two partitions on the destination hard drive.
// $harddrive - Disk name (e.g. ad0)
// $size - Partition 1 size in MB
// Return 0 if successful, otherwise 1
function install_init2partdisk($harddrive, $size)
{
	write_console("Initialze disk '{$harddrive}:'.\n");

	/* Initialize the destination disk with 2 partitions. */
	if (1 == install_fdisk2partdisk($harddrive, $size)) {
		write_console("Error: Failed to create partitions!\n");
		return 1;
	}

	/* Must wait until /dev is updated with the new disk partition information. */
	$devtotest="/dev/{$harddrive}s2";
	$i=0;
	while (!file_exists($devtotest)) {
		sleep(1);
		write_console(".");
		$i++;
		if (20 == $i) {
			write_console("\nError: System not updated! Failed to find partition '{$devtotest}'.\n");
			return 1;
		}
	}

	/* Create disk label. */
	write_console("Create BSD label on disk partition:");
	write_console(" {$harddrive}s1");
	if (install_bsdlabel("{$harddrive}s1")) {
		write_console("Error: Failed to label '{$harddrive}s1'!\n");
		return 1;
	}
	write_console(" {$harddrive}s2");
	if (install_bsdlabel("{$harddrive}s2")) {
		write_console("Error: Failed to label '{$harddrive}s2'!\n");
		return 1;
	}
	write_console("\n");

	/* Create filesystems. */
	write_console("Create filesystem:");
	write_console(" {$harddrive}s1");
	if (mwexec("/sbin/newfs -U /dev/{$harddrive}s1")) {
		write_console("\nError: Failed to create filesystem on '/dev/{$harddrive}s1'!\n");
		return 1;
	}
	write_console(" {$harddrive}s2");
	if (mwexec("/sbin/newfs -U /dev/{$harddrive}s2")) {
		write_console("\nError: Failed to create filesystem on '/dev/{$harddrive}s2'!\n");
		return 1;
	}
	write_console("\n");

	return 0;
}

// Install the IMG on the destination harddrive.
// Return 0 if successful, otherwise 1
function install_dd_image($harddrive)
{
	global $g;

	/* Part of install process: dd image file on the destination disk */
	// It's perhaps this brut method 'dd' that create some problem with BIOS configured on LBA or not mode....
	write_console("Installing system image on disk {$harddrive}.\n");
	$imgfilename=get_product_name(). "-{$g['arch']}-embedded.gz";
	if (mwexec("/usr/bin/gzip -cd /mnt/cdrom/{$imgfilename} | /bin/dd of=/dev/{$harddrive} > /dev/null 2>&1")) {
		write_console("Error: Failed to dd image on '/dev/{$harddrive}'!\n");
		return 1;
	}

	return 0;
}

// Install FreeNAS on the destination harddrive.
// Return 0 if successful, otherwise 1
function install_harddrive_image($harddrive)
{
	global $g;

	$mntpath = "/mnt/{$harddrive}_install";

	// Mount disk used to install OS.
	mwexec("/bin/mkdir -p {$mntpath}");
	mwexec("/sbin/mount /dev/{$harddrive}s1a {$mntpath}");

	write_console("Installing system files on disk {$harddrive}s1.\n");
	mwexec("/usr/bin/tar --exclude {$mntpath} --exclude /mnt --exclude /dev --exclude /var -cf - -C / ./ | tar -xvpf - -C {$mntpath}");
	mwexec("/bin/mkdir {$mntpath}/var");
	mwexec("/bin/mkdir {$mntpath}/dev");
	mwexec("/bin/mkdir {$mntpath}/mnt");
	mwexec("/bin/mkdir {$mntpath}/boot/defaults");
	mwexec("/bin/cp -v /mnt/cdrom/boot/* {$mntpath}/boot");
	mwexec("/bin/cp -v /mnt/cdrom/boot/defaults/* {$mntpath}/boot/defaults");
	mwexec("/bin/cp -v /mnt/cdrom/boot/kernel/* {$mntpath}/boot/kernel");

	// Generate new loader.conf file
	$loaderconf .= <<<EOD
kernel="kernel"
bootfile="kernel"
kernel_options=""
kern.hz="100"
splash_bmp_load="YES"
bitmap_load="YES"
bitmap_name="/boot/splash.bmp"

EOD;

	if (false === file_put_contents("{$mntpath}/boot/loader.conf", $loaderconf)) {
		write_console("Error: Failed to create '/boot/loader.conf'.\n");
		return 1;
	}

	// Set the release type.
	$platform .= <<<EOD
{$g['arch']}-full

EOD;

	if (false === file_put_contents("{$mntpath}/etc/platform", $platform)) {
		write_console("Error: Failed to create '/etc/platform'.\n");
		return 1;
	}

	//Unzipping the Kernel
	mwexec("/usr/bin/gzip -d {$mntpath}/boot/kernel/kernel.gz");

	//Generating the FSTAB
	$fstab .= <<<EOD
# Device                Mountpoint      FStype  Options         Dump    Pass#
/dev/{$harddrive}s1a             /       ufs     rw              1       1

EOD;

	if (false === file_put_contents("{$mntpath}/etc/fstab", $fstab)) {
		write_console("Error: Failed to create '/etc/fstab'.\n");
		return 1;
	}

	// Generating the /etc/cfdevice (this file is linked in /var/etc at bootup)
	// This file (more exatly /var/etc/cfdevice) is used by the firmware and mount check
	// This file is normally generated with 'liveCD' and 'embedded' during startup, but need to be created during install of 'full'
	$cfdevice .= <<<EOD
{$harddrive}s1a

EOD;

	if (false === file_put_contents("{$mntpath}/etc/cfdevice", $cfdevice)) {
		write_console("Error: Failed to create '/etc/cfdevice'.\n");
		return 1;
	}

	// Umount destination disk.
	mwexec("/sbin/umount {$mntpath}");
	mwexec("/bin/rm -f {$mntpath}");

	return 0;
}

// Upgrade a FreeNAS 'full' releaseon the destination harddrive.
// Return 0 if successful, otherwise 1
function install_upgrade_full($harddrive)
{
	global $g;

	write_console("Upgrade installed OS.\n");

	$mntpath = "/mnt/{$harddrive}_install";

	// Mount disk used by OS.
	mwexec("/bin/mkdir -p {$mntpath}");
	mwexec("/sbin/mount /dev/{$harddrive}s1a {$mntpath}");

	// Start pre-upgrade script.
	mwexec("/etc/preupgrade.sh");

	// Upgrade OS.
	mwexec("/usr/bin/tar --exclude {$mntpath} --exclude /mnt --exclude /dev --exclude /var -cf - -C / ./ | tar -xvpf - -C {$mntpath}");
	mwexec("/bin/cp -v /mnt/cdrom/boot/* {$mntpath}/boot");
	mwexec("/bin/cp -v /mnt/cdrom/boot/defaults/* {$mntpath}/boot/defaults");
	mwexec("/bin/cp -v /mnt/cdrom/boot/kernel/* {$mntpath}/boot/kernel");

	// Start post-upgrade script.
	mwexec("/etc/postupgrade.sh");

	// Umount destination disk.
	mwexec("/sbin/umount {$mntpath}");
	mwexec("/bin/rm -f {$mntpath}");

	return 0;
}

// Backup the configuration file on the hard drive/CF and unmount this drive.
// Return 0 if successful, otherwise 1
function install_backupconfig($disk)
{
	write_console("Backup configuration.\n");

	/* Creating temporary directory for the configuration file. */
	if (mwexec("/bin/mkdir -p /tmp/confbackup")) {
		write_console("Error: Failed to create '/tmp/confbackup'!\n");
		return 1;
	}

	/* Creating temporary directory for the disk containing the configuration file. */
	if (mwexec("/bin/mkdir -p /mnt/{$disk}_backup")) {
		write_console("Error: Failed to create '/mnt/{$disk}_backup'!\n");
		return 1;
	}

	/* Mount the drive to be upgraded (readonly mode). */
	if (mwexec("/sbin/mount -r /dev/{$disk} /mnt/{$disk}_backup")) {
		write_console("Error: Failed to mount device '{$disk}'!\n");
		$result = 1;
	}

	/* Backup the configuration file. */
	if (mwexec("cp -p /mnt/{$disk}_backup/conf/config.xml /tmp/confbackup")) {
		write_console("Error: Failed to backup the configuration file!\n");
		return 1;
	}

	/* Unmount the drive to be upgraded. */
	if (mwexec("/sbin/umount -f /mnt/{$disk}_backup")) {
		write_console("Error: Failed to umount '/mnt/{$disk}_backup'!\n");
		$result = 1;
	}

	return 0;
}

function install_restoreconfig($disk)
{
	write_console("Restore configuration.\n");

	/* Mount the drive to be upgraded. */
	if (mwexec("/sbin/mount /dev/{$disk} /mnt/{$disk}_backup")) {
		write_console("Error: Failed to mount device '{$disk}'!\n");
		$result = 1;
	}

	/* Restore the configuration file. */
	if (mwexec("cp -p /tmp/confbackup/config.xml /mnt/{$disk}_backup/conf")) {
		write_console("Error: Failed to restore the configuration file!\n");
		return 1;
	}

	/* Unmount the upgraded drive. */
	if (mwexec("/sbin/umount -f /mnt/{$disk}_backup")) {
		write_console("Error: Failed to umount '/mnt/{$disk}_backup'!\n");
		$result = 1;
	}

	/* Cleanup */
	if (mwexec("/bin/rm -f -r /mnt/{$disk}_backup /tmp/confbackup")) {
		write_console("Error: Failed to remove temporary backup data!\n");
		$result = 1;
	}

	return 0;
}

// Initialize harddrive. Use given size for partition 1, use the rest for partition 2.
// $harddrive - Disk name (e.g. ad0)
// $size - Partition 1 size in MB
// Return 0 if successful, otherwise 1
function install_fdisk2partdisk($harddrive, $size)
{
	global $g;

	write_console("Create slice table.\n");

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
	$fdiskconf .= <<<EOD
g c{$diskinfo['cylinders']} h{$diskinfo['heads']} s{$diskinfo['sectors']}
p 1 165 1 $part1_size_sectors
p 2 165 $part1_size_sectors $part2_size_sectors
p 3 0 0 0
p 4 0 0 0
a 1

EOD;

	if (false === file_put_contents("{$g['tmp_path']}/fdisk.conf", $fdiskconf)) {
		write_console("Error: Failed to create '{$g['varetc_path']}/fdisk.conf'.\n");
		return 1;
	}

	// Set slice values.
	if (mwexec("/sbin/fdisk -f {$g['tmp_path']}/fdisk.conf {$harddrive}")) {
		write_console("Error: Failed to set slice values on disk '{$harddrive}'.\n");
		return 1;
	}

	write_console("Install boot code.\n");

	// Reinitialize the boot code contained in sector 0 of the disk.	
	if (mwexec("(/bin/echo y; /bin/echo y) | /sbin/fdisk -B -b /boot/mbr {$harddrive}")) {
		write_console("Error: Failed to reinitialize the boot code on disk '{$harddrive}'!\n");
		return 1;
	}

	return 0;
}

// Create disk label.
// Return 0 if successful, otherwise 1
function install_bsdlabel($harddrive)
{
	global $g;

	// Do a automatic label first.
	if (mwexec("/sbin/bsdlabel -w -B {$harddrive}")) {
		write_console("\nError: Failed to write standard label.\n");
		return 1;
	}

	// Read the label to manipulate it.
	exec("/sbin/bsdlabel {$harddrive} > {$g['tmp_path']}/label.tmp", $output, $result);
	if ($result) {
		write_console("\nError: Failed to backup label.\n");
		return 1;
	}

	// Put the file conent into an array.
	$label = file("{$g['tmp_path']}/label.tmp");

	// Open this file in add mode.
	$fd = fopen("{$g['tmp_path']}/label.tmp", 'a');

	while(list(,$val) = each($label)) {
		if (ereg ("unused",$val)) {
			$val = ereg_replace("c:", "a:", $val);
			// Peplacing unused by $type
			$val = ereg_replace("unused", "4.2BSD", $val);
			// Adding this line add the end of the file
			fwrite($fd, $val);
		}
	}

	// Closing file
	fclose($fd);

	// Injecting modified label.
	if (mwexec("/sbin/bsdlabel -R -B {$harddrive} {$g['tmp_path']}/label.tmp")) {
		write_console("\nError: Failed to write modified label.\n");
		return 1;
	}

	// Cleanup.
	unlink("{$g['tmp_path']}/label.tmp");

	return 0;
}
?>
