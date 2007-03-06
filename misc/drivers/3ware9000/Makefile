ARCHIVE=	FreeBSD-6.x-driver-source-9.4.0.1.tgz
SRCDIR=	$(WORKINGDIR)

install:	clean
	@tar -zxvf ${.CURDIR}/files/$(ARCHIVE) -C $(SRCDIR)

	@cp -pv $(SRCDIR)/twa/Makefile /sys/modules/twa
	@cp -pv $(SRCDIR)/twa/tw_* /sys/dev/twa

clean:
	@rm -f -r $(WORKINGDIR)/twa
