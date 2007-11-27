ARCHIVE=	aac6x-i386.tgz
SRCDIR=		${FREENAS_WORKINGDIR}/aac

install:	clean
	@mkdir -pv ${SRCDIR}
	@tar -zxvf ${.CURDIR}/files/${ARCHIVE} -C ${SRCDIR}

# Create backup.
	-@if [ ! -e /sys/dev/aac.orig ]; then \
		cp -pvR /sys/dev/aac /sys/dev/aac.orig; \
		cp -pvR /sys/sys/aac_ioctl.h /sys/sys/aac_ioctl.h.orig; \
		cp -pvR /sys/modules/aac/Makefile /sys/modules/aac/Makefile.orig; \
		cp -pvR /sys/conf/files /sys/conf/files.orig; \
	fi

	@cp -pv $(SRCDIR)/aac.c /sys/dev/aac
	@cp -pv ${SRCDIR}/aac_cam.c /sys/dev/aac
	@cp -pv ${SRCDIR}/aac_compat.h /sys/dev/aac
	@cp -pv ${SRCDIR}/aac_debug.c /sys/dev/aac
	@cp -pv ${SRCDIR}/aac_disk.c /sys/dev/aac
	@cp -pv ${SRCDIR}/aac_fwdb.c /sys/dev/aac
	@cp -pv ${SRCDIR}/aac_fwdb.h /sys/dev/aac
	@cp -pv ${SRCDIR}/aac_linux.c /sys/dev/aac
	@cp -pv ${SRCDIR}/aac_pci.c /sys/dev/aac
	@cp -pv ${SRCDIR}/aac_tables.h /sys/dev/aac
	@cp -pv ${SRCDIR}/aacreg.h /sys/dev/aac
	@cp -pv ${SRCDIR}/aacvar.h /sys/dev/aac

	@cp -pv $(SRCDIR)/aac_ioctl.h /sys/sys
	@cp -pv ${.CURDIR}/files/Makefile.in /sys/modules/aac/Makefile

	@echo "dev/aac/aac_fwdb.c		optional aac" >> /sys/conf/files

clean:
	@rm -f -r ${SRCDIR}

#	-@if [ -e /sys/dev/aac.orig ]; then \
#		rm -rfv /sys/dev/aac; \
#		mv -fv /sys/dev/aac.orig /sys/dev/aac; \
#		mv -fv /sys/sys/aac_ioctl.h.orig /sys/sys/aac_ioctl.h; \
#		mv -fv /sys/modules/aac/Makefile.orig /sys/modules/aac/Makefile; \
#		mv -fv /sys/conf/files.orig /sys/conf/files; \
#	fi
