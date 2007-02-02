#!/usr/bin/env bash

build_ntfs-3g() {
	cd /usr/ports/sysutils
	tar -zxvf $SVNDIR/misc/software/ntfs-3g/files/fusefs-ntfs.tar.gz

	cd fusefs-ntfs

	make clean
	make

	return $?
}

install_ntfs-3g() {
	cd /usr/ports/sysutils/fusefs-ntfs

	cp -pv work/ntfs-3g-*/libntfs-3g/.libs/libntfs-3g.so $FREENAS/usr/local/lib/libntfs-3g.so.0
	install -vs work/ntfs-3g-*/src/.libs/ntfs-3g $FREENAS/usr/local/bin/
	install -vs /usr/local/lib/libfuse.so.2 $FREENAS/usr/local/lib/

	return 0
}

