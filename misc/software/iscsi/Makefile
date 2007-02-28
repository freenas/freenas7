ARCHIVE=	iscsi-2.0.1.tar.bz2
URL=	ftp://ftp.cs.huji.ac.il/users/danny/freebsd/$(ARCHIVE)
SRCDIR=	$(WORKINGDIR)/iscsi

build:	clean
	-@if [ ! -f $(FREENASDISTFILES)/$(ARCHIVE) ]; then \
		@cd $(FREENASDISTFILES) && fetch $(URL); \
	fi

	@${MKDIR} -v $(SRCDIR)
	@${TAR} -zxvf $(FREENASDISTFILES)/$(ARCHIVE) -C $(SRCDIR)

	@cd $(SRCDIR)/sys && ln -sv /sys/kern . && ln -sv /sys/tools .
	@cd $(SRCDIR)/sys/modules/iscsi_initiator && ln -sv ../.. @

	@${MAKE} -C $(SRCDIR)/sys/modules/iscsi_initiator
	@${MAKE} -C $(SRCDIR)/iscontrol

install:
	@${INSTALL_PROGRAM} $(SRCDIR)/sys/modules/iscsi_initiator/iscsi_initiator.ko $(FREENAS)/boot/kernel
	@${INSTALL_PROGRAM} $(SRCDIR)/iscontrol/iscontrol $(FREENAS)/usr/local/sbin

clean:
	@${RM} -f -r $(WORKINGDIR)/iscsi
