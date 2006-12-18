#!/usr/bin/env bash

build_smarttools() {
	cd /usr/ports/sysutils/smartmontools

	make clean
	make

	return 0
}

install_smarttools() {
	cd /usr/ports/sysutils/smartmontools

	install -vs work/smartmontools-*/smartctl $FREENAS/usr/local/sbin
	install -vs work/smartmontools-*/smartd $FREENAS/usr/local/sbin

	return 0
}
