#!/usr/bin/env bash

# Description displayed in dialog menu (max. 60 characters)
MENUDESC="ntfs-3g - Mount NTFS partitions and disk images"
# Is dialog menu selected [ ON | OFF ]
MENUSTATUS="ON"

build_ntfs-3g() {
	# Copy options file
	mkdir -pv /var/db/ports/fusefs
	cp -pv $SVNDIR/misc/software/ntfs-3g/files/options /var/db/ports/fusefs

	cd /usr/ports/sysutils/fusefs-kmod
	make clean
	make
	[ 0 != $? ] && return 1 # successful?

  cd /usr/ports/sysutils/fusefs-ntfs
	make clean
	make

	return $?
}

install_ntfs-3g() {
	cd /usr/ports/sysutils/fusefs-kmod
	cp -pv work/fuse4bsd-*/mount_fusefs/mount_fusefs $FREENAS/usr/local/sbin/
	cp -pv work/fuse4bsd-*/fuse_module/fuse.ko $FREENAS/boot/kernel

	cd /usr/ports/sysutils/fusefs-ntfs
	cp -pv work/ntfs-3g-*/libntfs-3g/.libs/libntfs-3g.so $FREENAS/usr/local/lib/libntfs-3g.so.0
	install -vs work/ntfs-3g-*/src/.libs/ntfs-3g $FREENAS/usr/local/bin/
	install -vs /usr/local/lib/libfuse.so.2 $FREENAS/usr/local/lib/

	return 0
}
