PATCH=	FreeBSD-6.2-wol-2007-08-30.diff

install:
	-@if [ ! -e /usr/src/sbin/ifconfig ]; then \
		cp -r /usr/src/sbin/ifconfig /usr/src/sbin/ifconfig.orig; \
		cp -r /usr/src/sys/dev/nve /usr/src/sys/dev/nve.orig; \
		cp -r /usr/src/sys/net /usr/src/sys/net.orig; \
		cp -r /usr/src/sys/pci /usr/src/sys/pci.orig; \
		cp -r /usr/src/sys/sys /usr/src/sys/sys.orig; \
		patch -p0 -d /usr/src < ${.CURDIR}/files/$(PATCH); \
		cd /usr/src; \
		make includes; \
		cd /usr/src/sbin/ifconfig; \
		make clean obj; \
		make && make install; \
	fi
