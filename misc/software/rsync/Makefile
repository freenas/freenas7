ARCHIVE=	rsync-2.6.9.tar.gz
URL=	http://samba.anu.edu.au/ftp/rsync/$(ARCHIVE)
SRCDIR=	$(WORKINGDIR)/$$(basename $(ARCHIVE) .tar.gz)

do-build:
	-@if [ ! -f $(FREENASDISTFILES)/$(ARCHIVE) ]; then \
		cd $(FREENASDISTFILES) && fetch $(URL); \
	fi

	${TAR} -zxvf $(FREENASDISTFILES)/$(ARCHIVE) -C $(WORKINGDIR)

	@cd $(SRCDIR) && ./configure --with-rsyncd-conf=/var/etc
	@${MAKE} -C $(SRCDIR)

do-install:
	@${INSTALL_PROGRAM} $(SRCDIR)/rsync $(FREENAS)/usr/local/bin

do-clean:
	@${RM} -f -r $(SRCDIR)

.include "bsd.freenas.mk"
