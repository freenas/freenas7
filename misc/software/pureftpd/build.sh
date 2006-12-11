#!/usr/bin/env bash

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

  ./configure --with-rfc2640 --with-largefile --with-pam
  make

	return 0
}

install_pureftpd() {
  cd $WORKINGDIR

  pureftpd_tarball=$(urlbasename $URL_PUREFTP)
  cd $(basename $pureftpd_tarball .tar.gz)

  install -vs src/pure-ftpd $FREENAS/usr/local/sbin/

  return 0
}
