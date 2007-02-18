#!/usr/bin/env bash

# Description displayed in dialog menu (max. 60 characters)
MENUDESC="ushare - A lightweight UPnP (TM) A/V Media Server"
# Is dialog menu selected [ ON | OFF ]
MENUSTATUS="ON"

build_ushare() {
	cd /usr/ports/net/ushare

  make clean
  make

  return $?
}

install_ushare() {
  cd /usr/ports/net/ushare

  install -vs work/ushare-*/src/ushare $FREENAS/usr/local/bin

  return 0
}
