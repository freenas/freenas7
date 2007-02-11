#!/usr/bin/env bash

build_iscsi-target() {
	
	cd /usr/ports/net/iscsi-target/
	make clean
	make

	return $?
}

install_iscsi-target() {
	cd /usr/ports/net/iscsi-target/
	cp -pv work/netbsd-iscsi-*/bin/iscsi-target $FREENAS/usr/local/bin/

	return 0
}
