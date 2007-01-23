#!/usr/bin/env bash

build_unison() {
	cd /usr/ports/net/unison/

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
