#!/usr/bin/env bash

# Description displayed in dialog menu (max. 60 characters)
MENUDESC="pure-ftpd - A fast and very secure FTP server"
# Is dialog menu selected [ ON | OFF ]
MENUSTATUS="ON"

URL_PUREFTP="ftp://ftp.pureftpd.org/pub/pure-ftpd/releases/pure-ftpd-1.0.21.tar.gz"

build_pureftpd() {
  cd $WORKINGDIR

  pureftpd_tarball=$(urlbasename $URL_PUREFTP)

	if [ ! -f "$pureftpd_tarball" ]; then
		fetch $URL_PUREFTP
		if [ 1 == $? ]; then
      echo "==> Failed to fetch $pureftpd_tarball."
      return 1
    fi
	fi

  tar zxvf $pureftpd_tarball
  cd $(basename $pureftpd_tarball .tar.gz)

  ./configure --with-rfc2640 --with-largefile --with-pam --with-ftpwho
  [ 0 != $? ] && return 1 # successful?

  make

	return $?
}

install_pureftpd() {
  cd $WORKINGDIR

  pureftpd_tarball=$(urlbasename $URL_PUREFTP)
  cd $(basename $pureftpd_tarball .tar.gz)

  install -vs src/pure-ftpd $FREENAS/usr/local/sbin
  install -vs src/pure-ftpwho $FREENAS/usr/local/sbin

  return 0
}
