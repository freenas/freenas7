ARCHIVE=	aac6x-i386.tar
SRCDIR=		${FREENAS_WORKINGDIR}/aac

install:	clean
	@mkdir -pv ${SRCDIR}
	@tar -xvf ${.CURDIR}/files/${ARCHIVE} -C ${SRCDIR}

# Create backup.
	-@if [ ! -e /sys/dev/aac.orig ]; then \
		cp -pvR /sys/dev/aac /sys/dev/aac.orig; \
	fi

	@cp -pv $(SRCDIR)/aac.c /sys/dev/aac
	@cp -pv ${SRCDIR}/aac_cam.c /sys/dev/aac
	@cp -pv ${SRCDIR}/aac_debug.c /sys/dev/aac
	@cp -pv ${SRCDIR}/aac_disk.c /sys/dev/aac
	@cp -pv ${SRCDIR}/aac_linux.c /sys/dev/aac
	@cp -pv ${SRCDIR}/aac_pci.c /sys/dev/aac
	@cp -pv ${SRCDIR}/aac_tables.h /sys/dev/aac
	@cp -pv ${SRCDIR}/aacreg.h /sys/dev/aac
	@cp -pv ${SRCDIR}/aacvar.h /sys/dev/aac

clean:
	@rm -f -r ${SRCDIR}

	-@if [ -e /sys/dev/aac.orig ]; then \
		rm -rfv /sys/dev/aac; \
		mv -fv /sys/dev/aac.orig /sys/dev/aac; \
	fi
