#!/usr/bin/env bash
# This is a script designed to automate the assembly of
# a FreeNAS box.
# Created: 2/12/2006 by Scott Zahn
# Modified: 11/2006 by Volker Theile (votdev@gmx.de)

# Global Variables:
WORKINGDIR="/usr/local/freenas"
FREENAS="/usr/local/freenas/rootfs"
BOOTDIR="/usr/local/freenas/bootloader"
SVNDIR="/usr/local/freenas/svn"
TMPDIR="/tmp/freenastmp"
VERSION=`cat $SVNDIR/etc/version`

# URL's:
URL_FREENASETC="http://www.freenas.org/downloads/freenas-etc.tgz"
URL_FREENASGUI="http://www.freenas.org/downloads/freenas-gui.tgz"
URL_FREENASROOTFS="http://www.freenas.org/downloads/freenas-rootfs.tgz"
URL_FREENASBOOT="http://www.freenas.org/downloads/freenas-boot.tgz"
URL_ZONEINFO="http://www.freenas.org/downloads/zoneinfo.tgz"
URL_LIGHTTPD="http://www.lighttpd.net/download/lighttpd-1.4.11.tar.gz"
URL_CLOG="http://www.freenas.org/downloads/clog-1.0.1.tar.gz"
URL_SYSLOGD="http://www.freenas.org/downloads/syslogd_clog-current.tgz"
URL_ISCSI="ftp://ftp.cs.huji.ac.il/users/danny/freebsd/iscsi-17.tar.bz2"
URL_PUREFTP="http://download.pureftpd.org/pub/pure-ftpd/releases/pure-ftpd-1.0.21.tar.gz"
URL_SAMBA="http://us2.samba.org/samba/ftp/samba-latest.tar.gz"
URL_NETATALK="http://ovh.dl.sourceforge.net/sourceforge/netatalk/netatalk-2.0.3.tar.gz"
URL_RSYNC="http://samba.anu.edu.au/ftp/rsync/rsync-2.6.8.tar.gz"

# Functions:

# Return filename of URL
urlbasename() {
  echo $1 | awk '{n=split($0,v,"/");print v[n]}'
}

# Copying required binaries
copy_bins() {
	[ -f freenas.files ] && rm -f freenas.files
	cp $SVNDIR/misc/freenas.files $WORKINGDIR

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
	
	#Setting right permission to su binary
	chmod 4755 $FREENAS/usr/bin/su
	
	return 0
}

# Preparing /etc
prep_etc() {
	[ -f "freenas-etc.tgz" ] && rm -f freenas-etc.tgz
	fetch $URL_FREENASETC
	if [ ! $? ]; then
    echo "Failed to fetch freenas-etc.tgz."
    return 1
  fi

	# Installing the etc archive and PHP configuration scripts
	tar -xzf freenas-etc.tgz -C $FREENAS/

  # Additional Notes
	pwd_mkdb -p -d $FREENAS/etc $FREENAS/etc/master.passwd

  # Configuring platform variable
	echo $VERSION > $FREENAS/etc/version
	date > $FREENAS/etc/version.buildtime

	echo $FREENAS_PLATFORM > $FREENAS/etc/platform

  # Config file: config.xml
	cd $FREENAS/conf.default/
	cp $SVNDIR/conf/config.xml .

  # Zone Info
	cd $FREENAS/usr/share/
	fetch $URL_ZONEINFO
	if [ ! $? ]; then
    echo "Failed to fetch $(urlbasename $URL_ZONEINFO)."
    return 1
  fi

	return 0
}

