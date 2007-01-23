#!/usr/bin/env bash

build_ushare() {
	cd /usr/ports/net
  tar -zxvf $SVNDIR/misc/software/ushare/files/ushare.tar.gz

  cd ushare

  make clean
  make

  return $?
}

install_ushare() {
  cd /usr/ports/net/ushare

  install -vs work/ushare-*/src/ushare $FREENAS/usr/local/bin

  return 0
}
