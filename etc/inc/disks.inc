<?php
/*
	disks.inc
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
	
/* include all configuration functions */
require_once("functions.inc");

/* Mount all configured disks */
function disks_mount_all()
{
	global $config, $g;
	
	if ($g['booting'])
		echo "Mounting Partitions... ";
	
	/* For each device configured:*/
	
	if (is_array($config['mounts']['mount'])) 	{
		foreach ($config['mounts']['mount'] as $mount) 		{
			/* Advanced Umount filesystem if not booting mode (mount edition)*/
			if (!$g['booting'])
				disks_umount_adv($mount);
								
			/* mount filesystem */
			disks_mount($mount);	
		
		}
	}
			
	if ($g['booting'])
		echo "done\n";
	
	return 0;
	
}

/* Mount all ISO files */
function disks_mount_iso_all()
{
	global $config, $g;
	
	if ($g['booting'])
		echo "Mounting ISO files... ";
	
	/* For each device configured:*/
	
	if (is_array($config['mounts']['iso'])) 	{
		foreach ($config['mounts']['iso'] as $mount_iso) 		{
			/* Advanced Umount filesystem if not booting mode (mount edition)*/
			if (!$g['booting'])
				disks_umount_iso_adv($mount);
								
			/* mount filesystem */
			disks_mount_iso($mount);	
		
		}
	}
			
	if ($g['booting'])
		echo "done\n";
	
	return 0;
	
}

/* Mount an ISO file
Return 0 if sucessful, 1 if error */

function disks_mount_iso($mount_iso)
{
	global $config, $g;
	
	/* Create one directory for each device under mnt */
	$mountname = escapeshellcmd($mount_iso['sharename']);
	@mkdir ("/mnt/{$mountname}",0777);
	
	/* Mount the ISO file. */
	$filename = escapeshellcmd($mount_iso['fullname']);
	$md_id = escapeshellcmd($mount_iso['md_id']);
	
	if (mwexec(" /sbin/mdconfig -a -t vnode -f {$filename} -u {$md_id}") == 0) {
		if (mwexec(" /sbin/mount -t cd9660 /dev/md{$md_id} /mnt/$mountname") == 0) {
	
				/* Change this directory into 777 mode */
				mwexec("/bin/chmod 777 /mnt/$mountname");
				$cmd="/usr/bin/logger \"ISO file  {$devname} mounted on /mnt/{$mountname}\"";
				mwexec($cmd);
				$result = 0;
		} else {
			/* If  error for mounting iso file*/
			$cmd="/usr/bin/logger \"Error for mounting ISO file  {$filename}\"";
		}
				
	} else {
		/* If  error for md device*/
		$cmd="/usr/bin/logger \"Error for reading file {$filename}\"";
	}
}

