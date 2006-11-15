<?php
/*
	disks.inc
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
	
function disks_mount_all()
{
	global $config, $g;
	
	if ($g['booting'])
		echo "Mounting Partitions... ";
	
	/* For each device configured:*/
	
	if (is_array($config['mounts']['mount'])) 	{
		foreach ($config['mounts']['mount'] as $mountent) 		{
			/* Advanced Umount filesystem if not booting mode (mount edition)*/
			if (!$g['booting'])
				disks_umount_adv($mountent);
								
			/* mount filesystem */
			disks_mount($mountent);	
		
		}
	}
			
	if ($g['booting'])
		echo "done\n";
	
	return 0;
	
}

function disks_mount($mount)
{
	global $config, $g;
	
	/* mount the specified $mount partition */
	/* The $mount variable is the all config table for the mount point*/
	
	/* Create one directory for each device under mnt */
	
	$mountname=escapeshellcmd($mount['sharename']);
		
	@mkdir ("/mnt/$mountname",0777);
			
	/* mount the filesystems */
	
	/* Must use a special dev name for software RAID volume */
	
	if (strcmp($mount['partition'],"gvinum") == 0)
		$devname="/dev/gvinum/{$mount['mdisk']}";
	else if (strcmp($mount['partition'],"gmirror") == 0)
		$devname="/dev/mirror/{$mount['mdisk']}";
	else if (strcmp($mount['partition'],"graid5") == 0)
		$devname="/dev/raid5/{$mount['mdisk']}";
	else
		$devname="/dev/{$mount['mdisk']}{$mount['partition']}";

	/* check the fileystem only if there is a problem*/
	
	// This part is too stupid: I must read the FreBSD start script for use the same intelligent method for checking hard drive
	
	switch ($mount['fstype']) 	{
		case "ufs":
			
			if (mwexec("/sbin/mount -t ufs  -o acls {$devname} /mnt/$mountname") == 0) 			{
				/* Change this directory into 777 mode */
				mwexec("/bin/chmod 777 /mnt/$mountname");
				return 0;
			}
			else 			{
				/* If it's NOK, Check  filesystem and do a fsck, answer Yes to all question*/
				mwexec("/sbin/fsck -y -t ufs {$devname}");
				/* Re-try to mount the partition */
				if (mwexec("/sbin/mount -t ufs -o acls {$devname} /mnt/$mountname") == 0) 				{
					/* Change this directory into 777 mode */
					mwexec("/bin/chmod 777 /mnt/$mountname");
					return 0;
				}
				else 				{
					/* Not OK, remove the directory, prevent writing on RAM disk*/
					@rmdir ("/mnt/$mountname");
					return 1;
				}
				
			}
		   break;
		   
		case "msdosfs":
			if (mwexec("/sbin/mount_msdosfs -u ftp -g ftp -m 777 {$devname} /mnt/$mountname") == 0)
				return 0;
			else 			{
				exec("/sbin/fsck -y -t msdosfs {$devname}");
				if (mwexec("/sbin/mount_msdosfs -u ftp -g ftp -m 777 {$devname} /mnt/$mountname") == 0)
					return 0;
				else 				{
					/* Not OK, remove the directory, prevent writing on RAM disk*/
					@rmdir ("/mnt/$mountname");
					return 1;
				}
			}
		
		   break;
		   
		case "ntfs":
			if (mwexec("/sbin/mount_ntfs -u ftp -g ftp -m 777 {$devname} /mnt/$mountname") == 0)
				return 0;
			else 			{
				/* Not OK, remove the directory, prevent writing on RAM disk */
				@rmdir ("/mnt/$mountname");
				return 1;
			}
			break;		   
		   
		case "ext2fs":
			if (mwexec("/sbin/mount_ext2fs {$devname} /mnt/$mountname") == 0) 			{
				/* Change this directory into 777 mode */
				mwexec("/bin/chmod 777 /mnt/$mountname");
				return 0;
			}
			else 			{
				exec("/usr/local/sbin/e2fsck -f -p {$devname}");
				if (mwexec("/sbin/mount_ext2fs {$devname} /mnt/$mountname") == 0) 				{
					/* Change this directory into 777 mode */
					mwexec("/bin/chmod 777 /mnt/$mountname");
					return 0;
				}
				else 				{
					/* Not OK, remove the directory, prevent writing on RAM */
					@rmdir ("/mnt/$mountname");
					return 1;
				}
			}
		   break;
		}
	
}

/* Advanced mount using disk and partition. */
function disks_mount_ex($disk,$partition)
{
  global $config;

  /* Search the mount list for given disk and partition. */
  foreach($config['mounts']['mount'] as $mountk => $mountv) {
    if($mountv['mdisk'] == $disk && $mountv['partition'] == $partition)
      $mount = $mountv;
  }

  if($mount)
    return disks_mount($mount);
  else
    return 1;
}

