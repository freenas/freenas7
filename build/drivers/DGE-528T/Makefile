PATCH=	if_re.diff

install:
	-@if [ ! -e /usr/src/sys/dev/re.orig ]; then \
		cp -r /usr/src/sys/dev/re /usr/src/sys/dev/re.orig; \
		patch -E -u -d /usr/src/sys/dev/re < ${.CURDIR}/files/$(PATCH); \
	fi
