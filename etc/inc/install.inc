<?php
/*
	install.inc
	Copyright (C) 2007-2008 Volker Theile (votdev@gmx.de)
	All rights reserved.

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

// Mount the CDROM.
// Return 0 if successful, otherwise 1
function install_mount_cd($cdrom)
{
	write_console("Mount CDROM.\n");

	$mntpath = "/mnt/cdrom";

	// Creating temporary directory to mount CDROM.
	if (1 == mwexec("/bin/mkdir -p {$mntpath}")) {
		write_console("Error: Failed to create directory '{$mntpath}'!\n");
		return 1;
	}

	// Mounting the CDROM.
	if (1 == mwexec("/sbin/mount_cd9660 /dev/{$cdrom} {$mntpath}")) {
		write_console("Error: Failed to mount device '{$cdrom}'!\n");
		@rmdir($mntpath);
		return 1;
	}

	return 0;
}

// Unmount CDROM.
// Return 0 if successful, otherwise 1
function install_unmount_cd()
{
	write_console("Unmount CDROM.\n");

	$result = 0;
	$mntpath = "/mnt/cdrom";

	// Unmount CDROM
	if (1 == mwexec("/sbin/umount {$mntpath}")) {
		$result = 1;
	}

	// Remove temporary mount directory
	@rmdir($mntpath);

	return $result;
}

// Create two partitions on the destination hard drive.
// $harddrive - Disk name (e.g. ad0)
// $size - Partition 1 size in MB
// Return 0 if successful, otherwise 1
function install_init2partdisk($harddrive, $size)
{
	write_console("Initialze disk '{$harddrive}':\n");

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

	/* Create volume label on file system. */
	if (mwexec("/sbin/tunefs -L Data /dev/{$harddrive}s2")) {
		write_console("\nError: Failed to modify volume label on '/dev/{$harddrive}s2'!\n");
		return 1;
	}

	return 0;
}

