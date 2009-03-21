# http://www.3ware.com/kb/article.aspx?id=14847
ARCHIVE=	driver-freebsd_7x-9.5.1.tgz
SRCDIR=	${FREENAS_WORKINGDIR}/twa_fat7

install:	clean
	@tar -zxvf ${.CURDIR}/files/${ARCHIVE} -C ${FREENAS_WORKINGDIR}

	@cp -pv ${SRCDIR}/Makefile /sys/modules/twa
	@cp -pv ${SRCDIR}/tw_* /sys/dev/twa

clean:
	@rm -f -r ${SRCDIR}