/* Umount the specified mount point. */
function disks_umount($mount)
{
	/* The $mount variable is the all config table for the mount point */
	$mountname = "/mnt/{$mount['sharename']}";

	if (mwexec("/sbin/umount $mountname") == 0) {
		if(@rmdir("$mountname"))
			return 0;
		else
			return 1;
	}
	else
		return 1;
}

/* Advanced umount using disk and partition. */
function disks_umount_ex($disk,$partition)
{
  global $config;

  /* Search the mount list for given disk and partition. */
  foreach($config['mounts']['mount'] as $mountk => $mountv) {
    if($mountv['mdisk'] == $disk && $mountv['partition'] == $partition)
      $mount = $mountv;
  }

  if($mount)
    return disks_umount_adv($mount);
  else
    return 1;
}

/* Advanced unmount the specified mount point without using the sharename (changing it). */
function disks_umount_adv($mount)
{
	/* The $mount variable is the all config table for the mount point */
	$mountpart="{$mount['partition']}";
	
	if ((strcmp($mountpart,"gvinum") == 0) || (strcmp($mountpart,"gmirror") == 0) || (strcmp($mountpart,"graid5") == 0))
		$complete = "{$mount['mdisk']}";
	else
		$complete = "{$mount['mdisk']}{$mount['partition']}";
	
	// get the mount list
	$detmount = get_mounts_list();
	
	foreach ($detmount as $detmountk => $detmountv) {
		// If we found the mount point on the device
		if (strcmp($detmountv['mdisk'],$complete) == 0) {
			$mountname="{$detmountv['mp']}";
		}
	}
	
	if ($mountname) {
		exec("/sbin/umount $mountname");
		@rmdir ("$mountname");
		return 0;
	}
	else
		return 1;
}

/* This option check if the given partition (s1 by default)) of the disk is mounted. */
/* Return 0 if not, 1 if yes */
function disks_check_mount($disk,$partition = "s1")
{
	$detmount = get_mounts_list();
	$status=0;
	
	// Recreate the full system name device+s+partition number
	if((0 == strcmp($partition,"gvinum")) || (0 == strcmp($partition,"gmirror")) || (0 == strcmp($partition,"graid5")))
		$complete = "{$disk}";
	else
    $complete = "{$disk}{$partition}";

	foreach ($detmount as $detmountk => $detmountv) {
		if (strcmp($detmountv['mdisk'],$complete) == 0) {
			$status="1";
			return $status;
		}
	}
	return $status;
}

/* This option check if the mount is mounted. */
function disks_check_mount_ex($mount)
{
  return disks_check_mount($mount['mdisk'], $mount['partition']);
}

function disks_status($diskname)
{
	// This option check if the configured disk is online (detected by the system)
	
	global $config, $g;
	$detectedlist = get_physical_disks_list();
	$status="MISSING";
	foreach ($detectedlist as $detecteddisk => $detecteddiskv) 	{
		
		if ($detecteddisk == $diskname['name'])
		{
			$status="ONLINE";
			if (($detecteddiskv['size'] != $diskname['size']) || ($detecteddiskv['desc'] != $diskname['desc']))
				$status="CHANGED";
			break;
		}
	}
	return $status;
}

function disks_addfstab($cfgdev,$cfgtype)
{
	
	// Where this function is called ? I don't remember ;-)
	// Must add feature for replacing old entry with new...
	
	global $config, $g;
	
	/* Open or create fstab in RW */
	$fd = fopen("{$g['etc_path']}/fstab", "w");
	
	if ( $fd ) 	{
		/* check for the precence of dev */
		/* ADD (check if it's ADD line or replace) the line for the dev */
		$fstab = "/dev/$cfgdev /mnt/$cfgdev $cfgtype rw 1 1\n";
			
		/* write out an fstab */
		fwrite($fd, $fstab);

		/* close file */
		fclose($fd);
	}
	else 	{
		die( "fopen failed for {$g['etc_path']}/fstab" ) ;
	}
	
}

function disks_umount_all()
{
	// Unmount all mount point
	global $config, $g;
	
	/* Sync disks*/
	mwexec("/bin/sync");
					
	if (is_array($config['mounts']['mount']))
	{
		foreach ($config['mounts']['mount'] as $mountent)
		{
      /* Umount filesystem */
      disks_umount($mountent);
		}
	}

	return 0;
}

