#!/usr/bin/env bash

URL_SAMBA="http://us1.samba.org/samba/ftp/stable/samba-3.0.23d.tar.gz"
PKG_SAMBA="openldap-client"

build_samba() {
  cd $WORKINGDIR

  # Check if needed packages are installed.
  check_packages $PKG_SAMBA
  if [ 1 == $? ]; then
    echo "==> Install missing package(s) first."
    return 1
  fi

  samba_tarball=$(urlbasename $URL_SAMBA)

	if [ ! -f "$samba_tarball" ]; then
		fetch $URL_SAMBA
		if [ 1 == $? ]; then
      echo "==> Failed to fetch $samba_tarball."
      return 1
    fi
	fi

	tar -zxvf $samba_tarball
	samba_dir=$(ls -d samba-3* | tail -n1)
	cd $samba_dir/source

	./configure --without-cups --with-ads --disable-cups --with-pam --with-ldapsam --with-acl-support --with-winbind --with-pam_smbpass --with-logfilebase=/var/log/samba --with-piddir=/var/run --with-privatedir=/var/etc/private --with-configdir=/var/etc --with-lockdir=/var/run --with-piddir=/var/run --with-shared-modules=idmap_rid --with-pammodulesdir=/usr/local/lib --with-syslog
	make

	return 0
}

install_samba() {
  cd $WORKINGDIR

  samba_tarball=$(urlbasename $URL_SAMBA)
	cd $(basename $samba_tarball .tar.gz)/source

	install -s bin/smbd $FREENAS/usr/local/sbin/
	install -s bin/nmbd $FREENAS/usr/local/sbin/
	install -s bin/winbindd $FREENAS/usr/local/sbin/
	install -s bin/wbinfo $FREENAS/usr/local/bin/
	install -s bin/net $FREENAS/usr/local/bin/
	install -s bin/smbpasswd $FREENAS/usr/local/bin/
	install -s bin/smbstatus $FREENAS/usr/bin/
	install -s bin/smbcontrol $FREENAS/usr/bin/
	install -s bin/smbtree $FREENAS/usr/bin/

	mkdir -p $FREENAS/usr/local/lib/samba/charset
	mkdir $FREENAS/usr/local/lib/samba/rpc
	mkdir $FREENAS/usr/local/lib/samba/pdb
	mkdir -p $FREENAS/usr/local/samba/lib/idmap
	mkdir $FREENAS/usr/local/samba/lib/vfs

	cp -v bin/CP*.so $FREENAS/usr/local/lib/samba/charset
	cp -v codepages/*.dat $FREENAS/usr/local/lib/samba
	cp -v po/*.* $FREENAS/usr/local/lib/samba
	cp -v bin/recycle.so $FREENAS/usr/local/samba/lib/vfs
	#cp -v bin/rid.so $FREENAS/usr/local/samba/lib/idmap
	
	return 0
}
