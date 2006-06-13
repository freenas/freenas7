#!/usr/bin/env bash
# This is a script designed to automate the assembly of
# a FreeNAS box.
# Created: 2/12/2006 by Scott Zahn

# Global Variables:
VERSION=0.67

WORKINGDIR="/usr/local/freenas"
FREENAS="/usr/local/freenas/rootfs"
BOOTDIR="/usr/local/freenas/bootloader"
SVNDIR="/usr/local/freenas/svn"
TMPDIR="/tmp/freenastmp"

# Functions:
create_fs() {
	if [ -d $FREENAS ]; then
		echo
		echo "$FREENAS already exists.  Remove the directory"
		echo "before running this script."
		echo
		echo "Exiting..."
		echo
		exit
	fi

	mkdir $FREENAS
	cd $FREENAS
	mkdir boot ;
	mkdir boot/kernel ;
	mkdir bin ;
	mkdir cf ;
	mkdir ftmp ;
	mkdir conf.default ;
	mkdir dev ;
	mkdir etc ;
	mkdir etc/defaults ;
	mkdir etc/inc ;
	mkdir etc/pam.d ;
	mkdir etc/ssh ;
	mkdir lib ;
	mkdir libexec ;
	mkdir -m 0777 mnt ;
	mkdir -m 0700 root ;
	mkdir sbin ;
	mkdir usr ;
	mkdir usr/bin ;
	mkdir usr/lib ;
	mkdir usr/lib/aout ;
	mkdir usr/libexec ;
	mkdir usr/local ;
	mkdir usr/local/bin;
	mkdir usr/local/lib ;
	mkdir usr/local/sbin ;
	mkdir usr/local/www ;
	mkdir usr/sbin ;
	mkdir usr/share ;
	mkdir tmp ;
	# share/empty mandatory for VSFTPD
	mkdir usr/share/empty ;
	mkdir var ;
	# Creating symbolic links
	#ln -s var/tmp tmp
	ln -s cf/conf conf
	ln -s /var/run/htpasswd usr/local/www/.htpasswd
	ln -s /var/etc/resolv.conf etc/resolv.conf
	ln -s /var/etc/master.passwd etc/master.passwd
	ln -s /var/etc/passwd etc/passwd
	ln -s /var/etc/group etc/group
	ln -s /var/etc/pwd.db etc/pwd.db
	ln -s /var/etc/spwd.db etc/spwd.db
	ln -s /var/etc/crontab etc/crontab
	ln -s /var/etc/ssh/sshd_config etc/ssh/sshd_config
	ln -s /var/etc/ssh/ssh_host_dsa_key etc/ssh/ssh_host_dsa_key
	ln -s /var/etc/pam.d/ftp etc/pam.d/ftp
	ln -s /var/etc/pam.d/sshd etc/pam.d/sshd
	ln -s /var/etc/pam.d/login etc/pam.d/login
	ln -s /var/etc/nsswitch.conf etc/nsswitch.conf
	ln -s /libexec/ld-elf.so.1 usr/libexec/ld-elf.so.1

	return 0
}

copy_bins() {
	[ -f freenas.files ] && rm -f freenas.files
	fetch http://www.freenas.org/downloads/freenas.files

	# Add custom binaries
	if [ -f freenas.custfiles ]; then
		cat freenas.custfiles >> freenas.files
	fi

	for i in $(cat freenas.files | grep -v "^#"); do
		file=$(echo "$i" | cut -d ":" -f 1)
		cp -p /$file $FREENAS/$(echo $file | rev | cut -d "/" -f 2- | rev)
		# deal with links
		if [ $(echo "$i" | grep -c ":") -gt 0 ]; then
			for j in $(echo $i | cut -d ":" -f 2- | sed "s/:/ /g"); do
				ln $FREENAS/$file $FREENAS/$j
			done
		fi
	done
	return 0
}

