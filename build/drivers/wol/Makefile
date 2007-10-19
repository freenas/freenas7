PATCH=	FreeBSD-6.2-wol-2007-10-14.diff

install:
	# Create backup.
	-@if [ ! -e /usr/src/sbin/ifconfig.orig ]; then \
		cp -prv /usr/src/sbin/ifconfig /usr/src/sbin/ifconfig.orig; \
		cp -prv /usr/src/sys/dev/nve /usr/src/sys/dev/nve.orig; \
		cp -prv /usr/src/sys/net /usr/src/sys/net.orig; \
		cp -prv /usr/src/sys/pci /usr/src/sys/pci.orig; \
		cp -prv /usr/src/sys/sys /usr/src/sys/sys.orig; \
	fi

	@patch -p0 -d /usr/src < ${.CURDIR}/files/${PATCH}

	@cd /usr/src; make includes
	@cd /usr/src/sbin/ifconfig; make clean obj; make && make install
