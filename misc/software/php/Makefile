.include "bsd.freenas.mk"

ARCHIVE=	php-5.2.1.tar.gz
URL=	http://de2.php.net/distributions/$(ARCHIVE)
SRCDIR=	$(WORKINGDIR)/$$(basename $(ARCHIVE) .tar.gz)

# PHP depends on libxml2.
PORTDIR=	/usr/ports/textproc/libxml2

build:	clean build_xml install_xml
	-@if [ ! -f $(FREENASDISTFILES)/$(ARCHIVE) ]; then \
		cd $(FREENASDISTFILES) && fetch $(URL); \
	fi

	-tar -zxvf $(FREENASDISTFILES)/$(ARCHIVE) -C $(WORKINGDIR)

	cd $(SRCDIR) && ./configure --enable-fastcgi --enable-discard-path --enable-force-cgi-redirect --without-mysql --without-pear --with-openssl --without-sqlite --with-pcre-regex --with-gettext
	make -C $(SRCDIR)

install:
	install -vs $(SRCDIR)/sapi/cgi/php $(FREENAS)/usr/local/bin
	cp -pv ${FILESDIR}/php.ini $(FREENAS)/usr/local/lib

clean:	clean_xml
	@rm -f -r $(SRCDIR)

build_xml:
	make -C $(PORTDIR)

install_xml:
	make -C $(PORTDIR) deinstall
	make -C $(PORTDIR) reinstall

clean_xml:
	make -C $(PORTDIR) clean
