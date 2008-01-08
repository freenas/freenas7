ARCHIVE=	driver-freebsd_6x-9.4.1.3.tgz
SRCDIR=	${FREENAS_WORKINGDIR}/twa

install:	clean
	@tar -zxvf ${.CURDIR}/files/${ARCHIVE} -C ${FREENAS_WORKINGDIR}

	@cp -pv ${SRCDIR}/Makefile /sys/modules/twa
	@cp -pv ${SRCDIR}/tw_* /sys/dev/twa

clean:
	@rm -f -r ${SRCDIR}
