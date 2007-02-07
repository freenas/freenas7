#!/usr/bin/env bash

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
