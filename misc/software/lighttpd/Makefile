ARCHIVE=	lighttpd-1.4.13.tar.gz
URL=	http://www.lighttpd.net/download/$(ARCHIVE)
SRCDIR=	$(WORKINGDIR)/$$(basename $(ARCHIVE) .tar.gz)

do-build:
	-@if [ ! -f $(FREENASDISTFILES)/$(ARCHIVE) ]; then \
		cd $(FREENASDISTFILES) && fetch $(URL); \
	fi

	@${TAR} -zxvf $(FREENASDISTFILES)/$(ARCHIVE) -C $(WORKINGDIR)

	@cd $(SRCDIR) && ./configure --sysconfdir=/var/etc/ --enable-lfs --without-mysql --without-ldap --with-openssl --without-lua --with-bzip2 --without-pcre
	@${MAKE} -C $(SRCDIR)

do-install:
	@${INSTALL_PROGRAM} $(SRCDIR)/src/lighttpd $(FREENAS)/usr/local/sbin

	@${MKDIR} -v $(FREENAS)/usr/local/lib/lighttpd
	@${CP} -pv $(SRCDIR)/src/.libs/mod_indexfile.so $(FREENAS)/usr/local/lib/lighttpd
	@${CP} -pv $(SRCDIR)/src/.libs/mod_access.so $(FREENAS)/usr/local/lib/lighttpd
	@${CP} -pv $(SRCDIR)/src/.libs/mod_accesslog.so $(FREENAS)/usr/local/lib/lighttpd
	@${CP} -pv $(SRCDIR)/src/.libs/mod_dirlisting.so $(FREENAS)/usr/local/lib/lighttpd
	@${CP} -pv $(SRCDIR)/src/.libs/mod_staticfile.so $(FREENAS)/usr/local/lib/lighttpd
	@${CP} -pv $(SRCDIR)/src/.libs/mod_cgi.so $(FREENAS)/usr/local/lib/lighttpd
	@${CP} -pv $(SRCDIR)/src/.libs/mod_auth.so $(FREENAS)/usr/local/lib/lighttpd
	@${CP} -pv $(SRCDIR)/src/.libs/mod_webdav.so $(FREENAS)/usr/local/lib/lighttpd

do-clean:
	@${RM} -f -r $(SRCDIR)

.include "bsd.freenas.mk"