prep_etc() {
	[ -f freenas-etc.tgz ] && rm -f freenas-etc.tgz
	fetch http://www.freenas.org/downloads/freenas-etc.tgz
	tar -xzf freenas-etc.tgz -C $FREENAS/
	#chmod 755 $FREENAS/etc/rc.*

	pwd_mkdb -p -d $FREENAS/etc $FREENAS/etc/master.passwd
	
	echo $VERSION > $FREENAS/etc/version
	date > $FREENAS/etc/version.buildtime

	echo $FREENAS_PLATFORM > $FREENAS/etc/platform

	cd $FREENAS/conf.default/
	fetch http://www.freenas.org/downloads/config.xml

	cd $FREENAS/usr/share/
	fetch http://www.freenas.org/downloads/zoneinfo.tgz

	return 0
}

build_kernel() {
	cd /sys/i386/conf
	if [ -f FREENAS ]; then
		rm -f FREENAS
	fi
	fetch http://freenas.org/downloads/FREENAS
	config /sys/i386/conf/FREENAS
	cd /sys/i386/compile/FREENAS
	make clean
	make depend && make
	gzip -9 kernel

	cp -p modules/usr/src/sys/modules/geom/geom_vinum/geom_vinum.ko $FREENAS/boot/kernel
	cp -p modules/usr/src/sys/modules/geom/geom_stripe/geom_stripe.ko $FREENAS/boot/kernel
	cp -p modules/usr/src/sys/modules/geom/geom_concat/geom_concat.ko $FREENAS/boot/kernel
	cp -p modules/usr/src/sys/modules/geom/geom_mirror/geom_mirror.ko $FREENAS/boot/kernel
	cp -p modules/usr/src/sys/modules/geom/geom_gpt/geom_gpt.ko $FREENAS/boot/kernel
	cp -p modules/usr/src/sys/modules/ntfs/ntfs.ko $FREENAS/boot/kernel
	cp -p modules/usr/src/sys/modules/ext2fs/ext2fs.ko $FREENAS/boot/kernel/
	
	cp -p /boot/mbr $FREENAS/boot/
	return 0
}

build_php() {
	php_tarball=$(ls php*.tar.gz | tail -n1)
	if [ -z "$php_tarball" ]; then
		echo "PHP tarball not found. Download PHP (tar.gz) and run step again."
		return 1
	else
		tar -zxf $php_tarball
		cd $(basename $php_tarball .tar.gz)
		./configure --without-mysql --without-pear --with-openssl --enable-discard-path
		make
		install -s sapi/cgi/php $FREENAS/usr/local/bin

		echo 'magic_quotes_gpc = off
magic_quotes_runtime = off
max_execution_time = 0
max_input_time = 180
register_argc_argv = off
file_uploads = on
upload_tmp_dir = /ftmp
upload_max_filesize = 32M
post_max_size = 48M
html_errors = off
include_path = ".:/etc/inc:/usr/local/www"' > $FREENAS/usr/local/lib/php.ini

	fi
	return 0
}

build_httpd() {
	httpd_tarball=$(ls mini_httpd*.tar.gz | tail -n1)
	if [ ! -z "$http_tarball" ]; then 
		rm -f $http_tarball
	fi
	fetch http://www.acme.com/software/mini_httpd/mini_httpd-1.19.tar.gz

	if [ -f mini_httpd.c.patch ]; then
		rm -f mini_httpd.c.patch
	fi
	fetch http://www.freenas.org/downloads/patchs/mini_httpd.c.patch

	tar -xzf $httpd_tarball
	patch < mini_httpd.c.patch
	cd $(basename $httpd_tarball .tar.gz)
	make
	install -s mini_httpd $FREENAS/usr/local/sbin
	return 0
}

build_msntp() {
	cd /usr/ports/net/msntp
	make
	install -s work/msntp*/msntp $FREENAS/usr/local/bin

	echo '#!/bin/sh
# write our PID to file
echo $$ > $1

# execute msntp in endless loop; restart if it
# exits (wait 1 second to avoid restarting too fast in case
# the network is not yet setup)
while true; do
	/usr/local/bin/msntp -r -P no -l $2 -x $3 $4
	sleep 1
done' > $FREENAS/usr/local/bin/runmsntp.sh

	chmod +x $FREENAS/usr/local/bin/runmsntp.sh
	return 0
}

