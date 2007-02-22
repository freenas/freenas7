ARCHIVE=	rsync-2.6.9.tar.gz
URL=	http://samba.anu.edu.au/ftp/rsync/$(ARCHIVE)
SRCDIR=	$(WORKINGDIR)/$$(basename $(ARCHIVE) .tar.gz)

build:	clean
	-@if [ ! -f $(FREENASDISTFILES)/$(ARCHIVE) ]; then \
		cd $(FREENASDISTFILES) && fetch $(URL); \
	fi

	tar -zxvf $(FREENASDISTFILES)/$(ARCHIVE) -C $(WORKINGDIR)

	cd $(SRCDIR) && ./configure --with-rsyncd-conf=/var/etc
	make -C $(SRCDIR)

install:
	install -vs $(SRCDIR)/rsync $(FREENAS)/usr/local/bin

clean:
	@rm -f -r $(SRCDIR)
