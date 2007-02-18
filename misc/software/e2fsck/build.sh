#!/usr/bin/env bash

# Description displayed in dialog menu (max. 60 characters)
MENUDESC="e2fsprogs - Utilities to manipulate ext2/ext3 filesystems"
# Is dialog menu selected [ ON | OFF ]
MENUSTATUS="ON"

build_e2fsck() {
	cd /usr/ports/sysutils/e2fsprogs

	make clean
	make

	return $?
}

install_e2fsck() {
	cd /usr/ports/sysutils/e2fsprogs

	install -vs work/e2fsprogs-*/e2fsck/e2fsck $FREENAS/usr/local/sbin

	return 0
}
