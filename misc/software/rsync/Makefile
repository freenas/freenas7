ARCNAME=	rsync-2.6.9.tar.gz
URL=	http://samba.anu.edu.au/ftp/rsync/$(ARCNAME)
SRCDIR=	$(WORKINGDIR)/$$(basename $(ARCNAME) .tar.gz)

all:	clean
	-@if [ ! -f $(FREENASDISTFILES)/$(ARCNAME) ]; then \
		cd $(FREENASDISTFILES) && fetch $(URL); \
	fi

	tar -zxvf $(FREENASDISTFILES)/$(ARCNAME) -C $(WORKINGDIR)

	cd $(SRCDIR) && ./configure --with-rsyncd-conf=/var/etc
	make -C $(SRCDIR)

install:
	install -vs $(SRCDIR)/rsync $(FREENAS)/usr/local/bin

clean:
	@rm -f -r $(SRCDIR)
