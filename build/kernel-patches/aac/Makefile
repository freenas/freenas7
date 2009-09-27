ARCH!=		/usr/bin/uname -p
ARCHIVE=	aac7x-${ARCH}.tgz
SRCDIR=		${FREENAS_WORKINGDIR}/aac

install:	clean
	@mkdir -pv ${SRCDIR}
	@tar -zxvf ${.CURDIR}/files/${ARCHIVE} -C ${SRCDIR}

# Create backup.
	@cp -pv $(SRCDIR)/aac.c /sys/dev/aac
	@cp -pv ${SRCDIR}/aacreg.h /sys/dev/aac
	@cp -pv ${SRCDIR}/aacvar.h /sys/dev/aac
	@cp -pv ${SRCDIR}/aac_cam.c /sys/dev/aac
	@cp -pv ${SRCDIR}/aac_compat.h /sys/dev/aac
	@cp -pv ${SRCDIR}/aac_debug.c /sys/dev/aac
	@cp -pv ${SRCDIR}/aac_disk.c /sys/dev/aac
	@cp -pv ${SRCDIR}/aac_fwdb.c /sys/dev/aac
	@cp -pv ${SRCDIR}/aac_fwdb.h /sys/dev/aac
	@cp -pv $(SRCDIR)/aac_ioctl.h /sys/sys
	@cp -pv ${SRCDIR}/aac_linux.c /sys/dev/aac
	@cp -pv ${SRCDIR}/aac_pci.c /sys/dev/aac
	@cp -pv ${SRCDIR}/aac_tables.h /sys/dev/aac
	
	@cp -pv ${.CURDIR}/files/Makefile.in /sys/modules/aac/Makefile

	@echo "dev/aac/aac_fwdb.c		optional aac" >> /sys/conf/files

clean:
	@rm -f -r ${SRCDIR}
