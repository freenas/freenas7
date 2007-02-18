#!/usr/bin/env bash

# Description displayed in dialog menu (max. 60 characters)
MENUDESC="ataidle - Utility to set spindown timeout for ATA drives"
# Is dialog menu selected [ ON | OFF ]
MENUSTATUS="ON"

build_ataidle() {
	cd /usr/ports/sysutils/ataidle

	make clean
	make

	return $?
}

install_ataidle() {
	cd /usr/ports/sysutils/ataidle

	install -vs work/ataidle*/ataidle $FREENAS/usr/local/sbin

	return 0
}
