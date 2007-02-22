ARCNAME=	iscsi-2.0.1.tar.bz2
URL=	ftp://ftp.cs.huji.ac.il/users/danny/freebsd/$(ARCNAME)
SRCDIR=	$(WORKINGDIR)/$$(basename $(ARCNAME) .tar.gz)

all:	clean
	-@if [ ! -f $(FREENASDISTFILES)/$(ARCNAME) ]; then \
		cd $(FREENASDISTFILES) && fetch $(URL); \
	fi

	mkdir -pv $(WORKINGDIR)/iscsi
	tar -zxvf $(FREENASDISTFILES)/$(ARCNAME) -C $(WORKINGDIR)/iscsi

	cd $(WORKINGDIR)/iscsi/sys && ln -fsv /sys/kern . && ln -fsv /sys/tools .
	cd $(WORKINGDIR)/iscsi/sys/modules/iscsi_initiator && ln -fsv ../.. @

	make -C $(WORKINGDIR)/iscsi/sys/modules/iscsi_initiator
	make -C $(WORKINGDIR)/iscsi/iscontrol

install:
	install -vs $(WORKINGDIR)/iscsi/sys/modules/iscsi_initiator/iscsi_initiator.ko $(FREENAS)/boot/kernel
	install -vs $(WORKINGDIR)/iscsi/iscontrol/iscontrol $(FREENAS)/usr/local/sbin

clean:
	@rm -f -r $(WORKINGDIR)/iscsi
