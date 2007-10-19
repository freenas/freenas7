PATCH=	patch-ata.diff

install:
	# Create backup.
	-@if [ ! -e /usr/src/sys/dev/ata.orig ]; then \
		cp -prv /usr/src/sys/dev/ata /usr/src/sys/dev/ata.orig; \
	fi

	@patch -E -u -d /usr/src/sys/dev < ${.CURDIR}/files/${PATCH}
