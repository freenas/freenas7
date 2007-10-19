PATCH=	patch-ata.diff

install:
	-@if [ ! -e /usr/src/sys/dev/ata.orig ]; then \
		cp -prv /usr/src/sys/dev/ata /usr/src/sys/dev/ata.orig; \
		patch -E -u -d /usr/src/sys/dev < ${.CURDIR}/files/${PATCH}; \
	fi
