PORTNAME=			lighttpd
PORTVERSION=	1.4.19
PORTREVISION=	0
PORTEPOCH=		1
CATEGORIES=		www
COMMENT=			A secure, fast, compliant, and very flexible Web Server
MAINTAINER=		votdev@gmx.de

MASTER_SITES=	http://www.lighttpd.net/download/ \
							http://mirrors.cat.pdx.edu/lighttpd/

GNU_CONFIGURE=		yes
CONFIGURE_ARGS+=	--sysconfdir=/var/etc/ \
									--enable-lfs \
									--without-mysql \
									--without-ldap \
									--with-openssl \
									--without-lua \
									--with-bzip2 \
									--without-pcre

do-install:
	@${INSTALL_PROGRAM} -v ${WRKSRC}/src/lighttpd ${FREENAS_ROOTFS}/usr/local/sbin

	@${MKDIR} -v ${FREENAS_ROOTFS}/usr/local/lib/lighttpd
	@${INSTALL_PROGRAM} -v ${WRKSRC}/src/.libs/mod_indexfile.so ${FREENAS_ROOTFS}/usr/local/lib/lighttpd
	@${LN} -f -v -s /usr/local/lib/lighttpd/mod_indexfile.so ${FREENAS_ROOTFS}/usr/local/lib/mod_indexfile.so
	@${INSTALL_PROGRAM} -v ${WRKSRC}/src/.libs/mod_access.so ${FREENAS_ROOTFS}/usr/local/lib/lighttpd
	@${LN} -f -v -s /usr/local/lib/lighttpd/mod_access.so ${FREENAS_ROOTFS}/usr/local/lib/mod_access.so
	@${INSTALL_PROGRAM} -v ${WRKSRC}/src/.libs/mod_accesslog.so ${FREENAS_ROOTFS}/usr/local/lib/lighttpd
	@${LN} -f -v -s /usr/local/lib/lighttpd/mod_accesslog.so ${FREENAS_ROOTFS}/usr/local/lib/mod_accesslog.so
	@${INSTALL_PROGRAM} -v ${WRKSRC}/src/.libs/mod_dirlisting.so ${FREENAS_ROOTFS}/usr/local/lib/lighttpd
	@${LN} -f -v -s /usr/local/lib/lighttpd/mod_dirlisting.so ${FREENAS_ROOTFS}/usr/local/lib/mod_dirlisting.so
	@${INSTALL_PROGRAM} -v ${WRKSRC}/src/.libs/mod_staticfile.so ${FREENAS_ROOTFS}/usr/local/lib/lighttpd
	@${LN} -f -v -s /usr/local/lib/lighttpd/mod_staticfile.so ${FREENAS_ROOTFS}/usr/local/lib/mod_staticfile.so
	@${INSTALL_PROGRAM} -v ${WRKSRC}/src/.libs/mod_cgi.so ${FREENAS_ROOTFS}/usr/local/lib/lighttpd
	@${LN} -f -v -s /usr/local/lib/lighttpd/mod_cgi.so ${FREENAS_ROOTFS}/usr/local/lib/mod_cgi.so
	@${INSTALL_PROGRAM} -v ${WRKSRC}/src/.libs/mod_auth.so ${FREENAS_ROOTFS}/usr/local/lib/lighttpd
	@${LN} -f -v -s /usr/local/lib/lighttpd/mod_auth.so ${FREENAS_ROOTFS}/usr/local/lib/mod_auth.so
	@${INSTALL_PROGRAM} -v ${WRKSRC}/src/.libs/mod_webdav.so ${FREENAS_ROOTFS}/usr/local/lib/lighttpd
	@${LN} -f -v -s /usr/local/lib/lighttpd/mod_webdav.so ${FREENAS_ROOTFS}/usr/local/lib/mod_webdav.so

.include <bsd.port.mk>
