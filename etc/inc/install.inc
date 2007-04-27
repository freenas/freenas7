<?php
/*
	system.inc
	part of FreeNAS (http://www.freenas.org)
	Copyright (C) 2005-2007 Olivier Cochard-Labbé <olivier@freenas.org>.
	All rights reserved.
	system_systime_set() function added by Paul Wheels (pwheels@users.sourceforge.net)

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
	/* Part of install process: Mount the CDROM */
	echo "\nCreating mount point for the CDROM...\n";	

	if (is_dir("/mnt/cdrom_fr_0507")) {
		echo "Temporary directory for CD already exists!\n";
		return 1;
	}

	/* Creating tempo directory for cdrom*/
	if (mwexec("/bin/mkdir /mnt/cdrom_fr_0507")) {
		echo "Can't create the temporary folder!\n";
		return 1;
	}

	/* Monting the CDROM */
	echo "Mount CDROM...\n";
	if (mwexec("/sbin/mount_cd9660 /dev/$cdrom /mnt/cdrom_fr_0507")) {
		echo "Can't mount the CDROM!\n";
		return 1;
	}

	return 0;
}

/* Unmount CDROM and destination disk.
   Called by install script. Return 0 if OK, 1 if not. */
function install_unmount_cd()
{
	$result = 0;

	/* Part of install process: Unmounting the disks: CDROM and destination drive */
	echo "Unmount CDROM...\n";

	/* Unmounting disk */
	if (mwexec("/sbin/umount /mnt/install_fr_0507")) {
		$result = 1;
	}
	if (mwexec("/sbin/umount /mnt/cdrom_fr_0507")) {
		$result = 1;
	}

	// Remove tempo directory
	@rmdir ("/mnt/cdrom_fr_0507");
	@rmdir ("/mnt/install_fr_0507");

	return $result;
}

/* Create to partition on the destination hard drive.
   Called by install script. Return 0 if OK, 1 if not. */
function install_init_halfdisk($harddrive)
{
	/* Part of install process: Initialize the destination disk with 2 partitions */
	/* Create the partitions */
	install_fdisk($harddrive);

	if (mwexec("(/bin/echo y; /bin/echo y) | /sbin/fdisk -B -b /boot/mbr $harddrive")) {
		echo "Can't fdisk the destination drive!\n";
		return 1;
	}

	/* Must wait that the /dev is upatded with the new information */
	echo "Waiting for system update...";
	$devtotest="/dev/$harddrive". "s2";
	$i=0;
	while (!file_exists($devtotest)) {
		sleep(1);
		echo ".";
		$i++;
		if ($i==20) {
			echo " System not updated after 20 seconds!\n";
			return 1;
		}
	}
	echo "\n";

	echo "Creating BSD label...\n";
	if (mwexec("/sbin/bsdlabel -B -w -b /boot/boot " . escapeshellarg($harddrive) ."s1 auto")) {
		return 1;
	}
	if (mwexec("/sbin/bsdlabel -w " . escapeshellarg($harddrive) ."s2 auto")) {
		return 1;
	}

	echo "Modify BSD label information...\n";
	install_bsdlabel($harddrive, "s1", "4.2BSD");
	install_bsdlabel($harddrive, "s2", "4.2BSD");

	echo "Creating filesystem...\n";
	if (mwexec("/sbin/newfs -U /dev/" . escapeshellarg($harddrive) . "s1")) {
		return 1;
	}
	if (mwexec("/sbin/newfs -U /dev/" . escapeshellarg($harddrive) . "s2")) {
		return 1;
	}

	return 0;
}

/* Install the IMG on the destination harddrive.
   Called by install script. Return 0 if OK, 1 if not. */
function install_dd_image($harddrive)
{
	/* Part of install process: dd image file on the destination disk */
	// It's perhaps this brut method 'dd' that create some problem with BIOS configured on LBA or not mode....
	echo "Installation...\n";

	if (mwexec("/usr/bin/gunzip -S \"\" -c /mnt/cdrom_fr_0507/".get_product_name()."-generic-pc.gz | dd of=/dev/".escapeshellarg($harddrive)." bs=512 > /dev/null 2>&1"))
		return 1;

	return 0;
}

/* Backup the configuration file on the hard drive/CF and unmount this drive
   Called by install script. Return 0 if OK, 1 if not. */
function install_backupconfig($disk)
{	
	/* Creating tempo directory for the config file*/
	if (mwexec("/bin/mkdir /tmp/configbak")) {
		echo "Can't create /tmp/configbak!\n";
		return 1;
	}
	
	if (mwexec("/bin/mkdir /tmp/installdrive")) {
		echo "Can't create /tmp/installdrive!\n";
		return 1;
	}
	/* mounting the drive to be upgraded */
	if (mwexec("/sbin/mount /dev/$disk /tmp/installdrive")) {
		echo "Can't mount the drive to be upgraded!\n";
		$result = 1;
	}
	
	/* Backuping the config file */
	if (mwexec("cp -p /tmp/installdrive/conf/* /tmp/configbak")) {
		echo "Can't backup the config file!\n";
		return 1;
	}
	
	/* unmounting the drive to be upgraded */
	if (mwexec("/sbin/umount -f /dev/$disk")) {
		echo "Can't unmount the drive to be upgraded during config backup process!\n";
		$result = 1;
	}
	
	return 0;
}

function install_restoreconfig($disk)
{
	/* Mount the upgraded drive in RW mode */
	if (mwexec("/sbin/mount /dev/$disk /tmp/installdrive")) {
		echo "Can't mount the drive just upgraded in RW mode!\n";
		$result = 1;
	}
	
	/* Restoring the config file*/
	if (mwexec("cp -p /tmp/configbak/* /tmp/installdrive/conf")) {
		echo "Can't restore the config file!\n";
		return 1;
	}
	
	/* unmount the upgraded drive */
	if (mwexec("/sbin/umount -f /dev/$disk")) {
		echo "Can't unmount the drive just upgraded!\n";
		$result = 1;
	}
	
	return 0;
}

// Initialise HARD DRIVE for installation (creating 2 partition).
// Return 0 if successful, 1 if error
function install_fdisk($harddrive)
{
	global $config, $g;

	// Get disk informations.
	$diskinfo = disks_get_diskinfo($harddrive);

	// Setting partition size to 32Mb.
	$part1_size_mbytes = 32;
	$part1_size_bytes = $part1_size_mbytes * 1024 * 1024;

	// Calculate partition sector sizes.
	$part1_size_sectors = $part1_size_bytes / $diskinfo['sectorsize'];
	$part2_size_sectors = ($diskinfo['mediasize_bytes'] / $diskinfo['sectorsize']) - $part1_size_sectors;

	// Create fdisk config file (fdisk.conf).
	$fd = fopen("{$g['varetc_path']}/fdisk.conf", "w");
	if (!$fd) {
		write_console("Error: Can't open fdisk.conf in install_fdisk().\n");
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
	passthru("/sbin/bsdlabel " . escapeshellarg($harddrive) ."$partition > {$g['tmp_path']}/label.tmp");

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
	passthru("/sbin/bsdlabel -R -B " . escapeshellarg($harddrive) ."$partition {$g['tmp_path']}/label.tmp");
}
?>