build_ataidle() {
	cd /usr/ports/sysutils/ataidle
	make
	install -s work/ataidle*/ataidle $FREENAS/usr/local/sbin
	return 0
}

build_vsftp() {
	vsftp_tarball=$(ls vsftpd*.tar.gz | tail -n1)
	if [ -z "$vsftp_tarball" ]; then
		fetch ftp://vsftpd.beasts.org/users/cevans/vsftpd-2.0.4.tar.gz
	else
		tar -zxf $vsftp_tarball
		cd $(basename $vsftp_tarball .tar.gz)
		make
		install -s vsftpd $FREENAS/usr/local/sbin/
	fi
	return 0
}

build_samba() {
	if [ ! -f samba-latest.tar.gz ]; then
		fetch http://us2.samba.org/samba/ftp/samba-latest.tar.gz
	fi

	tar -zxf samba-latest.tar.gz
	samba_dir=$(ls -d samba-3* | tail -n1)
	cd $samba_dir/source

	./configure --with-ldap --with-ads --with-pam --with-ldapsam --without-utmp --without-winbind --disable-cups --with-acl-support --with-logfilebase=/var/log/samba --with-piddir=/var/run --with-privatedir=/var/etc/private --with-configdir=/var/etc --with-lockdir=/var/run
	make

	install -s bin/smbd $FREENAS/usr/local/sbin/
	install -s bin/nmbd $FREENAS/usr/local/sbin/
	install -s bin/smbstatus $FREENAS/usr/bin/
	install -s bin/smbcontrol $FREENAS/usr/bin/
	install -s bin/smbtree $FREENAS/usr/bin/

	mkdir -p $FREENAS/usr/local/lib/samba/vfs
	mkdir $FREENAS/usr/local/lib/samba/charset
	mkdir $FREENAS/usr/local/lib/samba/rpc
	mkdir $FREENAS/usr/local/lib/samba/pdb

	cp bin/*.so $FREENAS/usr/local/lib/samba/vfs
	mv $FREENAS/usr/local/lib/samba/vfs/CP*.so $FREENAS/usr/local/lib/samba/charset
	cp codepages/*.dat $FREENAS/usr/local/lib/samba
	cp po/*.* $FREENAS/usr/local/lib/samba
	
	return 0
}

install_nfs() {
	cp -p /usr/sbin/nfsd $FREENAS/usr/sbin
	cp -p /usr/sbin/mountd $FREENAS/usr/sbin
	cp -p /usr/sbin/rpcbind $FREENAS/usr/sbin
	return 0
}

build_netatalk() {
	cd /usr/ports/net/netatalk
	echo "This isn't implemented yet." && sleep 2
	#make
	# TODO: FINISH THIS
	return 0
}

build_rsync() {
	cd /usr/ports/net/rsync
	make
	install -s work/rsync-*/rsync $FREENAS/usr/local/bin
	return 0
}

build_smarttools() {
	cd /usr/ports/sysutils/smartmontools
	make
	install -s work/smartmontools-*/smartctl $FREENAS/usr/local/sbin
	install -s work/smartmontools-*/smartd $FREENAS/usr/local/sbin
	return 0
}

build_beep() {
	cd /usr/ports/audio/beep
	make
	install -s work/beep/beep $FREENAS/usr/local/bin
	return 0 
}

build_bootldr() {
	mkdir -p $BOOTDIR/defaults
	mkdir $BOOTDIR/kernel
	cp -p /boot/defaults/loader.conf $BOOTDIR/defaults
	cp -p /boot/loader $BOOTDIR
	cp -p /boot/boot $BOOTDIR
	cp -p /boot/mbr $BOOTDIR
	cp -p /boot/cdboot $BOOTDIR
	cp -p /boot/loader.rc $BOOTDIR
	cp -p /boot/loader.4th $BOOTDIR
	cp -p /boot/support.4th $BOOTDIR
	cp -p /boot/device.hints $BOOTDIR
	cp -p /sys/i386/compile/FREENAS/kernel.gz $BOOTDIR/kernel
	echo 'mfsroot_load="YES"
mfsroot_type="mfs_root"
mfsroot_name="/mfsroot"
autoboot_delay="0"' > $BOOTDIR/loader.conf

	return 0
}

