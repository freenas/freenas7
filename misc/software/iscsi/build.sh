#!/usr/bin/env bash

URL_ISCSI="ftp://ftp.cs.huji.ac.il/users/danny/freebsd/iscsi-17.tar.bz2"

build_iscsi() {
  cd $WORKINGDIR

  iscsi_tarball=$(urlbasename $URL_ISCSI)
  
  if [ ! -f "$iscsi_tarball" ]; then
		fetch $URL_ISCSI
		if [ 1 == $? ]; then
      echo "==> Failed to fetch $iscsi_tarball."
      return 1
    fi
	fi

	tar zxvf $iscsi_tarball
	cd sys
  ln -s /sys/kern .
  ln -s /sys/tools .
  cd modules/iscsi_initiator
  make clean
  ln -s ../.. @
  make
  cp -v iscsi_initiator.ko $FREENAS/boot/kernel/
  cd ../../../iscontrol/
  make

	return 0
}

install_iscsi() {
	cd $WORKINGDIR/iscsi/sys/modules/iscsi_initiator
	install -sv iscsi_initiator.ko $FREENAS/boot/kernel

 	cd $WORKINGDIR/iscsi/iscontrol/
  install -vs iscontrol $FREENAS/usr/local/sbin

	return 0
}