/* Mount using the configured mount given in parameter
Return 0 if sucessful, 1 if error */
function disks_mount($mount)
{
	global $config, $g;
	
	/* Create one directory for each device under mnt */
	$mountname = escapeshellcmd($mount['sharename']);
	@mkdir ("/mnt/{$mountname}",0777);

	/* Mount the filesystem. */
	$devname = escapeshellcmd($mount['fullname']);
	
	/* In booting mode, skip encrypted disk (need to enter the passphrase on the webgui) */
	if ($g['booting']) {
		if (is_array($config['geli']['vdisk'])) {
			$a_geli = $config['geli']['vdisk'];
			if (array_search_ex($mount['mdisk'], $a_geli, "fullname")) {
				echo "\nSkip mounting {$mount['sharename']}: Passphrase need.";
				$cmd="/usr/bin/logger \"Passphrase needed for $mountname\"";
				mwexec($cmd);
				return 0;
			}
		}
	}

	/* check the fileystem only if there is a problem*/
	/* This part is too stupid: I must read the FreBSD start script for use the same intelligent method for checking hard drive */
	switch ($mount['fstype']) 	{
		case "ufs":
			if (mwexec("/sbin/mount -t ufs  -o acls {$devname} /mnt/{$mountname}") == 0) {
				/* Change this directory into 777 mode */
				mwexec("/bin/chmod 777 /mnt/$mountname");
				$cmd="/usr/bin/logger \"Disk {$devname} mounted using UFS on /mnt/{$mountname}\"";
				mwexec($cmd);
				$result = 0;
			} else {
				/* If it's NOK, Check  filesystem and do a fsck, answer Yes to all question*/
				$cmd="/usr/bin/logger \"Error detected for mounting disk {$devname}, starting a UFS fsck\"";
				mwexec($cmd);
				mwexec("/sbin/fsck -y -t ufs {$devname}");
		
				/* Re-try to mount the partition */
				if (mwexec("/sbin/mount -t ufs -o acls {$devname} /mnt/{$mountname}") == 0) {
				/* Change this directory into 777 mode */
					mwexec("/bin/chmod 777 /mnt/{$mountname}");
					$cmd="/usr/bin/logger \"Disk {$devname} mounted using UFS on /mnt/{$mountname}\"";
					mwexec($cmd);
					$result = 0;
				} else {
					/* Not OK, remove the directory, prevent writing on RAM disk*/
					@rmdir ("/mnt/{$mountname}");
					$cmd="/usr/bin/logger \"ERROR: Can not mount {$devname} using UFS\"";
					mwexec($cmd);
					$result = 1;
				}
			}
			break;
		   
		case "msdosfs":
			if (mwexec("/sbin/mount_msdosfs -u ftp -g ftp -m 777 {$devname} /mnt/{$mountname}") == 0) {
				$cmd="/usr/bin/logger \"Disk {$devname} mounted using FAT on /mnt/{$mountname}\"";
				mwexec($cmd);
				$result = 0;
			} else {
				exec("/sbin/fsck -y -t msdosfs {$devname}");
				$cmd="/usr/bin/logger \"Error detected for mounting disk {$devname}, starting a FAT fsck\"";
				mwexec($cmd);
				if (mwexec("/sbin/mount_msdosfs -u ftp -g ftp -m 777 {$devname} /mnt/{$mountname}") == 0) {
					$cmd="/usr/bin/logger \"Disk {$devname} mounted using FAT on /mnt/{$mountname}\"";
					mwexec($cmd);
					$result = 0;
				} else {
					/* Not OK, remove the directory, prevent writing on RAM disk*/
					@rmdir ("/mnt/{$mountname}");
					$cmd="/usr/bin/logger \"ERROR: Can not mount {$devname} using FAT\"";
					mwexec($cmd);
					$result = 1;
				}
			}
			break;

		case "cd9660":
			if (mwexec("/sbin/mount_cd9660 {$devname} /mnt/{$mountname}") == 0) {
				$cmd="/usr/bin/logger \"Disk {$devname} mounted as CD/DVD on /mnt/{$mountname}\"";
				mwexec($cmd);
				$result = 0;
			} else {
				/* Not OK, remove the directory, prevent writing on RAM disk*/
				@rmdir ("/mnt/$mountname");
				$cmd="/usr/bin/logger \"ERROR: Can not mount {$devname} as CD/DVD\"";
				mwexec($cmd);
				$result = 1;
			}
			break;
		   
		case "ntfs":
			// Load the kernel fuse module
			mwexec("/sbin/kldload /boot/kernel/fuse.ko");
			if (mwexec("/usr/local/bin/ntfs-3g {$devname} /mnt/{$mountname} -o force,show_sys_files,allow_other") == 0) {
				$cmd="/usr/bin/logger \"Disk {$devname} mounted using NTFS on /mnt/{$mountname}\"";
				mwexec($cmd);
				$result = 0;
			} else {	
				/* COMMENT FISR PART BEGIN: NTFSFIX DO'NT WORK
				$cmd="/usr/bin/logger \"Error detected for mounting disk $devname, starting a ntfsfix\"";
				mwexec($cmd);
				exec("/usr/local/bin/ntfsfix {$devname}");
				if (mwexec("/usr/local/bin/ntfs-3g {$devname} /mnt/{$mountname} -o force,show_sys_files,allow_other") == 0) {
					$cmd="/usr/bin/logger \"Disk {$devname} mounted using NTFS on /mnt/{$mountname}\"";
					mwexec($cmd);
					$result = 0;
				}
				else	{ 
				COMMENT FIRST PART END */
					/* Not OK, remove the directory, prevent writing on RAM disk*/
					@rmdir ("/mnt/{$mountname}");
					$cmd="/usr/bin/logger \"ERROR for mounting {$devname} using NTFS: Try to fix error on this partition with MS Windows\"";
					mwexec($cmd);
					$result = 1;
				/* COMMENT SECOND PART BEGIN
				}
				COMMENT SECOND PART END */
			}
			break;
		   
		case "ext2fs":
			if (mwexec("/sbin/mount_ext2fs {$devname} /mnt/{$mountname}") == 0)	{
				/* Change this directory into 777 mode */
				mwexec("/bin/chmod 777 /mnt/{$mountname}");
				$cmd="/usr/bin/logger \"Disk {$devname} mounted using EXT2 on /mnt/{$mountname}\"";
				mwexec($cmd);
				$result = 0;
			} else {
				$cmd="/usr/bin/logger \"Error detected for mounting disk {$devname}, starting a e2fsck\"";
				mwexec($cmd);
				exec("/usr/local/sbin/e2fsck -f -p {$devname}");
				if (mwexec("/sbin/mount_ext2fs {$devname} /mnt/{$mountname}") == 0) {
					/* Change this directory into 777 mode */
					mwexec("/bin/chmod 777 /mnt/{$mountname}");
					$cmd="/usr/bin/logger \"Disk {$devname} mounted using EXT2 on /mnt/{$mountname}\"";
					mwexec($cmd);
					$result = 0;
				} else {
					/* Not OK, remove the directory, prevent writing on RAM */
					@rmdir ("/mnt/{$mountname}");
					$cmd="/usr/bin/logger \"ERROR: Can not mount {$devname} using EXT2\"";
					mwexec($cmd);
					$result= 1;
				}
			}
			break;
	}

	return $result;
}