add_libs() {
	[ -f /tmp/lib.list ] && rm -f /tmp/lib.list
	dirs=($FREENAS/bin $FREENAS/sbin $FREENAS/usr/bin $FREENAS/usr/sbin $FREENAS/usr/local/bin $FREENAS/usr/local/sbin)
	for i in ${dirs[@]}; do
		for file in $(ls $i); do
			ldd -f "%p\n" $i/$file 2> /dev/null >> /tmp/lib.list
		done
	done

	for i in $(sort -u /tmp/lib.list); do
		cp -vp $i ${FREENAS}$(echo $i | rev | cut -d '/' -f 2- | rev)
	done
	rm -f /tmp/lib.list
	return 0
}

add_web_gui(){
	if [ ! -f "freenas-gui.tgz" ]; then
		fetch http://www.freenas.org/downloads/freenas-gui.tgz
		if [ ! $? ]; then
			echo "Failed to fetch freenas-gui.tgz"
			return 1
		fi
	fi
	tar -xzf freenas-gui.tgz -C $FREENAS/usr/local
	if [ $? ]; then
		echo "Untarred GUI files successfully."
		sleep 1
	fi
	return 0
}

create_mfsroot() {
	echo "Generating the MFSROOT filesystem"
	cd $WORKINGDIR
	[ -f mfsroot.gz ] && rm -f mfsroot.gz
	[ -d svn ] && use_svn ;
	
	# Setting Version type and date
	date > $FREENAS/etc/version.buildtime
	
	# Make mfsroot to be 32M
	dd if=/dev/zero of=mfsroot bs=1M count=32
	# Configure this file as a memory disk
	mdconfig -a -t vnode -f mfsroot -u 0
	# Create Label on this disk
	bsdlabel -w md0 auto
	# format it as UFS
	newfs -b 8192 -f 1024 -o space -m 0 /dev/md0c
	# umount the /mnt directory if allready used
	umount $TMPDIR
	mount /dev/md0c $TMPDIR
	cd /mnt
	tar -cf - -C $FREENAS ./ | tar -xvpf -
	cd $WORKINGDIR
	umount $TMPDIR
	mdconfig -d -u 0
	gzip -9 mfsroot
	return 0
}

