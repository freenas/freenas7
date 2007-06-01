PKGNAME=	lighttpd
COMMENT=	A secure, fast, compliant, and very flexible Web Server
MAINTAINER=	votdev@gmx.de

ARCHIVE=	lighttpd-1.4.15.tar.gz
URL=	http://www.lighttpd.net/download/$(ARCHIVE)
SRCDIR=	${FREENAS_WORKINGDIR}/$$(basename $(ARCHIVE) .tar.gz)

do-build:
	-@if [ ! -f ${FREENAS_DISTFILES}/$(ARCHIVE) ]; then \
		cd ${FREENAS_DISTFILES} && fetch $(URL); \
	fi

	@${TAR} -zxvf ${FREENAS_DISTFILES}/$(ARCHIVE) -C ${FREENAS_WORKINGDIR}

	@cd $(SRCDIR) && ./configure --sysconfdir=/var/etc/ --enable-lfs --without-mysql --without-ldap --with-openssl --without-lua --with-bzip2 --without-pcre
	@${MAKE} -C $(SRCDIR)

do-install:
	@${INSTALL_PROGRAM} $(SRCDIR)/src/lighttpd ${FREENAS_ROOTFS}/usr/local/sbin

	@${MKDIR} -v ${FREENAS_ROOTFS}/usr/local/lib/lighttpd
	@${CP} -pv $(SRCDIR)/src/.libs/mod_indexfile.so ${FREENAS_ROOTFS}/usr/local/lib/lighttpd
	@${CP} -pv $(SRCDIR)/src/.libs/mod_access.so ${FREENAS_ROOTFS}/usr/local/lib/lighttpd
	@${CP} -pv $(SRCDIR)/src/.libs/mod_accesslog.so ${FREENAS_ROOTFS}/usr/local/lib/lighttpd
	@${CP} -pv $(SRCDIR)/src/.libs/mod_dirlisting.so ${FREENAS_ROOTFS}/usr/local/lib/lighttpd
	@${CP} -pv $(SRCDIR)/src/.libs/mod_staticfile.so ${FREENAS_ROOTFS}/usr/local/lib/lighttpd
	@${CP} -pv $(SRCDIR)/src/.libs/mod_cgi.so ${FREENAS_ROOTFS}/usr/local/lib/lighttpd
	@${CP} -pv $(SRCDIR)/src/.libs/mod_auth.so ${FREENAS_ROOTFS}/usr/local/lib/lighttpd
	@${CP} -pv $(SRCDIR)/src/.libs/mod_webdav.so ${FREENAS_ROOTFS}/usr/local/lib/lighttpd

do-clean:
	@${RM} -f -r $(SRCDIR)

.include "bsd.freenas.mk"
