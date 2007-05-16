PORTNAME=			lighttpd
PORTVERSION=	1.4.15
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
	@${INSTALL_PROGRAM} ${WRKSRC}/src/lighttpd ${FREENAS_ROOTFS}/usr/local/sbin

	@${MKDIR} -v ${FREENAS_ROOTFS}/usr/local/lib/lighttpd
	@${CP} -pv ${WRKSRC}/src/.libs/mod_indexfile.so ${FREENAS_ROOTFS}/usr/local/lib/lighttpd
	@${CP} -pv ${WRKSRC}/src/.libs/mod_access.so ${FREENAS_ROOTFS}/usr/local/lib/lighttpd
	@${CP} -pv ${WRKSRC}/src/.libs/mod_accesslog.so ${FREENAS_ROOTFS}/usr/local/lib/lighttpd
	@${CP} -pv ${WRKSRC}/src/.libs/mod_dirlisting.so ${FREENAS_ROOTFS}/usr/local/lib/lighttpd
	@${CP} -pv ${WRKSRC}/src/.libs/mod_staticfile.so ${FREENAS_ROOTFS}/usr/local/lib/lighttpd
	@${CP} -pv ${WRKSRC}/src/.libs/mod_cgi.so ${FREENAS_ROOTFS}/usr/local/lib/lighttpd
	@${CP} -pv ${WRKSRC}/src/.libs/mod_auth.so ${FREENAS_ROOTFS}/usr/local/lib/lighttpd
	@${CP} -pv ${WRKSRC}/src/.libs/mod_webdav.so ${FREENAS_ROOTFS}/usr/local/lib/lighttpd

.include "bsd.freenas.mk"