create_image() {
	echo "IMG: Generating FreeNAS IMG File (to be rawrite on CF/USB/HD)"
	[ -f image.bin ] && rm -f image.bin
	PLATFORM="generic-pc"
	echo $PLATFORM > $FREENAS/etc/platform
	IMGFILENAME="FreeNAS-$PLATFORM-$VERSION.img"
	
	echo "IMG: Generating tempory $TMPDIR folder"
	mkdir $TMPDIR
	create_mfsroot;
	
	echo "IMG: Creating a 16Mb empty destination IMG file"
	dd if=/dev/zero of=$WORKINGDIR/image.bin bs=1k count=18432
	echo "IMG: using this file as a memory disk"
	mdconfig -a -t vnode -f $WORKINGDIR/image.bin -u 0
	echo "IMG: Creating partition on this memory disk"
	fdisk -BI -b $BOOTDIR/mbr /dev/md0
	echo "IMG: Configuring FreeBSD label on this memory disk"
	bsdlabel -B -w -b $BOOTDIR/boot /dev/md0 auto
	bsdlabel md0 >/tmp/label.$$
	bsdlabel md0 |
		 egrep unused |
		 sed "s/c:/a:/" |
		 sed "s/unused/4.2BSD/" >>/tmp/label.$$
	bsdlabel -R -B md0 /tmp/label.$$
	rm -f /tmp/label.$$
	echo "IMG: Formatting this memory disk on UFS"
	newfs -b 8192 -f 1024 -o space -m 0 /dev/md0a
	echo "IMG: Mount this virtual disk on $TMPDIR"
	mount /dev/md0a $TMPDIR
	echo "IMG: Copying previously generated MFSROOT file on memory disk"
	cp $WORKINGDIR/mfsroot.gz $TMPDIR
	echo "Copying bootloader file on memory disk"
	mkdir $TMPDIR/boot
	mkdir $TMPDIR/boot/kernel $TMPDIR/boot/defaults
	mkdir $TMPDIR/conf
	cp $FREENAS/conf.default/config.xml $TMPDIR/conf
	cp $BOOTDIR/kernel/kernel.gz $TMPDIR/boot/kernel
	cp $BOOTDIR/boot $TMPDIR/boot
	cp $BOOTDIR/loader $TMPDIR/boot
	cp $BOOTDIR/loader.conf $TMPDIR/boot
	cp $BOOTDIR/loader.rc $TMPDIR/boot
	cp $BOOTDIR/loader.4th $TMPDIR/boot
	cp $BOOTDIR/support.4th $TMPDIR/boot
	cp $BOOTDIR/defaults/loader.conf $TMPDIR/boot/defaults/
	cp $BOOTDIR/device.hints $TMPDIR/boot
	echo "IMG: unmount memory disk"
	umount $TMPDIR
	echo "IMG: Deconfigure memory disk"
	mdconfig -d -u 0
	echo "IMG: Compress the IMG file"
	gzip -9 $WORKINGDIR/image.bin
	mv $WORKINGDIR/image.bin.gz $IMGFILENAME
	
	echo "Cleaning tempo file"
	[ -d $TMPDIR ] && rm -rf $TMPDIR
	[ -f $WORKINGDIR/mfsroot.gz ] && rm -f $WORKINGDIR/mfsroot.gz
	[ -f $WORKINGDIR/image.bin ] && rm -f $WORKINGDIR/image.bin

	return 0
}

create_iso () {

	echo "ISO: remove old directory and file if exist"
	[ -d $TMPDIR ] && rm -rf $TMPDIR
	[ -f $WORKINGDIR/mfsroot.gz ] && rm -f $WORKINGDIR/mfsroot.gz
	
	ISOFILENAME="FreeNAS-$VERSION.iso"
	
	echo "ISO: Generating the FreeNAS Image file:"
	create_image;
	
	#Setting the variable for ISO image:
	PLATFORM="generic-pc-cdrom"
	echo "$PLATFORM" > $FREENAS/etc/platform
	date > $FREENAS/etc/version.buildtime
	
	echo "ISO: Generating tempory $TMPDIR folder"
	mkdir $TMPDIR
	create_mfsroot;
	
	echo "ISO: Copying previously generated MFSROOT file on $TMPDIR folder"
	cp $WORKINGDIR/mfsroot.gz $TMPDIR
	
	echo "ISO: Copying bootloader file on $TMPDIR folder"
	mkdir $TMPDIR/boot
	mkdir $TMPDIR/boot/kernel $TMPDIR/boot/defaults
	cp $BOOTDIR/kernel/kernel.gz $TMPDIR/boot/kernel
	cp $BOOTDIR/cdboot $TMPDIR/boot
	cp $BOOTDIR/loader $TMPDIR/boot
	cp $BOOTDIR/loader.conf $TMPDIR/boot
	cp $BOOTDIR/loader.rc $TMPDIR/boot
	cp $BOOTDIR/loader.4th $TMPDIR/boot
	cp $BOOTDIR/support.4th $TMPDIR/boot
	cp $BOOTDIR/defaults/loader.conf $TMPDIR/boot/defaults/
	cp $BOOTDIR/device.hints $TMPDIR/boot
	
	echo "ISO: Copying IMG file on $TMPDIR folder"
	
	cp $WORKINGDIR/FreeNAS-generic-pc-$VERSION.img $TMPDIR/FreeNAS-generic-pc.gz
	
	
	echo "ISO: Generating the ISO file"
	mkisofs -b "boot/cdboot" -no-emul-boot -A "FreeNAS CD-ROM image" -c "boot/boot.catalog" -d -r -publisher "freenas.org" -p "Olivier Cochard-Labbe" -V "freenas_cd" -o "$ISOFILENAME" $TMPDIR
	
	echo "ISO: Cleaning tempo file"
	[ -d $TMPDIR ] && rm -rf $TMPDIR
	[ -f $WORKINGDIR/mfsroot.gz ] && rm -f $WORKINGDIR/mfsroot.gz
	
	return 0
}

