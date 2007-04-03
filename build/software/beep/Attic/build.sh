#!/usr/bin/env bash

# Description displayed in dialog menu (max. 60 characters)
MENUDESC="beep - Beeps a certain duration and pitch out of the speaker"
# Is dialog menu selected [ ON | OFF ]
MENUSTATUS="ON"

build_beep() {
  cd /usr/ports/audio/beep

	make clean
	make

	return $?
}

install_beep() {
	cd /usr/ports/audio/beep

	install -vs work/beep/beep $FREENAS/usr/local/bin

	return 0
}
