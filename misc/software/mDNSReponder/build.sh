#!/usr/bin/env bash

build_mDNSReponder() {
	cd /usr/ports/net/mDNSResponder

	make clean
	make

	return 0
}

install_mDNSReponder() {
	cd /usr/ports/net/mDNSResponder

  install -vs work/mDNSResponder-*/mDNSPosix/build/prod/mDNSResponderPosix $FREENAS/usr/local/sbin/

	return 0
}
