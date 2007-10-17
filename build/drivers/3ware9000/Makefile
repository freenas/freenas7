ARCHIVE=	driver-freebsd_6x-9.4.1.3.tgz
SRCDIR=	${FREENAS_WORKINGDIR}/twa

install:	clean
	@tar -zxvf ${.CURDIR}/files/$(ARCHIVE) -C ${FREENAS_WORKINGDIR}

	@if [ ! -e "/sys/modules/twa.orig" ]; then \
    cp -pvR /sys/modules/twa /sys/modules/twa.orig; \
	fi
	@cp -pv $(SRCDIR)/Makefile /sys/modules/twa
 
	@if [ ! -e "/sys/dev/twa.orig" ]; then \
		cp -pvR /sys/dev/twa /sys/dev/twa.orig; \
	fi
	@cp -pv $(SRCDIR)/tw_* /sys/dev/twa

clean:
	@rm -f -r $(SRCDIR)
