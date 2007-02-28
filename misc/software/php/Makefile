ARCHIVE=	php-5.2.1.tar.gz
URL=	http://de2.php.net/distributions/$(ARCHIVE)
SRCDIR=	$(WORKINGDIR)/$$(basename $(ARCHIVE) .tar.gz)

LIB_DEPENDS=	xml2.5:${PORTSDIR}/textproc/libxml2

build:	clean
	-@if [ ! -f $(FREENASDISTFILES)/$(ARCHIVE) ]; then \
		@cd $(FREENASDISTFILES) && fetch $(URL); \
	fi

	-${TAR} -zxvf $(FREENASDISTFILES)/$(ARCHIVE) -C $(WORKINGDIR)

	@cd $(SRCDIR) && ./configure --enable-fastcgi --enable-discard-path --enable-force-cgi-redirect --without-mysql --without-pear --with-openssl --without-sqlite --with-pcre-regex --with-gettext
	@${MAKE} -C $(SRCDIR)

install:
	@${INSTALL_PROGRAM} $(SRCDIR)/sapi/cgi/php $(FREENAS)/usr/local/bin
	@${CP} -pv ${FILESDIR}/php.ini $(FREENAS)/usr/local/lib

clean:
	@${RM} -f -r $(SRCDIR)

.include "bsd.freenas.mk"
