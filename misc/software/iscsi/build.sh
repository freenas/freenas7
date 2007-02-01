#!/usr/bin/env bash

URL_ISCSI="ftp://ftp.cs.huji.ac.il/users/danny/freebsd/iscsi-2.0.1.tar.bz2"

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

  mkdir iscsi
	tar -zxvf $iscsi_tarball -C ./iscsi

	cd $WORKINGDIR/iscsi/sys
  ln -s /sys/kern .
  ln -s /sys/tools .
  cd modules/iscsi_initiator
  make clean
  ln -s ../.. @
  make
  [ 0 != $? ] && return 1 # successful?

  cp -v iscsi_initiator.ko $FREENAS/boot/kernel/

  cd $WORKINGDIR/iscsi/iscontrol
  make clean
  make

	return $?
}

install_iscsi() {
	cd $WORKINGDIR/iscsi/sys/modules/iscsi_initiator
	install -sv iscsi_initiator.ko $FREENAS/boot/kernel

 	cd $WORKINGDIR/iscsi/iscontrol/
  install -vs iscontrol $FREENAS/usr/local/sbin

	return 0
}
