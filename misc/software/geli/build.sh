#!/usr/bin/env bash

build_geli() {
	cd /usr/src/sbin/geom/class/eli

	patch ./geom_eli.c $SVNDIR/misc/software/geli/files/patch-geom_eli.c
	[ 0 != $? ] && return 1 # successful?

	make clean
	make

	return $?
}

install_geli() {
	cd /usr/src/sbin/geom/class/eli

	make install # How to create binary without this command?

	install -vs /sbin/geli $FREENAS/sbin
	install -vs /lib/geom/geom_eli.so $FREENAS/lib/geom

	return 0
}
