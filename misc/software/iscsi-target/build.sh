#!/usr/bin/env bash

# Description displayed in dialog menu (max. 60 characters)
MENUDESC="iscsi-target - Implementation of userland ISCSI target"
# Is dialog menu selected [ ON | OFF ]
MENUSTATUS="ON"

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
