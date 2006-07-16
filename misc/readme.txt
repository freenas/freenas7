FreeNAS (http://www.freenas.org) 
Olivier Cochard-Labbe (olivier@freenas.org)
Licence: BSD

Release: 0.68 (WORKING RELEASE)

============== SUMMARY =========

1. Files and directories listing
2. How to generate an FreeNAS ISO or IMG  file
3. Building FreeNAS with the latest sources
4. History changes log

================================
1. Files and directories listing
- /freenas/bootloader : contain FreeBSD boot loader files
- /freenas/rootfs: Minimum FreeBSD root filesystem and FreeNAS script/WebGUI
- /freenas/svn: contain all the up-to-date (working) release and scripts

================================
2. How to generate an FreeNAS ISO or IMG  file

Edit the scripts:
- /freenas/svn/misc/setupfreeenas.sh
for modify the directory variable with yours.

start /freenas/svn/misc/setupfreeenas.sh
And select your need

burn the freenas.iso file created.

=================================
3. Building FreeNAS with the latest sources

(normally possible with setupfreenas.sh)

Here is how to build a FreeNAS from the latest source file:

- Use the root user account on your FreeBSD system

3.1. Download the FreeNAS tgz file (new bootdir, new binary, new symlinks, old FreeNAS scripts)
cd /usr/local
fetch http://www.freenas.org/donwloads/freenas.tgz
tgz freenas.tgz

This action Create two directories:
freenas/rootfs
freenas/bootloader

cd /usr/local/freenas

3.2 Update your freenas scripts/WebGUI code with SVN:
(this create the svn directory)

svn co https://svn.sourceforge.net/svnroot/freenas/trunk svn

You should now have this directories now:
/usr/local/freenas
/usr/local/freenas/rootfs
/usr/local/freenas/bootloader
/usr/local/freenas/svn


3.3 Use the building script:
(this script overwrite the etc, conf and www files with the new one, and rebuild and ISO and IMG image).
svn/misc/setupfreenas.sh


################ History Change logs ##############

FreeNAS 0.68:
- Need a 32MB of minimal disk space for installing now
- Fixed su permisson
- Fixed FTP anonymous login that was not disabled
- Added DHCP client option for LAN interface (WebgUI only). Thanks to Volker Theile
- Changed default samba buffer size to 16384
- Added "-maproot=root" to NFS configuration file
- Added option for not erasing the MBR when initializing a disk because some RAID controller store information in the MBR.
- Replace PHP 4 by PHP 5 (preparing rewriting the code object oriented)

KNOW BUG TO BE FIXED: Giga ethernet NIC problem after a reboot (need to find a Gbiga NIC for reproduce the problem)

FreeNAS 0.671:
- gvinum bug fix: fix raid deletion , add config file conversion

FreeNAS 0.67:

- Multilanguage WebGUI (thanks to Aliet Santiesteban Sifontes), French and spanich translation are work in progress.
If you want translate the WebGUI, You can use the english file as reference file (this file is a work in progress and can change).
and send me your translated file by email.

- Upgrade to FreeBSD 6.1-STABLE
- Add software RAID: geom mirror (NEED TO BE TESTED)
- Replace vstpd 2.0.4 by pure-ftpd 1.0.21 (support UTF-8)
- Update RSYNC to 2.6.8
- Update Smartmon tools to 5.36
- Update e2fsprogs to 1.39
- Update iSCSI initiator to 0.17
- Replace Howl (maintenance stoped) by Apple Bonjour
- SSH: Adding the sftp subsystem
- CIFS: added dos charset 852, added unix charset, option for hidding some share, hide folder ".snap" (thanks to Jorge Valdes)
- FTP: Added FTP banner option, hide foler '.snap'
- AFP: Add server name configuration
- Adding CLI tools: fetch (FTP/HTTP download client)
- Support large FAT32 parition.

FreeBSD Kernel change:
- Drivers added: a100u2w
- Replacing 4BSD scheduler with ULE scheduler
- ACPI module is included in the kernel

Bug Fixed:
- clear log: rsync, smartd, dameon
- Permit to configure with the WebGUI this wireless card: awi,ral,iwi,ipw,ural
- no need to reboot for applying tuning settings.
- Speed of LAN and OPT interfaces
- Missing NFS daemons: rpc.statd and rpc.lockd
- static route edition
- somes tipos in the WebGUI

Vyatcheslav Tyulyukov patchs:
- Simplify adding existing disk
- improve RAID script

- FreeNAS generating script 'setupfreenas.sh' by Scott Zahn

FreeNAS 0.66:

News features:
- Upgraded to FreeBSD 6.1 RC #12
- Added:Broadcom NetXtreme II (BCM5706/BCM5708) PCI/PCIe Gigabit Ethernet adapter driver
- Added FreeBSD version on the main page (Thanks to Stephan)
- Add iSCSI diagnostic page (usefull for display the list of target name)
- Permit to mount more than 1 partition for the same hard drive
- Permit to use number in the login name
- Add CIFS Buffer configuration option
- Added Smartd and daemon on the syslog setting page

bugs fixed:
- Some typo fixed (Thanks to Stephan)
- Hide the domain admin password (on the web gui and on the diag page)
- Fixed: Add the 'scp' tools (forget to add it)
- Fixed a bug when editing user: the 'full shell' check box was missing
- Fixed the log clear of SSH part
- Fixed the AFP checkbox problem with some browser
- Fixed the tune value parameter (inversed)
- Fixed: AAC and APM desc inversed
- Fixed: Permit to use '-' and '_' character in sharename
- Fixed: The bad umount script for shutdown and reboot (still a problem for the shared data partition)

Vyatcheslav Tyulyukov patches:
- Fixed: The disable beep feature
- Fixed: Wrong display of RAID disk


FreeNAS 0.65:

News features :
- Adding MS Windows Domain authentication
- Adding Aple File Protocol (AFP)
- Announce service with Zeroconf (howl)
- iSCSI initator (NOT TESTED)

Minimum RAM requirement: 96MB

Change log:

- Adding MS Windows Domain authentication (tested with a 2K server and 2003 server)
- New protocol: AFP (AppleShare file server)
- Adding Zeroconf (howl) for announcing services
- adding iSCSI initator (NOT TESTED)
- New Install script (use the same method as the WebGUI: dd the image file directly on the hard drive) and made a more clear code
- Gvinum (Software RAID): Use 'sd lenght 0' parameter in the place of the disk size.
- Smartd have this own log file
- Local user with 'full shell' option can became root (with su command)
- The Web Admin admin password match the root password.
- Add option for forcing UDMA mode (to be used when there is the UDMA_ERROR.... LBA error message).
- Adding a link to the exec page on the diagnostic menu.
- Adding option for formating disk in UFS without soft Updates and reserved space to 0 (save 8% of space disk).
- Mount the UFS disk with ACLS enabled
- Permit to change the user used by rsync daemon
- Adding 'aaccli' tools (usable from command line only)
- Little fixe for AFP configuration  (WebGUI, mount point, etc..)
- Changing the classical syslogd by a patched circular log syslogd (adapted to FreeBSD by the pfsense project)....
- Improved rsync client code: thanks to Mat Murdock
- Add top information on the status page: thanks to Stefan Hendricks
- Adding changing MTU size...
- Add option for enabling/disabling tuning kernel variables
- changing Samba mask for file/directory:
If 'anonymous' mode: use 0666 for file and 0777 for directory
If 'user' mode: use 0744 for file and 0755 for directory
- Add FTP daemon option: min/max port and public IP address
- add drivers: ath -- Atheros IEEE 802.11 wireless network driver (thanks to Foxglove for the lines to be added)
- add drivers: HighPoint HPT374 ATA RAID Controller
- add drivers: ipw -- Intel PRO/Wireless 2100 IEEE 802.11 driver
- add drivers: iwi -- Intel PRO/Wireless 2200BG/2225BG/2915ABG IEEE 802.11 driver
- Upgrading to samba-3.0.21c
- Upgrading rsync to 2.6.7
- Upgrading to FreeBSD 6.1-PRERELEASE #10


FreeNAS 0.64:

bug fixes:
- Fixed the read-only UFS disks
- Fixed the asian support for CIFS share
- Fixed some tipos in the WebGUI
- Used a valided method for creating software RAID volume (but still same problem with software RAID 5)
- Minors enhancement in status:system page 

FreeNAS 0.62:

New features:
- Changed the default IP address to: 192.168.1.250
- Upgrading FreeBSD version from 6.0 to 6.1 Pre-release
- Replaced the fwe (non standard Firewire on ethernet) drivers by the fwip (standard IP on firewire)
- Included the Firmware Module for Qlogic based SCSI and FibreChannel SCSI Host
Adapters in the kernel
- Add SSH daemon that permit SCP only (by using scponly shell)
Warning: Actualy scponly is not chrooted, then ssh users can read the config file with clear password
- Add Cron/Rsync client: Permit to schedule RSYNC file synchronisation with another FreeNAS box (one master, and lot's of clients).
About the SYNC between two FreeNAS box, You must have the same share name on the box. Actually the rsync server reachability is not checked.
- Add text editor: nano (more easy for playing with FreeNAS!)
- Upgrade vsftpd from 2.0.3 to 2.0.4
- A little more intelligent mount script (check filesystem only if there is a problem)
- Adding the 'load average' information on the status page (Request Id: 1442490)
- Adding an option for shutdown from the console (Request Id: 1433067)
- Add e2fsck (ext2/ext3 filesystem check tools)

Bugs fixed:
- Fixed the FTP bug that permit the user going to the / folder (chrooting vsftpd)
- Remove the link0 options for fxp drivers (seem create problem with some users)
- Prevent using the reserved system login name (bug n°1433691)
- Prevent adding the system partition on the disk:mount point list
- No more error message "no config file found" when the image file is directly written on CF or hard drive. (bug n° 1424451)
- FAT volume are now mounted in read/write (chmod 777) for all services(bug n° 1400583 )
- Share/Mount Name and Description Error checking (bug n°1433339)
- Mount share not sorted (Request Id: 1442332)
- Error message "This group already exists in the group list" when editing group (bug n°1443102)
- Rsync log works now (bug n°1437039)
- Editing user prevent starting WebGUI (bug n°1443403)

Tunning:
- setting kernels variables to:

net.inet.tcp.delayed_ack=0
net.inet.tcp.sendspace=65536
net.inet.tcp.recvspace=65536
net.inet.udp.recvspace=65536
net.inet.udp.maxdgram=57344
net.local.stream.recvspace=65535
net.local.stream.sendspace=65535
kern.ipc.maxsockbuf=2097152
kern.ipc.somaxconn=8192
kern.ipc.maxsockets=16424
kern.ipc.nmbclusters=60000
kern.maxfiles=65536
kern.maxfilesperproc=32768

FreeNAS 0.6:

- Adding local user/group authentication
- Adding Rsync daemon
- Support hard drive biggest than 2TB (using GPT/EFI in the place of the MBR)

And minors changes/Bugs fixed:

- Add time/date display on the system page
- Adds comment for shares
- Add option in the mount point list for re-try to mount a share
- Add option in the NFS configuration page for using the good UID/GUID
- Install script check if the destination drive is used
- More options on the Software RAID tools (saveconfig, Force state to UP)
- FTP timeout display in minutes bug fixed
- Removing RAID volume is more clean (remove the RAID disks too)
- Can boot if there is no serial port
- "clean log" button works

FreeNAS 0.52:

This release add minors features:
- Possibility to use the disk where FreeNAS install for sharing data;
- Enabling the upgrade from Web GUI (but you can test for the next release only);
- ATA Advanced options (standby time, power management, acoustic management);

This release prepare the support of platform that don't have video and keyboard:
- The serial port is enable on booting.
- You can connect with a serial cable (null modem) for following the boot process.
- Serial port settings are: 9600 baud, 8 bits, no parity, and 1 stop bit.


FreeNAS 0.51:

New features:
- Kernel compiled for support SMP PC
- Removing the main "share" folder with CIFS
- Try to optimize the TCP samba speed

Bugs fixed:

- fix the bug "Windows station display incorrect disk size" (n° 1397508)
- Fix a problem for configuring a Nforce Ethernet card (n° 1379697)
- Fix a problem when there is ONLY a USB keyboard plug on the PC (n°1387815)
- Fix the wrong free memory display (bug n°1368478)
- Fix the Samba Server description not used (bug n°1395997)

FreeNAS 0.5:

New features:
- Software RAID with gvinum...need to be more tested (have a little problem with RAID 1 and soft reboot)
- S.M.A.R.T support (only logging actualy) with smartmontools (feature n°1368868)
- ext2 filesystem support...need to be more tested (feature n°1385882)
- Playing few notes after startup and reboot (feature n°1373316)
- Removing the small boot menu (feature n°1377592)

Bug resolved:
- Deleting file with FTP by replace pure-ftpd with vsftpd (bug n°1367965)
- Displaying hardware RAID disk (bug n°1373409)
- Call to undefined function in system_advanced (bug n°1372744)
- NFS server doesn't works (bug n°1372054)

Know bugs (WARNING!):
- When you delete a RAID volume, you must remove (with the RAID/Tools menu) all the object (especially the disks) used by this volume.
- RAID 1 volume doesn't correctly remount after a reboot, but works after a shutdown and power restart.


FreeNAS 0.4:

Changelog:
- Adding status for "disk" and "mount point"
- Correctig diag/og/settings error message
- More stable disk initialization
- More Samba options
- More information in status/disk

knowbug:
- DNS lookup and SYSLOG doesn't work
- Windows share display the wrong size disk

Limitation:
- Can't use the disk where FreeNAS is installed for sharing data

FreeNAS 0.3:

There is only minors changes:
- "install to hard drive" now works, and some new option for configuring FTP.
About the big ISO file size: it's because there is two filesystem
file, one for CDROM and the other for CF/HD.

Changes log:
- Install now works on Hard drive, should work on CF and  USB Key but no tested.
- Now you must add disk before mount it (it's for the futures
functionnaliy of disks status)
- Mount bug corrected (Call to undefined function: disk_unmout() in
/etc/inc/disks.inc on line 55 )
- More FTP configuration options

Knowbug:
- There is no check if there is error when trying to mount a disk

FreeNAS 0.2

Change log:
- Update to kernel FreeBSD 6 Stable
- Add simple disk paritionning/formating
- More options for mounting device
- More informations in status/disk
- networks CIFS shares in read/write mode

Know bugs:
- Hard disk install doesn't work
- Hotname lookup error
- Partitions display errors (Status/disk)

FreeNAS 0.1:

Initial release!
