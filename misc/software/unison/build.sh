#!/usr/bin/env bash

# Description displayed in dialog menu (max. 60 characters)
MENUDESC="unison - A user-level file synchronization tool"
# Is dialog menu selected [ ON | OFF ]
MENUSTATUS="ON"

build_unison() {
	cd /usr/ports/net/unison

	# Copy ocaml options file
	mkdir -pv /var/db/ports/ocaml
	cp -pv $SVNDIR/misc/software/unison/files/ocaml/options /var/db/ports/ocaml

	make clean
	make

	return $?
}

install_unison() {
	cd /usr/ports/net/unison/

	install -vs work/unison-*/unison $FREENAS/usr/local/bin

	return 0
}