# Building the kernel
build_kernel() {
	cd /sys/i386/conf
	if [ -f FREENAS ]; then
		rm -f FREENAS
	fi
	cp $SVNDIR/misc/kernel-config/FREENAS .
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

# Building the software package:
# PHP 5
build_php() {
	php_tarball=$(ls php*.tar.gz | tail -n1)
	if [ -z "$php_tarball" ]; then
		echo "PHP tarball not found. Download PHP (tar.gz) and run step again."
		return 1
	else
		tar -zxf $php_tarball
		cd $(basename $php_tarball .tar.gz)
		./configure --without-mysql --without-pear --with-openssl --without-sqlite --with-pcre-regex --enable-discard-path --enable-force-cgi-redirect --enable-embed=shared
		make
		install -s sapi/cgi/php $FREENAS/usr/local/bin

		echo 'magic_quotes_gpc = off
magic_quotes_runtime = off
max_execution_time = 0
max_input_time = 180
register_argc_argv = off
file_uploads = on
upload_tmp_dir = /ftmp
upload_max_filesize = 128M
post_max_size = 256M
html_errors = off
include_path = ".:/etc/inc:/usr/local/www"' > $FREENAS/usr/local/lib/php.ini

	fi
	return 0
}

# Lighttpd
build_lighttpd() {
  lighttpd_tarball=$(urlbasename $URL_LIGHTTPD)

  if [ ! -f "$lighttpd_tarball" ]; then
		fetch $URL_LIGHTTPD
		if [ ! $? ]; then
      echo "Failed to fetch $lighttpd_tarball."
      return 1
    fi
	fi

	tar -zxvf $lighttpd_tarball

	cd $(basename $lighttpd_tarball .tar.gz)

	./configure --sysconfdir=/var/etc/ --enable-lfs --without-mysql --without-ldap --with-openssl --without-lua --with-bzip2 --without-pcre
	make
	install -s src/lighttpd $FREENAS/usr/local/sbin

	mkdir $FREENAS/usr/local/bin/lighttpd

  cp src/.libs/mod_indexfile.so $FREENAS/usr/local/bin/lighttpd
  cp src/.libs/mod_access.so $FREENAS/usr/local/bin/lighttpd
  cp src/.libs/mod_accesslog.so $FREENAS/usr/local/bin/lighttpd
  cp src/.libs/mod_dirlisting.so $FREENAS/usr/local/bin/lighttpd
  cp src/.libs/mod_staticfile.so $FREENAS/usr/local/bin/lighttpd
  cp src/.libs/mod_cgi.so $FREENAS/usr/local/bin/lighttpd
  cp src/.libs/mod_auth.so $FREENAS/usr/local/bin/lighttpd
  cp src/.libs/mod_webdav.so $FREENAS/usr/local/bin/lighttpd

	return 0
}

# clog
build_clog() {
  cd /usr/src/usr.bin/

  clog_tarball=$(urlbasename $URL_CLOG)
  syslogd_tarball=$(urlbasename $URL_SYSLOGD)

  if [ ! -f "$clog_tarball" ]; then
		fetch $URL_CLOG
		if [ ! $? ]; then
      echo "Failed to fetch $clog_tarball."
      return 1
    fi
	fi
	if [ ! -f "$syslogd_tarball" ]; then
    fetch $URL_SYSLOGD
    if [ ! $? ]; then
      echo "Failed to fetch $syslogd_tarball."
      return 1
    fi
	fi

  tar zxvf $clog_tarball
  tar zxvf $syslogd_tarball

  cd syslogd
  make
  install -s syslogd $FREENAS/usr/sbin/

  cd ../clog
  gcc clog.c -o clog
  install -s clog $FREENAS/usr/sbin/

  return 0
}

# MSNTP
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

# ataidle
build_ataidle() {
	cd /usr/ports/sysutils/ataidle
	make
	install -s work/ataidle*/ataidle $FREENAS/usr/local/sbin
	return 0
}

# iscsi initiator
build_iscsi() {
  iscsi_tarball=$(urlbasename $URL_ISCSI)
  
  if [ ! -f "$iscsi_tarball" ]; then
		fetch $URL_ISCSI
		if [ ! $? ]; then
      echo "Failed to fetch $iscsi_tarball."
      return 1
    fi
	fi

	tar zxvf $iscsi_tarball
	cd sys
  ln -s /sys/kern .
  ln -s /sys/tools .
  cd modules/iscsi_initiator
  make clean
  ln -s ../.. @
  make
  cp iscsi_initiator.ko $FREENAS/boot/kernel/
  cd ../../../iscontrol/
  make
  install -s iscontrol $FREENAS/usr/local/sbin/

	return 0
}

# Pure-FTPd
build_pureftpd() {
  cd /root

	pureftpd_tarball=$(urlbasename $URL_PUREFTP)

	if [ ! -f "$pureftpd_tarball" ]; then
		fetch $URL_PUREFTP
		if [ ! $? ]; then
      echo "Failed to fetch $pureftpd_tarball."
      return 1
    fi
	fi

  tar zxvf $pureftpd_tarball
  cd $(basename $pureftpd_tarball .tar.gz)
  ./configure --with-rfc2640 --with-largefile --with-pam
  make
  install -s src/pure-ftpd $FREENAS/usr/local/sbin/

	return 0
}

# Samba (CIFS server)
build_samba() {
  samba_tarball=$(urlbasename $URL_SAMBA)

	if [ ! -f "$samba_tarball" ]; then
		fetch $URL_SAMBA
		if [ ! $? ]; then
      echo "Failed to fetch $samba_tarball."
      return 1
    fi
	fi

	tar -zxf $samba_tarball
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

# NFS
install_nfs() {
	cp -p /usr/sbin/nfsd $FREENAS/usr/sbin
	cp -p /usr/sbin/mountd $FREENAS/usr/sbin
	cp -p /usr/sbin/rpcbind $FREENAS/usr/sbin
	return 0
}

# Netatalk
build_netatalk() {
  netatalk_tarball=$(urlbasename $URL_NETATALK)

	cd /usr/ports/databases/db42
  make install

	if [ ! -f "$netatalk_tarball" ]; then
		fetch $URL_NETATALK
		if [ ! $? ]; then
      echo "Failed to fetch $netatalk_tarball."
      return 1
    fi
	fi

  tar zxvf $netatalk_tarball
  cd $(basename $netatalk_tarball .tar.gz)
  ./configure --bindir=/usr/local/bin --sbindir=/usr/local/sbin --sysconfdir=/var/etc --localstatedir=/var --enable-largefile --disable-tcp-wrappers --disable-cups --with-pam --with-uams-path=/etc/uams/
  install -s etc/afpd/afpd $FREENAS/usr/local/sbin/
  mkdir $FREENAS/etc/uams
  cp etc/uams/.libs/uams_passwd.so $FREENAS/etc/uams
  cp etc/uams/.libs/uams_dhx_passwd.so $FREENAS/etc/uams
  cp etc/uams/.libs/uams_guest.so $FREENAS/etc/uams
  cp etc/uams/.libs/uams_randnum.so $FREENAS/etc/uams
  cd $FREENAS/etc/uams
  ln -s uams_passwd.so uams_clrtxt.so
  ln -s uams_dhx_passwd.so uams_dhx.so
  cd $FREENAS/usr/local/lib/
  cp /usr/local/lib/libdb-4.2.so.2 .
  cd $FREENAS/usr/lib/
  cp /usr/lib/librpcsvc.so.3 .

	return 0
}

# RSYNC
build_rsync() {
  rsync_tarball=$(urlbasename $URL_RSYNC)

	if [ ! -f "$rsync_tarball" ]; then
		fetch $URL_RSYNC
		if [ ! $? ]; then
      echo "Failed to fetch $rsync_tarball."
      return 1
    fi
	fi
  
  tar zxvf $rsync_tarball
  cd $(basename $rsync_tarball .tar.gz)
  ./configure --with-rsyncd-conf=/var/etc
  make
  install -s rsync $FREENAS/usr/local/bin/

	return 0
}

# Unison
build_unison() {
  cd /usr/ports/net/unison/
  make
  cp work/unison-*/unison $FREENAS/usr/local/bin/
	return 0
}

# scponly
build_scponly() {
  echo "Edit the Makefile, and add these lines:"
  echo "WITH_SCPONLY_RSYNC=YES"
  echo "WITH_SCPONLY_SCP=YES"
  echo "WITH_SCPONLY_WINSCP=YES"
  echo "WITH_SCPONLY_UNISON=YES"

  cd /usr/ports/shells/scponly/  
  make
  install -s work/scponly-*/scponly $FREENAS/usr/local/bin/
	return 0
}

# e2fsck
build_e2fsck() {
  cd /usr/ports/sysutils/e2fsprogs/
  make
  install -s work/e2fsprogs-*/e2fsck/e2fsck $FREENAS/usr/local/sbin/
	return 0
}

# SMART tools
build_smarttools() {
	cd /usr/ports/sysutils/smartmontools
	make
	install -s work/smartmontools-*/smartctl $FREENAS/usr/local/sbin
	install -s work/smartmontools-*/smartd $FREENAS/usr/local/sbin
	return 0
}

# aaccli
build_aaccli() {
  cd /usr/ports/sysutils/aaccli/
  make
  tar zxvf work/aaccli-1.0_0.tgz
  cp work/bin/aaccli $FREENAS/usr/local/bin/
	return 0
}

# beep
build_beep() {
	cd /usr/ports/audio/beep
	make
	install -s work/beep/beep $FREENAS/usr/local/bin
	return 0 
}

# mDNSReponder (Apple bonjour)
build_mDNSReponder() {
  cd /usr/ports/net/mDNSResponder
  make
  install -s work/mDNSResponder-*/mDNSPosix/build/prod/mDNSResponderPosix $FREENAS/usr/local/sbin/
	return 0
}

# Build all software packages
build_softpkg() {
  build_php;
  build_lighttpd;
  build_clog;
  build_msntp;
  build_ataidle;
  build_iscsi;
  build_pureftpd;
  build_samba;
  install_nfs;
  build_netatalk;
  build_rsync;
  build_unison;
  build_scponly;
  build_e2fsck;
  build_smarttools;
  build_aaccli;
  build_beep;
  build_mDNSReponder;
	return 0
}

# Adding the libraries
add_libs() {
  # Identify required libs.
	[ -f /tmp/lib.list ] && rm -f /tmp/lib.list
	dirs=($FREENAS/bin $FREENAS/sbin $FREENAS/usr/bin $FREENAS/usr/sbin $FREENAS/usr/local/bin $FREENAS/usr/local/sbin)
	for i in ${dirs[@]}; do
		for file in $(ls $i); do
			ldd -f "%p\n" $i/$file 2> /dev/null >> /tmp/lib.list
		done
	done

  # Copy identified libs. 
	for i in $(sort -u /tmp/lib.list); do
		cp -vp $i ${FREENAS}$(echo $i | rev | cut -d '/' -f 2- | rev)
	done
	rm -f /tmp/lib.list

	# Adding the PAM library.
  cp -p /usr/lib/pam_*.so.3 $FREENAS/usr/lib

  # The LDAP PAM are not bulding by default.
  cd /usr/ports/security/pam_ldap/
  make install
  cp -p /usr/local/lib/pam_ldap.so $FREENAS/usr/local/lib

  # Don't forget to copy this mandatory library.
  cp /libexec/ld-elf.so.1 $FREENAS/libexec

  # GEOM tools.
  mkdir $FREENAS/lib/geom
  cp /lib/geom/geom
  cp /lib/geom

	return 0
}

# Adding Web GUI
add_web_gui(){
	if [ ! -f "freenas-gui.tgz" ]; then
		fetch $URL_FREENASGUI
		if [ ! $? ]; then
			echo "Failed to fetch freenas-gui.tgz."
			return 1
		fi
	fi
	tar -zxvf freenas-gui.tgz -C $FREENAS/usr/local
	if [ $? ]; then
		echo "Untarred GUI files successfully."
		sleep 1
	fi
	return 0
}

# Creating msfroot
create_mfsroot() {
	echo "Generating the MFSROOT filesystem"
	cd $WORKINGDIR
	[ -f $WORKINGDIR/mfsroot.gz ] && rm -f $WORKINGDIR/mfsroot.gz
	[ -d $WORKINGDIR/svn ] && use_svn ;

	# Setting Version type and date
	date > $FREENAS/etc/version.buildtime
	
	# Make mfsroot to be 42M
	dd if=/dev/zero of=$WORKINGDIR/mfsroot bs=1M count=42
	# Configure this file as a memory disk
	mdconfig -a -t vnode -f $WORKINGDIR/mfsroot -u 0
	# Create Label on this disk
	bsdlabel -w md0 auto
	# format it as UFS
	newfs -b 8192 -f 1024 -o space -m 0 /dev/md0c
	# umount the /mnt directory if allready used
	umount $TMPDIR
	mount /dev/md0c $TMPDIR
	cd $TMPDIR
	tar -cf - -C $FREENAS ./ | tar -xvpf -
	cd $WORKINGDIR
	umount $TMPDIR
	mdconfig -d -u 0
	gzip -9 $WORKINGDIR/mfsroot
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
	
	echo "IMG: Creating a 21Mb empty destination IMG file"
	dd if=/dev/zero of=$WORKINGDIR/image.bin bs=1M count=21
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
	
	#Special for enabling serial port if no keyboard
	#cp $BOOTDIR/boot.config $TMPDIR/
	
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
	
	if [ ! $LIGHT_ISO ]; then
		echo "ISO: Generating the FreeNAS Image file:"
		create_image;
	fi
	
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
	
	#Special test for enabling serial port if no keyboard
	#Removed because meet some problem with some hardware (no keyboard detected)
	#cp $BOOTDIR/boot.config $TMPDIR/
	
	if [ ! $LIGHT_ISO ]; then
		echo "ISO: Copying IMG file on $TMPDIR folder"
		cp $WORKINGDIR/FreeNAS-generic-pc-$VERSION.img $TMPDIR/FreeNAS-generic-pc.gz
	fi

	echo "ISO: Generating the ISO file"
	mkisofs -b "boot/cdboot" -no-emul-boot -A "FreeNAS CD-ROM image" -c "boot/boot.catalog" -d -r -publisher "freenas.org" -p "Olivier Cochard-Labbe" -V "freenas_cd" -o "$ISOFILENAME" $TMPDIR
	
	echo "ISO: Cleaning tempo file"
	[ -d $TMPDIR ] && rm -rf $TMPDIR
	[ -f $WORKINGDIR/mfsroot.gz ] && rm -f $WORKINGDIR/mfsroot.gz
	
	return 0
}

create_iso_light() {
	LIGHT_ISO=1
	create_iso;
	return 0
}

download_rootfs() {
  # Ensure we are in $WORKINGDIR
	[ ! -d "$WORKINGDIR" ] && mkdir $WORKINGDIR
	cd $WORKINGDIR
	
	update=y
	if [ -e freenas-rootfs.tgz -a -e freenas-boot.tgz ]; then
    echo -n "Update existing files [y/n]?"
    read update
	fi

  if [ $update = 'y' ]; then
    echo "Deleting old archives"
    [ -f "freenas-rootfs.tgz" ] && rm -f freenas-rootfs.tgz
    [ -f "freenas-boot.tgz" ] && rm -f freenas-boot.tgz
    
    echo "Downloading new archives"
    fetch $URL_FREENASROOTFS
    if [ ! $? ]; then
      echo "Failed to fetch freenas-rootfs.tgz."
      return 1
    fi
    fetch $URL_FREENASBOOT
    if [ ! $? ]; then
      echo "Failed to fetch freenas-boot.tgz."
      return 1
    fi
	fi

	echo "De-taring archives"
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
	cp -p $SVNDIR/etc/inc/*.* $FREENAS/etc/inc
	cp -p $SVNDIR/etc/defaults/*.* $FREENAS/etc/defaults
	cp -p $SVNDIR/www/*.* $FREENAS/usr/local/www
	cp -p $SVNDIR/www/syntaxhighlighter/*.* $FREENAS/usr/local/www/syntaxhighlighter
	cp -p $SVNDIR/conf/*.* $FREENAS/conf.default
		
	return 0
}

fromscratch() {
echo -n '
Rebulding FreeNAS from Scratch
Menu:
1 - Create directory structure 
2 - Copy required binaries to FreeNAS filesystem
3 - Prepare /etc
4 - Build kernel
5 - Software package
6 - Build bootloader
7 - Add necessary libraries
8 - Add web GUI
9 - All
* - Quit
> '
	read choice
	case $choice in
		1) $SVNDIR/misc/freenas-create-dirs.sh $FREENAS;;
		2) copy_bins;;
		3) prep_etc;;
		4) build_kernel;;
		5) fromscratch_softpkg;;
		6) $SVNDIR/misc/freenas-create-bootdir.sh $BOOTDIR;;
		7) add_libs;;
		8) add_web_gui;;
		9) $SVNDIR/misc/freenas-create-dirs.sh $FREENAS;
       copy_bins;
       prep_etc;
       build_kernel;
       build_softpkg;
       $SVNDIR/misc/freenas-create-bootdir.sh $BOOTDIR;
       add_libs;
       add_web_gui;;
		*) main;;
	esac
	[ $? ] && echo "Success" || echo "Failure"
	sleep 1

	return 0
}

fromscratch_softpkg() {
echo -n '
Software package
Menu:
1  - Build and install PHP
2  - Build and install lighttpd
3  - Build and install clog
4  - Build and install msntp
5  - Build and install ataidle
6  - Build and install iSCSI target
7  - Build and install Pure-FTPd
8  - Build and install samba
9  - Install NFS
10 - Build and install Netatalk
11 - Build and install Rsync
12 - Build and install Unison
13 - Build and install scponly
14 - Build and install e2fsck
15 - Build and install SMART tools
16 - Build and install aaccli
17 - Build and install beep
18 - Build and install mDNSReponder
19 - Build all
*  - Quit
> '
	read choice
	case $choice in
		1) build_php;;
		2) build_lighttpd;;
		3) build_clog;;
		4) build_msntp;;
		5) build_ataidle;;
		6) build_iscsi;;
		7) build_pureftpd;;
		8) build_samba;;
		9) install_nfs;;
		10) build_netatalk;;
		11) build_rsync;;
		12) build_unison;;
		13) build_scponly;;
		14) build_e2fsck;;
		15) build_smarttools;;
		16) build_aaccli;;
		17) build_beep;;
		18) build_mDNSReponder;;
		19) build_softpkg;;
		*)  fromscratch;;
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
Welcome to the FreeNAS build environment.
Menu:
1  - Download and decompress FreeNAS root filesystem 
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
