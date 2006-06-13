#!/usr/bin/env bash
# This is a script designed to automate the assembly of
# a FreeNAS box.
# Created: 2/12/2006 by Scott Zahn

# Global Variables:
FREENAS_VERSION=0.67
FREENAS_PLATFORM_CD="generic-pc-cdrom"
FREENAS_PLATFORM="generic-pc"

WORKINGDIR="/usr/local/freenas"
FREENAS="/usr/local/freenas/rootfs"
BOOTDIR="/usr/local/freenas/bootloader"

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
	
	echo $FREENAS_VERSION > $FREENAS/etc/version
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
	cd /usr/local/freenas
	[ -f mfsroot.gz ] && rm -f mfsroot.gz
	
	# Setting Version type and date
	date > $FREENAS/etc/version.buildtime
	
	# Make mfsroot to be 32M
	dd if=/dev/zero of=mfsroot bs=1M count=32
	# Configure this file as a virtual disk
	mdconfig -a -t vnode -f mfsroot -u 0
	# Create Label on this disk
	bsdlabel -w md0 auto
	# format it as UFS
	newfs -b 8192 -f 1024 -o space -m 0 /dev/md0c
	mount /dev/md0c /mnt
	cd /mnt
	tar -cf - -C $FREENAS ./ | tar -xvpf -
	cd $WORKINGDIR
	umount /mnt
	mdconfig -d -u 0
	gzip -9 mfsroot
	return 0
}

create_image() {
	[ -f image.bin ] && rm -f image.bin
	echo "Generating MFSROOT"
	set FREENAS_PLATFORM="generic-pc"
	create_mfsroot;;
	echo "Creating a 16M disk image"
	dd if=/dev/zero of=image.bin bs=1M count=16
	mdconfig -a -t vnode -f image.bin -u 0
	bsdlabel -w md0
#	fdisk -BI -b $BOOTDIR/mbr md0 # apparently, this doesn't work
	bsdlabel -w -B -b $BOOTDIR/mbr md0 auto
	bsdlabel md0 > /tmp/label.freenas
	bsdlabel md0 | grep -E "unused" | sed -e "s/c:/a:/" -e "s/unused/4.2BSD/" >> /tmp/label.freenas
	bsdlabel -R -B md0 /tmp/label.freenas
	rm /tmp/label.freenas

	echo "Creating filesystem"
	newfs -b 8192 -f 1024 -o space -m 0 /dev/md0a
	mount /dev/md0a /mnt
	mkdir -p /mnt/boot/kernel
	mkdir /mnt/boot/defaults
	
	echo "Copying boot and mfsroot files"
	cp -p $BOOTDIR/kernel/kernel.gz /mnt/boot/kernel
	cp mfsroot.gz /mnt
	cp -p $BOOTDIR/boot /mnt/boot
	cp -p $BOOTDIR/loader /mnt/boot
	cp -p $BOOTDIR/loader.conf /mnt/boot
	cp -p $BOOTDIR/loader.rc /mnt/boot
	cp -p $BOOTDIR/device.hints /mnt/boot/
	cp -p $BOOTDIR/support.4th /mnt/boot/
	cp -p $BOOTDIR/loader.4th /mnt/boot/
	cp -p $BOOTDIR/defaults/loader.conf /mnt/boot/defaults/
	
	echo "Copying default config file"
	mkdir /mnt/conf
	cp -p $FREENAS/conf.default/config.xml /mnt/conf

	umount /mnt
	mdconfig -d -u 0
#	gzip -9 image.bin
	
	return 0
}


main() {
	# Ensure we are in $WORKINGDIR
	[ ! -d "$WORKINGDIR" ] && mkdir $WORKINGDIR
	cd $WORKINGDIR

	echo -n '
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
41 - Create disk image
42 - Create ISO file
43 - Create light ISO (without disk image)
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
		41) create_image;;
		42) Create ISO file;;
		43) Create light ISO (without disk image);;
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
