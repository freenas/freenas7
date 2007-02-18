#!/usr/bin/env bash

# Description displayed in dialog menu (max. 60 characters)
MENUDESC="GELI - GEOM Eli filesystem encryption"
# Is dialog menu selected [ ON | OFF ]
MENUSTATUS="ON"

build_geom_eli() {
	cd /usr/src/sbin/geom/class/eli

	# Patch geom eli sources.
	if [ ! -e ./geom_eli.c.orig ]; then
		patch -f ./geom_eli.c $SVNDIR/misc/software/geom_eli/files/patch-geom_eli.c
		[ 0 != $? ] && return 1 # successful?
	fi

	make clean
	make

	return $?
}

install_geom_eli() {
	cd /usr/src/sbin/geom/class/eli

	# How to create binary without this command? Any ideas?
	make install

	install -vs /sbin/geli $FREENAS/sbin
	install -vs /lib/geom/geom_eli.so $FREENAS/lib/geom

	return 0
}