download_rootfs() {
	mkdir $WORKINGDIR
	cd $WORKINGDIR
	echo "Deleting old archives"
	[ -f freenas-rootfs.tgz ] && rm -f freenas-rootfs.tgz
	[ -f freenas-boot.tgz ] && rm -f freenas-boot.tgz
	echo "Downloading new archives"
	fetch http://www.freenas.org/downloads/freenas-rootfs.tgz
	fetch http://www.freenas.org/downloads/freenas-boot.tgz
	echo "De-taring new archives"
	tar -xzf freenas-rootfs.tgz -C $WORKINGDIR/
	tar -xzf freenas-boot.tgz -C $WORKINGDIR/
	
	return 0

}

update_sources() {
	
	cd $WORKINGDIR
	svn co https://svn.sourceforge.net/svnroot/freenas/trunk svn
	return 0

}

use_svn() {
	
	echo "Replacing old code with SVN code"
	cp -p $SVNDIR/etc/*.* $FREENAS/etc
	cp -p $SVNDIR/etc/* $FREENAS/etc
	cp -p $SVNDIR/etc/inc/*.* $FREENAS/etc
	cp -p $SVNDIR/www/*.* $FREENAS/usr/local/www
	cp -p $SVNDIR/conf/*.* $FREENAS/conf.default
	svn co https://svn.sourceforge.net/svnroot/freenas/trunk svn
	
	return 0

}

fromscratch() {

echo -n '
Rebulding FreeNAS from Scratch
Menu:
1  - Create FreeNAS directory structure 
2  - Copy FreeBSD binaries to FreeNAS filesystem
3  - Prepare FreeNAS /etc
4  - Build FreeNAS kernel
10 - Build and install PHP
11 - Build and install mini_httpd
12 - Build and install msntp
13 - Build and install ataidle
14 - Build and install vsftp
15 - Build and install samba
16 - Install NFS
17 - Build and install Netatalk
18 - Build and install Rsync
19 - Build and install SMART tools
20 - Build and install beep
30 - Build bootloader
31 - Add necessary libraries
32 - Add web GUI

*  - Quit

> '
	read choice
	case $choice in
		1)  create_fs;;
		2)  copy_bins;;
		3)  prep_etc;;
		4)  build_kernel;;
		10) build_php;;
		11) build_httpd;;
		12) build_msntp;;
		13) build_ataidle;;
		14) build_vsftp;;
		15) build_samba;;
		16) install_nfs;;
		17) build_netatalk;;
		18) build_rsync;;
		19) build_smarttools;;
		20) build_beep;;
		30) build_bootldr;;
		31) add_libs;;
		32) add_web_gui;;
		*)  main;;
	esac
	[ $? ] && echo "Success" || echo "Failure"
	sleep 1

	return 0

}



main() {
	# Ensure we are in $WORKINGDIR
	[ ! -d "$WORKINGDIR" ] && mkdir $WORKINGDIR
	cd $WORKINGDIR

	echo -n '
Menu:
1  - Download and decompres FreeNAS root filesystem 
2  - Update the source to latest (need SVN)
10 - Create FreeNAS IMG file (rawrite to CF/USB/DD)
11 - Create FreeNAS ISO file (need cdrtool installed)
12 - Create FreeNAS ISO file without IMG image (need cdrtool installed)
20 - Build FreeNAS from scratch advanced menu
*  - Quit

> '
	read choice
	case $choice in
		1)  download_rootfs;;
		2)  update_sources;;
		10) create_image;;
		11) create_iso;;
		12) create_iso_light;;
		20) fromscratch;;
		*)  exit 0;;
	esac
	[ $? ] && echo "Success" || echo "Failure"
	sleep 1

	return 0
}

while true; do
	main
done
exit 0
