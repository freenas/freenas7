#!/usr/bin/env bash

# Description displayed in dialog menu (max. 60 characters)
MENUDESC="consolehm - Console based hardware monitor"
# Is dialog menu selected [ ON | OFF ]
MENUSTATUS="ON"

build_consolehm() {
	cd /usr/ports/sysutils/consolehm

	make clean
	make

	return $?
}

install_consolehm() {
	cd /usr/ports/sysutils/consolehm

	install -vs work/consolehm*/consolehm/chm $FREENAS/usr/local/bin

	return 0
}
