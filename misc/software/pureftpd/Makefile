ARCHIVE=	pure-ftpd-1.0.21.tar.gz
URL=	ftp://ftp.pureftpd.org/pub/pure-ftpd/releases/$(ARCHIVE)
SRCDIR=	$(WORKINGDIR)/$$(basename $(ARCHIVE) .tar.gz)

build:	clean
	-@if [ ! -f $(FREENASDISTFILES)/$(ARCHIVE) ]; then \
		cd $(FREENASDISTFILES) && fetch $(URL); \
	fi

	tar -zxvf $(FREENASDISTFILES)/$(ARCHIVE) -C $(WORKINGDIR)

	cd $(SRCDIR) && ./configure --with-rfc2640 --with-largefile --with-pam --with-ftpwho
	make -C $(SRCDIR)

install:
	install -vs $(SRCDIR)/src/pure-ftpd $(FREENAS)/usr/local/sbin
	install -vs $(SRCDIR)/src/pure-ftpwho $(FREENAS)/usr/local/sbin

clean:
	@rm -f -r $(SRCDIR)
