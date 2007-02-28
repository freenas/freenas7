ARCHIVE=	pure-ftpd-1.0.21.tar.gz
URL=	ftp://ftp.pureftpd.org/pub/pure-ftpd/releases/$(ARCHIVE)
SRCDIR=	$(WORKINGDIR)/$$(basename $(ARCHIVE) .tar.gz)

build:	clean
	-@if [ ! -f $(FREENASDISTFILES)/$(ARCHIVE) ]; then \
		@cd $(FREENASDISTFILES) && fetch $(URL); \
	fi

	${TAR} -zxvf $(FREENASDISTFILES)/$(ARCHIVE) -C $(WORKINGDIR)

	@cd $(SRCDIR) && ./configure --with-rfc2640 --with-largefile --with-pam --with-ftpwho
	@${MAKE} -C $(SRCDIR)

install:
	@${INSTALL_PROGRAM} $(SRCDIR)/src/pure-ftpd $(FREENAS)/usr/local/sbin
	@${INSTALL_PROGRAM} $(SRCDIR)/src/pure-ftpwho $(FREENAS)/usr/local/sbin

clean:
	@${RM} -f -r $(SRCDIR)

.include "bsd.freenas.mk"
