#!/usr/bin/env bash

URL_GEOMRAID5="http://home.tiscali.de/cmdr_faako/geom_raid5.tbz"

build_geom_raid5() {
	cd /usr/src

	geomraid5_tarball=$(urlbasename $URL_GEOMRAID5)

	# Adding experimental geom RAID 5 module
  if [ ! -f "$geomraid5_tarball" ]; then
    fetch $URL_GEOMRAID5
    if [ 1 == $? ]; then
      echo "==> Failed to fetch $geomraid5_tarball."
      return 1
    fi
  fi

  tar -zxvf $geomraid5_tarball

	# Make kernel module. 
  cd /usr/src/sys/modules/geom/geom_raid5/
  make depend
  [ 0 != $? ] && return 1 # successful?
  make
	[ 0 != $? ] && return 1 # successful?

	# Make application.
	cd /usr/src/sbin/geom/class/raid5
  mkdir /usr/include/geom/raid5
  cp -v /usr/src/sys/geom/raid5/g_raid5.h /usr/include/geom/raid5
  make depend
  [ 0 != $? ] && return 1 # successful?
  make

	return $?
}

install_geom_raid5() {
	cp -v /usr/src/sys/modules/geom/geom_raid5/geom_raid5.ko $FREENAS/boot/kernel

	cd /usr/src/sbin/geom/class/raid5

	# How to create application binary without this command? Any ideas?
	make install

  install -vs /sbin/graid5 $FREENAS/sbin
  install -vs geom_raid5.so $FREENAS/lib/geom

	return 0
}