/* Mount using fullname (/dev/ad0s1) given in parameter
Can't be used for NTFS volume (use /dev/fuseX)
*/
function disks_mount_fullname($fullname)
{
	global $config;

	
	if (is_array($config['mounts']['mount'])) {
		// Search the mount list for given disk and partition
		foreach($config['mounts']['mount'] as $mountk => $mountv) {
			if($mountv['fullname'] == $fullname) {
				$mount = $mountv;
			}
		}
	}

  	if($mount) {
    		$result= disks_mount($mount);
	}
  	else {
    		$result=0;
	}
	return $result;
}

/* Mount using sharename given in parameter
Can be used for NTFS volume (use /dev/fuseX)
*/
function disks_mount_sharename($sharename)
{
	global $config;

	
	if (is_array($config['mounts']['mount'])) {
		// Search the mount list for given disk and partition
		foreach($config['mounts']['mount'] as $mountk => $mountv) {
			if($mountv['sharename'] == $sharename) {
				$mount = $mountv;
			}
		}
	}

  	if($mount) {
    		$result= disks_mount($mount);
	}
  	else {
    		$result=0;
	}
	return $result;
}

/* Umount the specified configured mount point
Can't be used for NTFS volume (use /dev/fuseX)
Return 0 is successfull, 1 if error */
function disks_umount($mount)
{
	/* The $mount variable is the all config table for the mount point */
	$mountname = "/mnt/{$mount['sharename']}";

	if (mwexec("/sbin/umount $mountname") == 0) {
		if(@rmdir("$mountname")) {
			$result = 0;
			$cmd="/usr/bin/logger \"Sucessfull umounted /mnt/{$mountname}\"";
			mwexec($cmd);
		}
		else {
			$cmd="/usr/bin/logger \"ERROR for umount /mnt/{$mountname}\"";
			mwexec($cmd);
			$result = 1;
		}
	}
	else
		$result = 1;
	return $result;
}

/* Advanced umount using fullname (/dev/ad01s1)
Return 0 if successfull, 1 if error */
function disks_umount_fullname($fullname)
{
	global $config;

	/* Search the mount list for given fullname */
	foreach($config['mounts']['mount'] as $mountk => $mountv) {
  
		if (strcmp($mountv['fullname'],$fullname) == 0) {
			$mount = $mountv;
		}
	}

	if($mount) {
		$result = disks_umount($mount);
	}
	else {
		$result= 1;
	}
	
	return $result;
}

/* Advanced umount using sharename
Return 0 if successfull, 1 if error */
function disks_umount_sharename($sharename)
{
	global $config;

	/* Search the mount list for given fullname */
	foreach($config['mounts']['mount'] as $mountk => $mountv) {
  
		if (strcmp($mountv['sharename'],$sharename) == 0) {
			$mount = $mountv;
		}
	}

	if($mount) {
		$result = disks_umount($mount);
	}
	else {
		$result= 1;
	}
	
	return $result;
}

/* Advanced unmount the specified mount point without using the sharename value
Used when changing the 'sharename': Need to umount the old unknow sharename
Return 0 if successful, 1 if error */
function disks_umount_adv($mount)
{
	
	$fullname="{$mount['fullname']}";
		
	// get the mount list
	$detmount = get_mounts_list();
	
	//Look for the mount point in all mounted point
	
	foreach ($detmount as $detmountk => $detmountv) {
		// If we found the mount point on the device
		if (strcmp($detmountv['fullname'],$fullname) == 0) {
			$mountname="{$detmountv['mp']}";
		}
	}
	
	if ($mountname) {
		exec("/sbin/umount $mountname");
		@rmdir ("$mountname");
		return 0;
	}
	else
		$result = 1;
	return $result;
}

