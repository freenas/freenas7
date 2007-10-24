ARCHIVE=	driver-freebsd_6x-9.4.1.3.tgz
SRCDIR=	${FREENAS_WORKINGDIR}/twa

install:	clean
	@tar -zxvf ${.CURDIR}/files/${ARCHIVE} -C ${FREENAS_WORKINGDIR}

# Create backup.
	-@if [ ! -e /sys/modules/twa.orig ]; then \
		cp -pvR /sys/modules/twa /sys/modules/twa.orig; \
	fi
 
	-@if [ ! -e /sys/dev/twa.orig ]; then \
		cp -pvR /sys/dev/twa /sys/dev/twa.orig; \
	fi

	@cp -pv ${SRCDIR}/Makefile /sys/modules/twa
	@cp -pv ${SRCDIR}/tw_* /sys/dev/twa

clean:
	@rm -f -r ${SRCDIR}

	-@if [ -e /sys/modules/twa.orig ]; then \
		rm -rfv /sys/modules/twa; \
		mv -fv /sys/modules/twa.orig /sys/modules/twa; \
	fi

	-@if [ -e /sys/dev/twa.orig ]; then \
		rm -rfv /sys/dev/twa; \
		mv -fv /sys/dev/twa.orig /sys/dev/twa; \
	fi
