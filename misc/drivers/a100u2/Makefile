SRCDIR=	$(WORKINGDIR)/a100

install:	clean
	mkdir -pv $(SRCDIR)
	tar -zxvf ${.CURDIR}/files/bsd4a100.zip -C $(SRCDIR)

	cp -pv $(SRCDIR)/a100.* /usr/src/sys/pci
	@echo "pci/a100.c optional ihb device-driver" >> /usr/src/sys/conf/files

clean:
	@rm -f -r $(SRCDIR)