function disks_raid_gvinum_configure()
{
	// Configure and sart all gvinum RAID volume
		
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
drive disk_{$diskrv} device /dev/{$diskrv}s1a

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

function disks_raid_gmirror_configure()
{
	global $config, $g;
	
	/* Create the gmirror device */

	if ($config['gmirror']['vdisk']) {
		
		// Load gmirror
		
		mwexec("/sbin/gmirror load");
				
		foreach ($config['gmirror']['vdisk'] as $a_raid_conf) 	{
	
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

function disks_raid_graid5_configure()
{
	global $config, $g;
	
	/* Create the graid5 device */

	if ($config['graid5']['vdisk']) {
		
		// Load gmirror
		
		mwexec("/sbin/graid5 load");
		
		foreach ($config['graid5']['vdisk'] as $a_raid_conf) 	{
	
			/* Create each volume */
			
			$cmd = "/sbin/graid5 label -s 131072 {$a_raid_conf['name']} ";
			
			foreach ($a_raid_conf['diskr'] as $diskrk => $diskrv) 	{
				$cmd .= "{$diskrv} ";
			}			
		
			mwexec($cmd);
		
		}	
	}

	return 0;
}

function disks_raid_start()
{
	// Start ALL software RADI volume
	
	global $config, $g;
	
	/* Start software RAID */
	disks_raid_gvinum_start();
	disks_raid_gmirror_start();
	disks_raid_graid5_start();
	
	return 0;
	
}	


function disks_raid_gvinum_start()
{
	global $config, $g;
	
	/* Generate the raid.conf file */

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

function disks_raid_graid5_start()
{
	global $config, $g;
	
	/* Start Geom RAID5 */

	if ($config['graid5']['vdisk']) {
	
		if ($g['booting'])
			echo "Start graid5 raid... ";

		// Load geom raid5 module
		
		mwexec("/sbin/graid5 load");
		
		if ($g['booting'])
		echo "done\n";
		
	}
	
	return 0;
}

function disks_raid_stop()
{
	// Stop ALL software RAID volume

	disks_raid_gvinum_stop();
	disks_raid_gmirror_stop();
	disks_raid_graid5_stop();
	
	return 0;
	
}

function disks_raid_gvinum_stop()
{
	global $config, $g;
	
	/* Generate the raid.conf file */

	if ($config['gvinum']['vdisk']) {
		
		/* stop each volume */
		
		foreach ($config['gvinum']['vdisk'] as $a_raid_conf) 	{
			mwexec("/sbin/gvinum stop {$a_raid_conf['name']}");
		}

	}
	
	return 0;
}

function disks_raid_gmirror_stop()
{
	global $config, $g;
	
	/* Generate the raid.conf file */

	if ($config['gmirror']['vdisk']) {
	
		/* start each volume */
		
		foreach ($config['gmirror']['vdisk'] as $a_raid_conf) 	{
			mwexec("/sbin/gmirror stop {$a_raid_conf['name']}");
		}
		
		
	}
	
	return 0;
}

function disks_raid_graid5_stop()
{
	global $config, $g;
	
	/* Generate the raid.conf file */

	if ($config['graid5']['vdisk']) {
	
		/* start each volume */
		
		foreach ($config['graid5']['vdisk'] as $a_raid_conf) 	{
			mwexec("/sbin/graid5 stop {$a_raid_conf['name']}");
		}
		
		
	}
	
	return 0;
}

function disks_raid_gvinum_delete($raidname)
{
	global $config, $g;
	
	/* Delete a gvinum volume */
	
	//echo "DEBUG: delete {$raidname}";

	exec("/sbin/gvinum lv $raidname",$rawdata);
	if (strpos($rawdata[0],"State: up") === false) {
		return 0;
	}
	
	mwexec("/sbin/gvinum rm -r $raidname");
	
	foreach ($config['gvinum']['vdisk'] as $a_raid) 	{
			if ($a_raid['name'] == $raidname) 	{
				foreach ($a_raid['diskr'] as $disk) 	{				
					mwexec("/sbin/gvinum rm -r disk_{$disk}");
				}
			}
	}
	
	return 0;
}

function disks_raid_gmirror_delete($raidname)
{
	global $config, $g;
	
	/* Delete a gmirror volume */
	
	// Stop the volume
	
	mwexec("/sbin/gmirror stop $raidname");
	
	// Clear the gmirror information on the hard drive
	
	foreach ($config['gmirror']['vdisk'] as $a_raid) {
			if ($a_raid['name'] == $raidname) 	{
				foreach ($a_raid['diskr'] as $disk) 	{				
					mwexec("/sbin/gmirror clear {$disk}");
				}
			}
	}
	
	return 0;
}

function disks_raid_graid5_delete($raidname)
{
	global $config, $g;
	
	/* Delete a graid5 volume */
	
	// Stop the volume
	
	mwexec("/sbin/graid5 stop $raidname");
	
	// Clear the graid5 information on the hard drive
	
	foreach ($config['graid5']['vdisk'] as $a_raid) {
			if ($a_raid['name'] == $raidname) 	{
				foreach ($a_raid['diskr'] as $disk) 	{				
					mwexec("/sbin/graid5 clear {$disk}");
				}
			}
	}
	
	return 0;
}

function fdisk_hd_install($harddrive)
{
	global $config, $g;
	
	/* Initialise HARD DRIVE for installing FreeNAS (creating 2 partition) */
	
	// OLD NOT USED FUNCTION !!!
	
	/* getting disk information */
	$fdisk_info=fdisk_get_info($harddrive);
	
	/* setting FreeNAS partition size to 32Mb*/	
	
	$part_freenas_size=32;
	
	/* convert Mb to b */
	$part_freenas_size=$part_freenas_size * 1024 * 1024;
	
	$part1_size=$part_freenas_size / $fdisk_info['sec_size'];
	$part2_size=$fdisk_info['total'] - $part1_size;
	
	/* Create fdisk config file */
	
	
	/* generate fdisk.conf */
	$fd = fopen("{$g['varetc_path']}/fdisk.conf", "w");
	if (!$fd)
	{
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

function fdisk_get_info($harddrive)
{
	
	/* Return information about an harddrive
	
	$result['total'] : size
	$result['cyl'] : cylinders
	$result['head'] : heads
	$result['sect'] :  sectors/track
	$result['sec_size'] : Media sector size
	
	*/
	
	global $config, $g;
	
	exec("/sbin/fdisk /dev/$harddrive",$rawdata);
	
	$result=array();
	
	foreach ($rawdata as $line)
	{
		/* separe the line by space or egal*/
		
		$aline= preg_split("/[\s,]+|=/", $line);
		
		$first_word = chop($aline[0]);
		
		if ($aline[0] == "Media")
		{
			$result['sec_size']=chop($aline[4]);
			continue ;
		}
		
		if ($aline[0] == "cylinders")
		{
			
			$result['cyl']=chop($aline[1]);
			$result['head']=chop($aline[3]);
			$result['sect']=chop($aline[5]);
			continue ;
		}
		
	}
	
	$result['total'] = $result['cyl'] * $result['head'] * $result['sect'] ;

	return $result;
	
}

function disks_bsdlabel($harddrive,$partition,$type)
{
	global $config, $g;
	
	// Generating BSD Label table
	
	passthru("/sbin/bsdlabel " . escapeshellarg($harddrive) ."$partition > {$g['tmp_path']}/label.tmp");

	// put this file on a array
	$tableau = file("{$g['tmp_path']}/label.tmp");

	// Open this file in add mode 
	$handle = fopen("{$g['tmp_path']}/label.tmp", 'a');
	
	while(list(,$val) = each($tableau))
	{
		// If the line contain the word "unused"
		
		// Why did I replace 'a' with 'c' ? Must found the URL where I found this information
		
		if (ereg ("unused",$val))
		{
			// Replacing c: by a:
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


function disks_set_ataidle()
{
	global $g, $config;

	if (is_array($config['disks']['disk'])) 	{
		foreach ($config['disks']['disk'] as $disk) {
		
			if ($disk['type']=="IDE") 		{		
					/* If UDMA mode forced, launch atacontrol */
					if (isset($disk['udma']) && ($disk['udma'] != "auto"))
						mwexec("/sbin/atacontrol mode {$disk['name']}  {$disk['udma']}");
					
					/* Don't use ataidle if all is disabled */
					if (($disk['harddiskstandby'] == 0) && ($disk['apm'] == 0) && ($disk['acoustic'] == 0))
						continue;
						
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
					
					/* mwexec("/usr/local/sbin/ataidle -A $ac -P $apm -S $time $channel $device"); */
					
					$cmd = "/usr/local/sbin/ataidle ";
					
					if ($disk['acoustic'] != 0)
						$cmd .= "-A $ac  ";
					if ($disk['apm'] != 0)
						$cmd .= "-P $apm ";
					if ($disk['harddiskstandby'] !=0)
						$cmd .= "-S $time ";
					
					$cmd .= "$channel $device";
					
					mwexec($cmd);
					
				
			}	
	
		}
		return 1;
	}
			
	return 0;
}

/* Get list of partition information from disk. */
function disks_get_partition_info($disk)
{
  // [1] => Array
  //    (
  //        [start] => 31
  //        [size]  => 409169
  //        [type]  => 0xa5
  //        [flags] => 0x80
  //    )
  
  exec("/sbin/fdisk -s /dev/{$disk}", $rawdata);
  array_shift($rawdata);
  array_shift($rawdata);

  $result = array();

  foreach($rawdata as $partinfo)
  {
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
?>
