ARCHIVE=	iscsi-2.0.1.tar.bz2
URL=	ftp://ftp.cs.huji.ac.il/users/danny/freebsd/$(ARCHIVE)
SRCDIR=	$(WORKINGDIR)/iscsi

build:	clean
	-@if [ ! -f $(FREENASDISTFILES)/$(ARCHIVE) ]; then \
		cd $(FREENASDISTFILES) && fetch $(URL); \
	fi

	mkdir -pv $(SRCDIR)
	tar -zxvf $(FREENASDISTFILES)/$(ARCHIVE) -C $(SRCDIR)

	cd $(SRCDIR)/sys && ln -sv /sys/kern . && ln -sv /sys/tools .
	cd $(SRCDIR)/sys/modules/iscsi_initiator && ln -sv ../.. @

	make -C $(SRCDIR)/sys/modules/iscsi_initiator
	make -C $(SRCDIR)/iscontrol

install:
	install -vs $(SRCDIR)/sys/modules/iscsi_initiator/iscsi_initiator.ko $(FREENAS)/boot/kernel
	install -vs $(SRCDIR)/iscontrol/iscontrol $(FREENAS)/usr/local/sbin

clean:
	@rm -f -r $(WORKINGDIR)/iscsi