/* Advanced unmount the specified ISO mount point without using the sharename value
Used when changing the 'sharename': Need to umount the old unknow sharename
Return 0 if successful, 1 if error */
function disks_umount_iso_adv($mount)
{
	
	$md_id="md" . "{$mount['md_id']}";
		
	// get the mount list
	$detmount = get_mounts_list();
	
	//Look for the mount point in all mounted point corresponding to the md id
	
	foreach ($detmount as $detmountk => $detmountv) {
		// If we found the mount point on the device
		if (strcmp($detmountv['fullname'],$md_id) == 0) {
			$mountname="{$detmountv['mp']}";
		}
	}
	
	if ($mountname) {
		exec("/sbin/umount $mountname");
		@rmdir ("$mountname");
		return 0;
	}
	else
		$result = 1;
	return $result;
}

// This option check if this fullname (/dev/ad0s1) is mounted
// Return 0 if not, 1 if yes
function disks_check_mount_fullname($fullname)
{
	$detmount = get_mounts_list();
	$status=0;
	foreach ($detmount as $detmountk => $detmountv) {
		if (strcmp($detmountv['fullname'],$fullname) == 0) {
			$status=1;
			break;
		}
	}
	return $status;
}
// This option check if the sharename (/mnt/tutu) is mounted
// Return 0 if not, 1 if yes
function disks_check_mount_sharename($sharename)
{
	$detmount = get_mounts_list();
	$status=0;
	foreach ($detmount as $detmountk => $detmountv) {
		if (strcmp($detmountv['sharename'],$sharename) == 0) {
			$status=1;
			break;
		}
	}
	return $status;
}

/* This option check if the configured mount is mounted */

function disks_check_mount($mount)
{
	// MUST CHECK IF IT'S NTFS filesystem, and use a disks_check_mount_sharename function!!
	if (strcmp($mount['fstype'],"ntfs")==0) {
		return disks_check_mount_sharename($mount['sharename']);
	}
	else {
		return disks_check_mount_fullname($mount['fullname']);
	}
}

/* This function check if the disk is mounted
Return 0 if not, 1 if yes */
function disks_check_mount_disk($disk)
{
  
  	$detmount = get_mounts_list();
	$status=0;
	
	foreach ($detmount as $detmountk => $detmountv) {
		/* Must found the $disk (ad0) in result $mdisk (ad0s1) */
		// strpos will return 0 (found at position 0) if found, must check the 'false' value
		if (strpos($detmountv['mdisk'], $disk) !== false ) {
			$status=1;
			break;
		}
	}
	return $status;
}

/* This option check if the configured disk is online (detected by the system)
Result: "MISSING", disk don't detected by OS
Result: "ONLINE", disk is online
Result: "CHANGED", disk have changed (be replaced ?) */
function disks_status($diskname)
{
	global $config, $g;
	
	//$detectedlist = get_physical_disks_list();
	/* get All disk list (with CDROM) */
	
	$detectedlist = array_merge((array)get_physical_disks_list(),(array)get_cdrom_list());
	$status="MISSING";
	foreach ($detectedlist as $detecteddisk => $detecteddiskv) 	{
		
		if ($detecteddisk == $diskname['name']) {
			$status="ONLINE";
			// Removed the information CHANGED: Not usefull, and depend of the dmesg log (if lot's of log, the state of disk will be 'changed'
			/*
			if (($detecteddiskv['size'] != $diskname['size']) || ($detecteddiskv['desc'] != $diskname['desc'])) {
				$status="CHANGED";
			}
			*/
			break;
		}
	}
	return $status;
}

/* This function umount all configured mount */
function disks_umount_all()
{
	// Unmount all mount point
	global $config, $g;
	
	/* Sync disks*/
	mwexec("/bin/sync");
	
	if (is_array($config['mounts']['iso'])) 	{
		foreach ($config['mounts']['iso'] as $mount_iso) 	{
		disks_umount($mount_iso);
		}
	}
					
	if (is_array($config['mounts']['mount'])) 	{
		foreach ($config['mounts']['mount'] as $mountent) 	{
		disks_umount($mountent);
		}
	}

	return 0;
}

