ARCHIVE=	aoe-freebsd-1.2.0.tar.gz
SRCDIR=		${FREENAS_WORKINGDIR}/aoe

install:	clean
	@mkdir -pv ${SRCDIR}
	@tar -zxvf ${.CURDIR}/files/${ARCHIVE} -C ${SRCDIR}

	@cp -Rpv ${SRCDIR}/dev/aoe /sys/dev

	@mkdir -pv /sys/modules/aoe
	@mv -v /sys/dev/aoe/Makefile /sys/modules/aoe

	@echo "dev/aoe/aoeblk.c		optional aoe" >> /sys/conf/files
	@echo "dev/aoe/aoecmd.c		optional aoe" >> /sys/conf/files
	@echo "dev/aoe/aoedev.c		optional aoe" >> /sys/conf/files
	@echo "dev/aoe/aoemain.c	optional aoe" >> /sys/conf/files
	@echo "dev/aoe/aoenet.c		optional aoe" >> /sys/conf/files

clean:
	@rm -f -r ${SRCDIR}
