ARCHIVE=	FreeBSD-6.x-driver-source-9.4.0.1.tgz
SRCDIR=	$(WORKINGDIR)/twa

install:	clean
	@tar -zxvf ${.CURDIR}/files/$(ARCHIVE) -C $(WORKINGDIR)

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
