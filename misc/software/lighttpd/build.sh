#!/usr/bin/env bash

URL_LIGHTTPD="http://www.lighttpd.net/download/lighttpd-1.4.13.tar.gz"

build_lighttpd() {
  cd $WORKINGDIR

  lighttpd_tarball=$(urlbasename $URL_LIGHTTPD)

  if [ ! -f "$lighttpd_tarball" ]; then
		fetch $URL_LIGHTTPD
		if [ 1 == $? ]; then
      echo "==> Failed to fetch $lighttpd_tarball."
      return 1
    fi
	fi

	tar -zxvf $lighttpd_tarball

	cd $(basename $lighttpd_tarball .tar.gz)

	./configure --sysconfdir=/var/etc/ --enable-lfs --without-mysql --without-ldap --with-openssl --without-lua --with-bzip2 --without-pcre
	make

	return 0
}

install_lighttpd() {
 	cd $WORKINGDIR

 	lighttpd_tarball=$(urlbasename $URL_LIGHTTPD)
	cd $(basename $lighttpd_tarball .tar.gz)

	install -vs src/lighttpd $FREENAS/usr/local/sbin

	mkdir $FREENAS/usr/local/lib/lighttpd

  cp -v src/.libs/mod_indexfile.so $FREENAS/usr/local/lib/lighttpd
  cp -v src/.libs/mod_access.so $FREENAS/usr/local/lib/lighttpd
  cp -v src/.libs/mod_accesslog.so $FREENAS/usr/local/lib/lighttpd
  cp -v src/.libs/mod_dirlisting.so $FREENAS/usr/local/lib/lighttpd
  cp -v src/.libs/mod_staticfile.so $FREENAS/usr/local/lib/lighttpd
  cp -v src/.libs/mod_cgi.so $FREENAS/usr/local/lib/lighttpd
  cp -v src/.libs/mod_auth.so $FREENAS/usr/local/lib/lighttpd
  cp -v src/.libs/mod_webdav.so $FREENAS/usr/local/lib/lighttpd

	return 0
}
