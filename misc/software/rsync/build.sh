#!/usr/bin/env bash

# Description displayed in dialog menu (max. 60 characters)
MENUDESC="rsync - A network file distribution/synchronization utility"
# Is dialog menu selected [ ON | OFF ]
MENUSTATUS="ON"

URL_RSYNC="http://samba.anu.edu.au/ftp/rsync/rsync-2.6.9.tar.gz"

build_rsync() {
	cd $WORKINGDIR

  rsync_tarball=$(urlbasename $URL_RSYNC)

	if [ ! -f "$rsync_tarball" ]; then
		fetch $URL_RSYNC
		if [ 1 == $? ]; then
      echo "==> Failed to fetch $rsync_tarball."
      return 1
    fi
	fi
  
  tar zxvf $rsync_tarball
  cd $(basename $rsync_tarball .tar.gz)

  ./configure --with-rsyncd-conf=/var/etc
  [ 0 != $? ] && return 1 # successful?

  make

	return $?
}

install_rsync() {
 	cd $WORKINGDIR

 	rsync_tarball=$(urlbasename $URL_RSYNC)
	cd $(basename $rsync_tarball .tar.gz)

	install -vs rsync $FREENAS/usr/local/bin

	return 0
}
