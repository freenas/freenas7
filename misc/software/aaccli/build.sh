#!/usr/bin/env bash

# Description displayed in dialog menu (max. 60 characters)
MENUDESC="aaccli - Adaptec SCSI RAID administration tool"
# Is dialog menu selected [ ON | OFF ]
MENUSTATUS="ON"

build_aaccli() {
	cd /usr/ports/sysutils/aaccli/

	make clean
	make

	return $?
}

install_aaccli() {
	cd /usr/ports/sysutils/aaccli/

 	tar zxvf work/aaccli-1.0_0.tgz
 	install -vs bin/aaccli $FREENAS/usr/local/bin

	return 0
}
