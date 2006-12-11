#!/usr/bin/env bash

build_aaccli() {
	cd /usr/ports/sysutils/aaccli/

	make clean
	make

	return 0
}

install_aaccli() {
	cd /usr/ports/sysutils/aaccli/

 	tar zxvf work/aaccli-1.0_0.tgz
 	install -vs bin/aaccli $FREENAS/usr/local/bin

	return 0
}
