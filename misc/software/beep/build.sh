#!/usr/bin/env bash

build_beep() {
  cd /usr/ports/audio/beep

	make clean
	make

	return 0
}

install_beep() {
	cd /usr/ports/audio/beep

	install -vs work/beep/beep $FREENAS/usr/local/bin

	return 0
}
