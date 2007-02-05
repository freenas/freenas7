#!/usr/bin/env bash

build_geli() {
	cd /usr/src/sbin/geom/class/eli

	# Patch geom eli sources.
	if [ ! -e ./geom_eli.c.orig ]; then
		patch -f ./geom_eli.c $SVNDIR/misc/software/geli/files/patch-geom_eli.c
		[ 0 != $? ] && return 1 # successful?
	fi

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
