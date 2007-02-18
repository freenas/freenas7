#!/usr/bin/env bash

# Description displayed in dialog menu (max. 60 characters)
MENUDESC="netatalk - File and print server for AppleTalk networks"
# Is dialog menu selected [ ON | OFF ]
MENUSTATUS="ON"

URL_NETATALK="http://heanet.dl.sourceforge.net/sourceforge/netatalk/netatalk-2.0.3.tar.gz"

build_netatalk() {
  cd $WORKINGDIR

  netatalk_tarball=$(urlbasename $URL_NETATALK)

	if [ ! -f "$netatalk_tarball" ]; then
		fetch $URL_NETATALK
		if [ 1 == $? ]; then
      echo "==> Failed to fetch $netatalk_tarball."
      return 1
    fi
	fi

  tar zxvf $netatalk_tarball
  cd $(basename $netatalk_tarball .tar.gz)

  ./configure --bindir=/usr/local/bin --sbindir=/usr/local/sbin --sysconfdir=/var/etc --localstatedir=/var --enable-largefile --disable-tcp-wrappers --disable-cups --with-pam --with-uams-path=/etc/uams/
  [ 0 != $? ] && return 1 # successful?

  make

	return $?
}

install_netatalk() {
  cd $WORKINGDIR

  netatalk_tarball=$(urlbasename $URL_NETATALK)
  cd $(basename $netatalk_tarball .tar.gz)

  install -vs etc/afpd/afpd $FREENAS/usr/local/sbin

  mkdir -p $FREENAS/etc/uams
  cp -v etc/uams/.libs/uams_passwd.so $FREENAS/etc/uams
  cp -v etc/uams/.libs/uams_dhx_passwd.so $FREENAS/etc/uams
  cp -v etc/uams/.libs/uams_guest.so $FREENAS/etc/uams
  cp -v etc/uams/.libs/uams_randnum.so $FREENAS/etc/uams

  cd $FREENAS/etc/uams
  ln -s uams_passwd.so uams_clrtxt.so
  ln -s uams_dhx_passwd.so uams_dhx.so

  #cd $FREENAS/usr/local/lib/
  #cp -v /usr/local/lib/libdb-4.2.so.2 .
  #cd $FREENAS/usr/lib/
  #cp -v /usr/lib/librpcsvc.so.3 .

  return 0
}
