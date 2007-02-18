#!/usr/bin/env bash

# Description displayed in dialog menu (max. 60 characters)
MENUDESC="mDNSResponder - Apple's mDNSResponder"
# Is dialog menu selected [ ON | OFF ]
MENUSTATUS="ON"

build_mDNSReponder() {
	cd /usr/ports/net/mDNSResponder

	make clean
	make

	return $?
}

install_mDNSReponder() {
	cd /usr/ports/net/mDNSResponder

  install -vs work/mDNSResponder-*/mDNSPosix/build/prod/mDNSResponderPosix $FREENAS/usr/local/sbin

	return 0
}
