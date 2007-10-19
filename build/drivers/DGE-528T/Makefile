PATCH=	if_re.diff

install:
	# Create backup.
	-@if [ ! -e /usr/src/sys/dev/re.orig ]; then \
		cp -prv /usr/src/sys/dev/re /usr/src/sys/dev/re.orig; \
	fi

	@patch -E -u -d /usr/src/sys/dev/re < ${.CURDIR}/files/${PATCH}
