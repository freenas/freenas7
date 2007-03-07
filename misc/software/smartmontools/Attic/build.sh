#!/usr/bin/env bash

# Description displayed in dialog menu (max. 60 characters)
MENUDESC="smartmontools - S.M.A.R.T. disk monitoring tools"
# Is dialog menu selected [ ON | OFF ]
MENUSTATUS="ON"

build_smarttools() {
	cd /usr/ports/sysutils/smartmontools

	make clean
	make

	return $?
}

install_smarttools() {
	cd /usr/ports/sysutils/smartmontools

	install -vs work/smartmontools-*/smartctl $FREENAS/usr/local/sbin
	install -vs work/smartmontools-*/smartd $FREENAS/usr/local/sbin

	return 0
}