/* Configure, create and start gvinum volume */
function disks_raid_gvinum_configure()
{
		
	global $config, $g;
	
	/* Generate the raid.conf file */

	if ($config['gvinum']['vdisk']) 	{
		
		foreach ($config['gvinum']['vdisk'] as $a_raid_conf) 		{

			if (file_exists($g['varrun_path'] . "/raid.conf.dirty") && 
	                    !in_array("{$a_raid_conf['name']}\n",file($g['varrun_path'] . "/raid.conf.dirty"))) continue;
		
			/* generate raid.conf */
			$fd = fopen("{$g['varetc_path']}/raid-{$a_raid_conf['name']}.conf", "w");
			if (!$fd) 			{
				printf("Error: cannot open raid.conf in services_raid_configure().\n");
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
			exec("/sbin/gvinum lv $raidname",$rawdata);
			if (strpos($rawdata[0],"State: up")>=0) continue;

			mwexec("/sbin/gvinum start {$a_raid_conf['name']}");
		}
		
	}

	return 0;
}

/* Configure, create and start gmirror volume */
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

/* Configure, create and start gconcat volume */
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

/* Configure, create and start gstripe volume */
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

/* Configure, create and start graid5 volume */

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

/* Start all geom RAID volumes */
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

/* Start geom vinum volumes */
function disks_raid_gvinum_start()
{
	global $config, $g;

	if ($config['gvinum']['vdisk']) {
		if ($g['booting'])
			echo "Start gvinum raid... ";

		/* start each volume */
		foreach ($config['gvinum']['vdisk'] as $a_raid_conf) 	{
			mwexec("/sbin/gvinum start {$a_raid_conf['name']}");
		}
		
		if ($g['booting'])
			echo "done\n";
	}
	
	return 0;
}

/* Start geom mirror volumes */
function disks_raid_gmirror_start()
{
	global $config, $g;

	/* Start Geom mirror */
	if ($config['gmirror']['vdisk']) {
		if ($g['booting'])
			echo "Start gmirror raid... ";

		// Load geom mirror module
		mwexec("/sbin/gmirror load");
		
		if ($g['booting'])
			echo "done\n";
	}

	return 0;
}

/* Start geom concat volumes */
function disks_raid_gconcat_start()
{
	global $config, $g;

	/* Start Geom concat */
	if ($config['gconcat']['vdisk']) {
		if ($g['booting'])
			echo "Start gconcat raid... ";

		// Load geom concat module
		mwexec("/sbin/gconcat load");
		
		if ($g['booting'])
			echo "done\n";
	}

	return 0;
}

/* Start geom stripe volumes */
function disks_raid_gstripe_start()
{
	global $config, $g;
	
	/* Start Geom stripe */
	if ($config['gstripe']['vdisk']) {
		if ($g['booting'])
			echo "Start gstripe raid... ";

		// Load geom stripe module
		mwexec("/sbin/gstripe load");
		
		if ($g['booting'])
			echo "done\n";
	}

	return 0;
}

/* Start geom raid5 volumes */
function disks_raid_graid5_start()
{
	global $config, $g;

	/* Start Geom RAID5 */
	if ($config['graid5']['vdisk']) {
		if ($g['booting'])
			echo "Start graid5 raid... ";

		// Load geom raid5 module
		mwexec("/sbin/graid5 load");
		
		//Tune graid5
		disks_raid_graid5_tune();
		
		if ($g['booting']) {
			echo "done\n";
		}
		
	}
	
	return 0;
}

/* Optimize geom raid5 RAM use. */
function disks_raid_graid5_tune()
{
	/* Get RAM informations. */
	$raminfo = get_ram_info();
	/* Calculate real RAM in MB. */
	$realram = $raminfo['real'] / 1024 / 1024;

	if ($realram <= 128) {
		$cmd="/usr/bin/logger \"Optimizing graid5 for system with less or 128MB of RAM\"";
		mwexec($cmd);

		mwexec('sysctl value kern.geom.raid5.maxwql=10');
		mwexec('sysctl value kern.geom.raid5.maxmem=1100000');
	} else if ($realram <= 256) {
		$cmd="/usr/bin/logger \"Optimizing graid5 for system with loss or 256MB of RAM\"";
		mwexec($cmd);

		mwexec('sysctl value kern.geom.raid5.maxwql=20');
		mwexec('sysctl value kern.geom.raid5.maxmem=2000000');
	} else {
		$cmd="/usr/bin/logger \"512MB of RAM or more detected... No need to optimize RAM use for graid5\"";
		mwexec($cmd);
	}
}


/* Stop all geom RAID volumes */
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

/* Stop all geom gvinum volumes */
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

/* Stop all geom mirror volumes */
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

/* Stop all geom concat volumes */
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

/* Stop all geom stripe volumes */
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

/* Stop all geom raid5 volumes */
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

/* Delete geom gvinum volume given in parameter */
function disks_raid_gvinum_delete($raidname)
{
	global $config;

	exec("/sbin/gvinum lv {$raidname}",$rawdata);
	if (strpos($rawdata[0],"State: up") === false) {
		return 0;
	}
	
	mwexec("/sbin/gvinum rm -r {$raidname}");
	
	foreach ($config['gvinum']['vdisk'] as $a_raid) {
			if ($a_raid['name'] == $raidname) {
				foreach ($a_raid['diskr'] as $disk){				
					mwexec("/sbin/gvinum rm -r disk_{$disk}");
				}
			}
	}
	
	return 0;
}

/* Delete geom mirror volume given in parameter */
function disks_raid_gmirror_delete($raidname)
{
	global $config;

	// Stop the volume
	mwexec("/sbin/gmirror stop {$raidname}");

	// Clear the gmirror information on the hard drive
	foreach ($config['gmirror']['vdisk'] as $a_raid) {
			if ($a_raid['name'] == $raidname) {
				foreach ($a_raid['diskr'] as $disk) {				
					mwexec("/sbin/gmirror clear {$disk}");
				}
			}
	}

	return 0;
}

/* Delete geom concat volume given in parameter */
function disks_raid_gconcat_delete($raidname)
{
	global $config;

	// Stop the volume
	mwexec("/sbin/gconcat stop {$raidname}");

	// Clear the gconcat information on the hard drive
	foreach ($config['gconcat']['vdisk'] as $a_raid) {
			if ($a_raid['name'] == $raidname) {
				foreach ($a_raid['diskr'] as $disk) {				
					mwexec("/sbin/gconcat clear {$disk}");
				}
				mwexec("/sbin/gconcat destroy {$raidname}");
			}
	}

	return 0;
}

/* Delete geom stripe volume given in parameter */
function disks_raid_gstripe_delete($raidname)
{
	global $config;

	// Stop the volume
	mwexec("/sbin/gstripe stop {$raidname}");

	// Clear the gconcat information on the hard drive
	foreach ($config['gstripe']['vdisk'] as $a_raid) {
			if ($a_raid['name'] == $raidname) 	{
				foreach ($a_raid['diskr'] as $disk) {				
					mwexec("/sbin/gstripe clear {$disk}");
				}
				mwexec("/sbin/gstripe destroy {$raidname}");
			}
	}

	return 0;
}

/* Delete geom raid5 volume given in parameter */
function disks_raid_graid5_delete($raidname)
{
	global $config;

	// Stop the volume
	// Arne Woerner Advice: Don't stop the volume before deleting it.
	// mwexec("/sbin/graid5 stop {$raidname}");

	// The volume can disapear a few second after stop
	sleep(2);

	// Clear the graid5 information on the hard drive
	foreach ($config['graid5']['vdisk'] as $a_raid) {
			if ($a_raid['name'] == $raidname) 	{
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

/* Initialise HARD DRIVE for installation (creating 2 partition) */
function fdisk_hd_install($harddrive)
{
	global $config, $g;

	/* getting disk information */
	$fdisk_info=fdisk_get_info($harddrive);

	/* setting partition size to 32Mb*/	
	$part_freenas_size=32;

	/* convert Mb to b */
	$part_freenas_size=$part_freenas_size * 1024 * 1024;
	$part1_size=$part_freenas_size / $fdisk_info['sec_size'];
	$part2_size=$fdisk_info['total'] - $part1_size;

	/* Create fdisk config file (fdisk.conf) */
	$fd = fopen("{$g['varetc_path']}/fdisk.conf", "w");
	if (!$fd) {
		printf("Error: cannot open fdisk.conf in fdisk_hd_install().\n");
		return 1;
	}

	$fdiskconf .= <<<EOD
g c{$fdisk_info['cyl']} h{$fdisk_info['head']} s{$fdisk_info['sect']}
p 1 165 1 $part1_size
p 2 165 $part1_size $part2_size
p 3 0 0 0
p 4 0 0 0
a 1

EOD;

	fwrite($fd, $fdiskconf);
	fclose($fd);

	/* Fdisk the disk */
	/* Warning: Ask two questions to the user */
	mwexec("/sbin/fdisk -f {$g['varetc_path']}/fdisk.conf /dev/$harddrive");

	return 0;
}

/* Return information about an harddrive given in parameter
	$result['total'] : size
	$result['cyl'] : cylinders
	$result['head'] : heads
	$result['sect'] :  sectors/track
	$result['sec_size'] : Media sector size
*/
function fdisk_get_info($harddrive)
{
	global $config, $g;
	
	exec("/sbin/fdisk /dev/$harddrive",$rawdata);
	
	$result=array();
	
	foreach ($rawdata as $line) 	{
		/* separe the line by space or egal*/
		
		$aline= preg_split("/[\s,]+|=/", $line);
		
		$first_word = chop($aline[0]);
		
		if ($aline[0] == "Media") {
			$result['sec_size']=chop($aline[4]);
			continue ;
		}
		
		if ($aline[0] == "cylinders") 	{
			
			$result['cyl']=chop($aline[1]);
			$result['head']=chop($aline[3]);
			$result['sect']=chop($aline[5]);
			continue ;
		}
		
	}
	
	$result['total'] = $result['cyl'] * $result['head'] * $result['sect'] ;

	return $result;
	
}

/* Is this function still used ??? */
function disks_bsdlabel($harddrive,$partition,$type)
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

/* Set the ataidle tools parameters */
function disks_set_ataidle()
{
	global $g, $config;

	if (is_array($config['disks']['disk'])) {
		foreach ($config['disks']['disk'] as $disk) {
		
			if ($disk['type']=="IDE") {		
				/* If UDMA mode forced, launch atacontrol */
				if (isset($disk['udma']) && ($disk['udma'] != "auto")) {
					mwexec("/sbin/atacontrol mode {$disk['name']}  {$disk['udma']}");
				}
					
				/* Don't use ataidle if all is disabled */
				if (($disk['harddiskstandby'] == 0) && ($disk['apm'] == 0) && ($disk['acoustic'] == 0)) {
					continue;
				}
						
				/* Found the channel and device number from the /dev name */
				/* Divise the number by 2, the interger is the channel number, the rest is the device */
				
				$value=trim($disk['name'],'ad');		
				
				$value=intval($value);
				$channel = $value/2;
				$device=$value % 2;
				$channel=intval($channel);
				$time=$disk['harddiskstandby'];
				$apm=$disk['apm'];
				$ac=$disk['acoustic'];				
					
				$cmd = "/usr/local/sbin/ataidle ";
				
				if ($disk['acoustic'] != 0) {
					$cmd .= "-A $ac  ";
				}
				if ($disk['apm'] != 0) {
					$cmd .= "-P $apm ";
				}
				if ($disk['harddiskstandby'] !=0) {
					$cmd .= "-S $time ";
				}
					
				$cmd .= "$channel $device";
				
				mwexec($cmd);	
			
			}	

		}
		return 1;
	}
			
	return 0;
}

/* 
Get list of partition information from disk.
Result is in the form:
[1] => Array
(
	[start] => 31
	[size]  => 409169
	[type]  => 0xa5
	[flags] => 0x80
)

Called in disks_manage_tools.php
*/
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

// Check the status of a geli encrypted disk
// Return 0 is OK, 1 is no OK
function disks_geli_check($gelifullname)
{
	$result = 1;
	if (file_exists("$gelifullname")) {
		$result = 0;
	}
	return $result;
}

// Attach a geli encrypted disk
// Return 0 is OK, 1 is no OK
function disks_geli_attach($gelifullname,$passphrase,$verbose = false)
{
	$result = 1;
	// Remove the 4 last character ".eli" of $gelifullanme
	$gelifullname=rtrim($gelifullname,".eli");
	if(false == $verbose) {
		$result=mwexec("/sbin/geli attach -X " . escapeshellarg($passphrase) . " " . $gelifullname);
	} else {
		$result=mwexec("/sbin/geli attach -v -X " . escapeshellarg($passphrase) . " " . $gelifullname);
	}
	return $result;
}

// Detach a geli encrypted disk
// Return 0 is OK, 1 is no OK
function disks_geli_detach($gelifullname)
{
	$result=mwexec("/sbin/geli detach $gelifullname");
	return $result;
}

// Kill (destroy) a geli encrypted volume
// Return 0 is OK, 1 is no OK
function disks_geli_kill($gelifullname)
{
	$result=mwexec("/sbin/geli kill $gelifullname");
	return $result;
}

// Detach all geli encrypted disks
// Return 0 is OK, 1 is no OK
function disks_geli_detach_all()
{
	global $g, $config;
	
	$result = 0;
	
	if (!is_array($config['geli']['vdisk']))
	$config['geli']['vdisk'] = array();
	
	$a_geli = &$config['geli']['vdisk'];
	
	foreach($a_geli as $geli) {
		$result |= disks_geli_detach($geli['fullname']) ;
	}
	
	return $result;
}

// Check is the iscsi initiator is connected to the target
// Return 0 is OK, 1 is no OK
function disks_iscsiinit_check($name)
{
	// To be Checked with a camcontrol ??
	return 0;
}

function disks_format($disk,$type,$notinitmbr,$minspace)
{
	if (!$notinitmbr) {
		echo gettext("Erasing MBR and all partitions").".\n";
		system("dd if=/dev/zero of=" . escapeshellarg($disk) . " bs=32k count=640");
	}

	switch ($type) {
		case "ufsgpt":
			// Create GPT partition table
			echo sprintf(gettext("Destroying old %s information"), "GPT").":\n";
			system("/sbin/gpt destroy " . escapeshellarg($disk));
			echo sprintf(gettext("Creating %s partition"), "GPT").":\n";
			system("/sbin/gpt create -f " . escapeshellarg($disk));
			system("/sbin/gpt add -t ufs " . escapeshellarg($disk));
			// Create filesystem
			echo gettext("Creating filesystem with 'Soft Updates'").":\n";
			system("/sbin/newfs -m $minspace -U " . escapeshellarg($disk) . "p1");
			echo gettext("Done")."!\n";
			break;
		case "softraid":
			// Initialise the disk
			echo gettext("Initializing disk").".\n";
			system("/bin/dd if=/dev/zero of=" . escapeshellarg($disk) . " bs=1m count=16");
			echo gettext("Done")."!\n";
			break;
		case "msdos":
			// Initialize disk
			echo gettext("Creating one partition").":\n";
			system("/sbin/fdisk -I -b /boot/mbr " . escapeshellarg($disk));
			// Initialize the partition
			echo gettext("Initializing partition")."\n";
			system("/bin/dd if=/dev/zero of=" . escapeshellarg($disk) . "s1 bs=32k count=16");
			// Create filesystem
			echo gettext("Creating filesystem").":\n";
			system("/sbin/newfs_msdos -F 32 " . escapeshellarg($disk) . "s1");
			echo "Done!\n";
			break;
		case "ext2":
			// Initialize disk
			echo gettext("Creating one partition").":\n";
			system("/sbin/fdisk -I -b /boot/mbr " . escapeshellarg($disk));
			// Initialize the partition
			echo gettext("Initializing partition")."\n";
			system("/bin/dd if=/dev/zero of=" . escapeshellarg($disk) . "s1 bs=32k count=16");
			// Create filesystem
			echo gettext("Creating filesystem").":\n";
			system("/usr/local/sbin/mke2fs " . escapeshellarg($disk) . "s1");
			echo "Done!\n";
			break;
	}
}

// File system consistency check and interactive repair.
// The filesystem type will be get from the mount configuration information.
// Parameter:
// disk   - Name of the disk.
// umount - Unmount disk if necessary.
// Return: 0 is OK, 1 is not OK
function disks_fsck($disk,$umount = false)
{
	global $config;

	/* Check if there exits any mounts. */
	if(!is_array($config['mounts']['mount']))
		return 1;

	/* Get the id of the disk. */
	$id = array_search_ex($disk, $config['mounts']['mount'], "fullname");
	/* Get the filesystem type of the disk. */ 
	$fstype = $config['mounts']['mount'][$id]['fstype'];
	/* Check if disk is mounted. */
	$ismounted = disks_check_mount_fullname($disk);
	/* Umount disk temporary if necessary. */
	if($umount && $ismounted) {
		echo("<strong class='red'>" . gettext("Note") . ":</strong> " . gettext("The disk is currently mounted! The mount point will be removed temporary to perform selected command.") . "<br><br>");
		disks_umount_fullname($disk);
	}

	switch($fstype) {
		case "":
		case "ufs":
			system("/sbin/fsck -t ufs -y -f " . escapeshellarg($disk));
			break;
		case "msdosfs":
			system("/sbin/fsck -t msdos -y -f " . escapeshellarg($disk));
			break;
	}

	/* Mount disk again if necessary. */
	if($umount && $ismounted) {
		disks_mount_fullname($disk);
	}

	return 0;
}

// Get disk informations.
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
function disks_get_diskinfo($disk)
{
	exec("/usr/sbin/diskinfo {$disk}",$rawdata);

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
