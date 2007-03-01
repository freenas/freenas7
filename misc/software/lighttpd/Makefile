ARCHIVE=	lighttpd-1.4.13.tar.gz
URL=	http://www.lighttpd.net/download/$(ARCHIVE)
SRCDIR=	$(WORKINGDIR)/$$(basename $(ARCHIVE) .tar.gz)

do-build:	clean
	-@if [ ! -f $(FREENASDISTFILES)/$(ARCHIVE) ]; then \
		cd $(FREENASDISTFILES) && fetch $(URL); \
	fi

	tar -zxvf $(FREENASDISTFILES)/$(ARCHIVE) -C $(WORKINGDIR)

	cd $(SRCDIR) && ./configure --sysconfdir=/var/etc/ --enable-lfs --without-mysql --without-ldap --with-openssl --without-lua --with-bzip2 --without-pcre
	make -C $(SRCDIR)

do-install:
	install -vs $(SRCDIR)/src/lighttpd $(FREENAS)/usr/local/sbin

	mkdir -pv $(FREENAS)/usr/local/lib/lighttpd
	cp -pv $(SRCDIR)/src/.libs/mod_indexfile.so $(FREENAS)/usr/local/lib/lighttpd
	cp -pv $(SRCDIR)/src/.libs/mod_access.so $(FREENAS)/usr/local/lib/lighttpd
	cp -pv $(SRCDIR)/src/.libs/mod_accesslog.so $(FREENAS)/usr/local/lib/lighttpd
	cp -pv $(SRCDIR)/src/.libs/mod_dirlisting.so $(FREENAS)/usr/local/lib/lighttpd
	cp -pv $(SRCDIR)/src/.libs/mod_staticfile.so $(FREENAS)/usr/local/lib/lighttpd
	cp -pv $(SRCDIR)/src/.libs/mod_cgi.so $(FREENAS)/usr/local/lib/lighttpd
	cp -pv $(SRCDIR)/src/.libs/mod_auth.so $(FREENAS)/usr/local/lib/lighttpd
	cp -pv $(SRCDIR)/src/.libs/mod_webdav.so $(FREENAS)/usr/local/lib/lighttpd

do-clean:
	@rm -f -r $(SRCDIR)

.include "bsd.freenas.mk"
