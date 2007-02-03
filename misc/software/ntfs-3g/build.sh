#!/usr/bin/env bash

build_ntfs-3g() {

	cd /usr/ports/systutils/fusefs-kmod
	make clean
	make

    cd /usr/ports/sysutils/fusefs-ntfs

	make clean
	make

	return $?
}

install_ntfs-3g() {
	
	cd /usr/ports/systutils/fusefs-kmod
	cp -pv work/fuse4bsd-*/mount_fusefs/mount_fusefs $FREENAS/usr/local/sbin/
	cp -pv work/fuse4bsd-*/fuse_module/fuse.ko $FREENAS/boot/kernel
	
	cd /usr/ports/sysutils/fusefs-ntfs

	cp -pv work/ntfs-3g-*/libntfs-3g/.libs/libntfs-3g.so $FREENAS/usr/local/lib/libntfs-3g.so.0
	install -vs work/ntfs-3g-*/src/.libs/ntfs-3g $FREENAS/usr/local/bin/
	install -vs /usr/local/lib/libfuse.so.2 $FREENAS/usr/local/lib/

	return 0
}