// Install the IMG on the destination harddrive.
// Return 0 if successful, otherwise 1
function install_dd_image($harddrive)
{
	global $g;

	$imgfilename=get_product_name(). "-{$g['arch']}-embedded.gz";

	// Test firmware file integrity.
	if (mwexec("/usr/bin/gzip -t /mnt/cdrom/{$imgfilename}")) {
		write_console("Error: Firmware file is corrupted.\n");
		return 1;
	}

	// dd image file on the destination disk.
	write_console("Installing system image on device {$harddrive}.\n");
	if (mwexec("/usr/bin/gzip -cd /mnt/cdrom/{$imgfilename} | /bin/dd of=/dev/{$harddrive} bs=16k > /dev/null 2>&1")) {
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

	$mntpath = "/mnt/inst_{$harddrive}";

	// Mount disk used to install OS.
	mwexec("/bin/mkdir -p {$mntpath}");
	if (1 == mwexec("/sbin/mount /dev/{$harddrive}s1a {$mntpath}")) {
		write_console("Error: Failed to mount '/dev/{$harddrive}s1a'!\n");
		return 1;
	}

	// Install system files.
	write_console("Installing system files on device {$harddrive}s1.\n");
	mwexec("/usr/bin/tar --exclude mnt/ --exclude dev/ --exclude var/ --exclude tmp/ --exclude {$g['cf_path']}/ -c -f - -C / . | tar -xvpf - -C {$mntpath}");
	mwexec("/bin/mkdir -p {$mntpath}/var");
	mwexec("/bin/mkdir -p {$mntpath}/dev");
	mwexec("/bin/mkdir -p {$mntpath}/mnt");
	mwexec("/bin/mkdir -p {$mntpath}/tmp");
	mwexec("/bin/mkdir -p {$mntpath}/boot/defaults");
	mwexec("/bin/cp -v /mnt/cdrom/boot/* {$mntpath}/boot");
	mwexec("/bin/cp -v /mnt/cdrom/boot/defaults/* {$mntpath}/boot/defaults");
	mwexec("/bin/cp -v /mnt/cdrom/boot/kernel/* {$mntpath}/boot/kernel");

	// Install configuration file
	mwexec("/bin/mkdir -p {$mntpath}{$g['cf_conf_path']}");
	mwexec("/bin/cp -v {$g['conf_default_path']}/config.xml {$mntpath}{$g['conf_path']}");

	// Generate new loader.conf file.
	$loaderconf .= <<<EOD
kernel="kernel"
bootfile="kernel"
kernel_options=""
kern.hz="100"

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

	// Decompress kernel.
	mwexec("/usr/bin/gzip -d -f {$mntpath}/boot/kernel/kernel.gz");

	// Generate /etc/fstab.
	$fstab .= <<<EOD
# Device                Mountpoint      FStype  Options         Dump    Pass#
/dev/{$harddrive}s1a             /       ufs     rw              1       1

EOD;

	if (false === file_put_contents("{$mntpath}/etc/fstab", $fstab)) {
		write_console("Error: Failed to create '/etc/fstab'.\n");
		return 1;
	}

	// Generating the /etc/cfdevice (this file is linked in /var/etc at bootup)
	// This file is used by the firmware and mount check and is normally
	// generated with 'liveCD' and 'embedded' during startup, but need to be
	// created during install of 'full'.
	$cfdevice .= <<<EOD
{$harddrive}s1a

EOD;

	if (false === file_put_contents("{$mntpath}/etc/cfdevice", $cfdevice)) {
		write_console("Error: Failed to create '/etc/cfdevice'.\n");
		return 1;
	}

	// Unmount disk containing OS
	mwexec("/sbin/umount {$mntpath}");
	mwexec("/bin/rm -f -r {$mntpath}");

	return 0;
}

// Upgrade a FreeNAS 'full' release on the destination harddrive.
// Return 0 if successful, otherwise 1
function install_upgrade_full($harddrive)
{
	global $g;

	$mntpath = "/mnt/inst_{$harddrive}";
	$sysbackup = "/tmp/inst_{$harddrive}_bak";

	// Mount disk used to install OS
	mwexec("/bin/mkdir -p {$mntpath}");
	if (1 == mwexec("/sbin/mount /dev/{$harddrive}s1a {$mntpath}")) {
		write_console("Error: Failed to mount '/dev/{$harddrive}s1a'!\n");
		return 1;
	}

	// Backup system files
	write_console("Backup system configuration files.\n");
	mwexec("/bin/mkdir -p {$sysbackup}");
	mwexec("/bin/cp -p {$mntpath}/boot/loader.conf {$sysbackup}");
	mwexec("/bin/cp -p {$mntpath}/etc/platform {$sysbackup}");
	mwexec("/bin/cp -p {$mntpath}/etc/fstab {$sysbackup}");
	mwexec("/bin/cp -p {$mntpath}/etc/cfdevice {$sysbackup}");

	// Install system files
	write_console("Installing system files on device {$harddrive}s1.\n");
	mwexec("/usr/bin/tar --exclude mnt/ --exclude dev/ --exclude var/ --exclude tmp/ --exclude {$g['cf_path']}/ -c -f - -C / . | tar -xvpf - -C {$mntpath}");
	mwexec("/bin/mkdir -p {$mntpath}/var");
	mwexec("/bin/mkdir -p {$mntpath}/dev");
	mwexec("/bin/mkdir -p {$mntpath}/mnt");
	mwexec("/bin/mkdir -p {$mntpath}/tmp");
	mwexec("/bin/mkdir -p {$mntpath}/boot/defaults");
	mwexec("/bin/cp -v /mnt/cdrom/boot/* {$mntpath}/boot");
	mwexec("/bin/cp -v /mnt/cdrom/boot/defaults/* {$mntpath}/boot/defaults");
	mwexec("/bin/cp -v /mnt/cdrom/boot/kernel/* {$mntpath}/boot/kernel");

	// Decompress kernel
	mwexec("/usr/bin/gzip -d -f {$mntpath}/boot/kernel/kernel.gz");

	// Restore system files
	write_console("Restore system configuration files.\n");
	mwexec("/bin/cp -p {$sysbackup}/loader.conf {$mntpath}/boot");
	mwexec("/bin/cp -p {$sysbackup}/platform {$mntpath}/etc");
	mwexec("/bin/cp -p {$sysbackup}/fstab {$mntpath}/etc");
	mwexec("/bin/cp -p {$sysbackup}/cfdevice {$mntpath}/etc");

	// Cleanup system backup files
	mwexec("/bin/rm -f -r {$sysbackup}");

	// Start upgrade script to remove obsolete files.
	write_console("Remove obsolete files.\n");
	mwexec("/etc/install/upgrade.sh clean {$mntpath}");

	// Unmount disk containing OS
	mwexec("/sbin/umount {$mntpath}");
	mwexec("/bin/rm -f -r {$mntpath}");

	return 0;
}

// Backup the configuration file on the hard drive/CF and unmount this drive.
// Return 0 if successful, otherwise 1
function install_backup_config($disk)
{
	global $g;

	$tmp_cf_bak = "/tmp/cf_bak";
	$mnt_cf_disk = "/mnt/inst_{$disk}";

	write_console("Backup configuration.\n");

	/* Creating temporary directory for the configuration file. */
	if (1 == mwexec("/bin/mkdir -p {$tmp_cf_bak}")) {
		write_console("Error: Failed to create '{$tmp_cf_bak}'!\n");
		return 1;
	}

	/* Creating temporary directory for the disk containing the configuration file. */
	if (1 == mwexec("/bin/mkdir -p {$mnt_cf_disk}")) {
		write_console("Error: Failed to create '{$mnt_cf_disk}'!\n");
		return 1;
	}

	/* Mount the drive to be upgraded (readonly mode). */
	if (1 == mwexec("/sbin/mount -r /dev/{$disk} {$mnt_cf_disk}")) {
		write_console("Error: Failed to mount device '{$disk}'!\n");
		return 1;
	}

	/* Backup the configuration file */
	if (1 == mwexec("/bin/cp -p {$mnt_cf_disk}/conf/config.xml {$tmp_cf_bak}")) {
		write_console("Error: Failed to backup the configuration file!\n");
		return 1;
	}

	/* Unmount the drive to be upgraded */
	if (1 == mwexec("/sbin/umount {$mnt_cf_disk}")) {
		write_console("Error: Failed to unmount '{$mnt_cf_disk}'!\n");
		return 1;
	}

	return 0;
}

function install_restore_config($disk)
{
	global $g;

	$tmp_cf_bak = "/tmp/cf_bak";
	$mnt_cf_disk = "/mnt/inst_{$disk}";

	write_console("Restore configuration.\n");

	/* Mount the drive to be upgraded */
	if (1 == mwexec("/sbin/mount /dev/{$disk} {$mnt_cf_disk}")) {
		write_console("Error: Failed to mount device '{$disk}'!\n");
		return 1;
	}

	/* Restore the configuration file */
	if (1 == mwexec("/bin/cp -p {$tmp_cf_bak}/config.xml {$mnt_cf_disk}/conf")) {
		write_console("Error: Failed to restore the configuration file!\n");
		return 1;
	}

	/* Unmount the upgraded drive */
	if (1 == mwexec("/sbin/umount {$mnt_cf_disk}")) {
		write_console("Error: Failed to unmount '{$mnt_cf_disk}'!\n");
		return 1;
	}

	/* Cleanup */
	if (1 == mwexec("/bin/rm -f -r {$mnt_cf_disk} {$tmp_cf_bak}")) {
		write_console("Error: Failed to remove temporary backup data!\n");
		return 1;
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

	// Get disk information
	$diskinfo = disks_get_diskinfo($harddrive);

	// Get valid CHS information. 'diskinfo' gets values from harddisk firmware that
	// are maybe unusable by 'fdisk' (e.g. sectors = 255, fdisk requires <= 63).
	disk_sanitize_bios_geom($diskinfo);

	// Check available disk space
	if ($diskinfo['mediasize_mbytes'] <= $size) {
		write_console("Error: Not enough space available on disk {$diskinfo['name']}.\n");
		return 1;
	}

	// Setting partition size to $size (in MB)
	$part1_size_mbytes = $size;
	$part1_size_bytes = $part1_size_mbytes * 1024 * 1024;

	// Calculate partition sector sizes
	$part1_size_sectors = $part1_size_bytes / $diskinfo['sectorsize'];
	$part2_size_sectors = $diskinfo['mediasize_sectors'] - $part1_size_sectors;

	// Create fdisk config file (fdisk.conf)
	$fdiskconf .= <<<EOD
g c{$diskinfo['cylinders']} h{$diskinfo['heads']} s{$diskinfo['sectors']}
p 1 165 1 $part1_size_sectors
p 2 165 $part1_size_sectors $part2_size_sectors
p 3 0 0 0
p 4 0 0 0
a 1

EOD;

	if (false === file_put_contents("{$g['tmp_path']}/fdisk.conf", $fdiskconf)) {
		write_console("Error: Failed to create '{$g['tmp_path']}/fdisk.conf'.\n");
		return 1;
	}

	// Completely wipe any prior information on the disk
	if (mwexec("/bin/dd if=/dev/zero of=/dev/{$harddrive} bs=512 count=32")) {
		write_console("Error: Failed to wipe any prior information on the disk '{$harddrive}'.\n");
		return 1;
	}

	// Set slice values
	if (mwexec("/sbin/fdisk -f {$g['tmp_path']}/fdisk.conf {$harddrive}")) {
		write_console("Error: Failed to set partitions on the disk '{$harddrive}'.\n");
		return 1;
	}

	write_console("Install boot code.\n");

	// Reinitialize the boot code contained in sector 0 of the disk
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

	// Completely wipe any prior information on the partition
	if (mwexec("/bin/dd if=/dev/zero of=/dev/{$harddrive} bs=512 count=32")) {
		write_console("Error: Failed to wipe any prior information on the partition '{$harddrive}'.\n");
		return 1;
	}

	// Do an automatic label first
	if (mwexec("/sbin/bsdlabel -w -B {$harddrive}")) {
		write_console("\nError: Failed to write standard label.\n");
		return 1;
	}

	// Read the label to manipulate it
	exec("/sbin/bsdlabel {$harddrive} > {$g['tmp_path']}/label.tmp", $output, $result);
	if ($result) {
		write_console("\nError: Failed to backup label.\n");
		return 1;
	}

	// Put the file conent into an array
	$label = file("{$g['tmp_path']}/label.tmp");

	// Open this file in add mode
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

	// Injecting modified label
	if (mwexec("/sbin/bsdlabel -R -B {$harddrive} {$g['tmp_path']}/label.tmp")) {
		write_console("\nError: Failed to write modified label.\n");
		return 1;
	}

	// Cleanup
	unlink("{$g['tmp_path']}/label.tmp");

	return 0;
}

// Detect device where the OS is installed.
// $mode [in] - Search for 'embedded' or 'full' installation.
// $devinfo [out] - Array (
//		[device] => ad0
//		[partition] => s1
//	)
// Return 0 if successful, otherwise 1
function install_detect_installation($mode, &$devinfo) {
	global $g;

	$result = 1;
	$devinfo = array();

	echo "Try to detect installation...\n";

	// Create temporary mountpoint
	$mnt_cf_disk = "/mnt/inst_cf_tmp";
	mwexec("/bin/mkdir -p {$mnt_cf_disk}");

	// Scan all disks detected from kernel
	$adisk = explode(" ", trim(preg_replace("/kern.disks: /", "", exec("/sbin/sysctl kern.disks"))));
	foreach ($adisk as $diskv) {
		// Skip mfs-mounted filesystems
		if (0 < ereg("md[0-9]*", $diskv))
			continue;

		// Search on slice 1 for OS installed on disk with install script
		if (0 == mwexec("/sbin/mount -r /dev/{$diskv}s1 {$mnt_cf_disk}")) {
			// Check if configuration file exists
			if (file_exists("{$mnt_cf_disk}/conf/config.xml")) {
				$devinfo['device'] = $diskv;
				$devinfo['partition'] = "s1";

				// Check install mode
				switch ($mode) {
					case 'embedded':
						if (file_exists("{$mnt_cf_disk}/mfsroot.gz")) {
							$result = 0;
						}
						break;

					case 'full':
						$platform = chop(@file_get_contents("{$mnt_cf_disk}{$g['etc_path']}/platform"));
						if (FALSE !== ereg("^.*-full", $platform)) {
							$result = 0;
						}
						break;
				}
			}

			// Unmount device
			mwexec("/sbin/umount {$mnt_cf_disk}");

			if (0 == $result)
				break;
		}

		// Search on partition 'a' for OS IMG image written on disk
		if (mwexec("/sbin/mount -r /dev/{$diskv}a {$mnt_cf_disk}") == 0) {
			// Check if configuration file exists
			if (file_exists("{$mnt_cf_disk}/conf/config.xml")) {
				$devinfo['device'] = $diskv;
				$devinfo['partition'] = "a";

				// Check install mode
				switch ($mode) {
					case 'embedded':
						if (file_exists("{$mnt_cf_disk}/mfsroot.gz")) {
							$result = 0;
						}
						break;

					case 'full':
						$platform = chop(@file_get_contents("{$mnt_cf_disk}{$g['etc_path']}/platform"));
						if (FALSE !== ereg("^.*-full", $platform)) {
							$result = 0;
						}
						break;
				}
			}

			// Unmount device
			mwexec("/sbin/umount {$mnt_cf_disk}");

			if (0 == $result)
				break;
		}
	}

	// Cleanup
	mwexec("/bin/rm -f -r {$mnt_cf_disk}");

	return $result;
}
?>
