ARCNAME=	php-5.2.1.tar.gz
URL=	http://de2.php.net/distributions/$(ARCNAME)
SRCDIR=	$(WORKINGDIR)/$$(basename $(ARCNAME) .tar.gz)

all:	clean
	-@if [ ! -f $(FREENASDISTFILES)/$(ARCNAME) ]; then \
		cd $(FREENASDISTFILES) && fetch $(URL); \
	fi

	tar -zxvf $(FREENASDISTFILES)/$(ARCNAME) -C $(WORKINGDIR)

	cd $(SRCDIR) && ./configure --enable-fastcgi --enable-discard-path --enable-force-cgi-redirect --without-mysql --without-pear --with-openssl --without-sqlite --with-pcre-regex --with-gettext
	make -C $(SRCDIR)

install:
	install -vs $(SRCDIR)/sapi/cgi/php $(FREENAS)/usr/local/bin
	cp -pv ${.CURDIR}/files/php.ini $(FREENAS)/usr/local/lib

clean:
	@rm -f -r $(SRCDIR)
