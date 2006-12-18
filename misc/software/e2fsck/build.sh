#!/usr/bin/env bash

build_e2fsck() {
	cd /usr/ports/sysutils/e2fsprogs/

	make clean
	make

	return 0
}

install_e2fsck() {
	cd /usr/ports/sysutils/e2fsprogs

	install -vs work/e2fsprogs-*/e2fsck/e2fsck $FREENAS/usr/local/sbin/

	return 0
}
