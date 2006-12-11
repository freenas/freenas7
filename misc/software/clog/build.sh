#!/usr/bin/env bash

URL_CLOG="http://www.freenas.org/downloads/clog-1.0.1.tar.gz"
URL_SYSLOGD="http://www.freenas.org/downloads/syslogd_clog-current.tgz"

build_clog() {
	cd /usr/src/usr.bin/

  clog_tarball=$(urlbasename $URL_CLOG)
  syslogd_tarball=$(urlbasename $URL_SYSLOGD)

  if [ ! -f "$clog_tarball" ]; then
		fetch $URL_CLOG
		if [ 1 == $? ]; then
      echo "==> Failed to fetch $clog_tarball."
      return 1
    fi
	fi
	if [ ! -f "$syslogd_tarball" ]; then
    fetch $URL_SYSLOGD
    if [ 1 == $? ]; then
      echo "==> Failed to fetch $syslogd_tarball."
      return 1
    fi
	fi

  tar zxvf $clog_tarball
  tar zxvf $syslogd_tarball

  cd syslogd
  make

  cd ../clog
  gcc clog.c -o clog

	return 0
}

install_clog() {
	clog_tarball=$(urlbasename $URL_CLOG)
	syslogd_tarball=$(urlbasename $URL_SYSLOGD)

  cd /usr/src/usr.bin/

	cd syslogd	
  install -vs syslogd $FREENAS/usr/sbin/
	
	cd ../clog
  install -vs clog $FREENAS/usr/sbin/	

	return 0
}
