PORTNAME=			lighttpd
PORTVERSION=	1.4.26
CATEGORIES=		www
COMMENT=			A secure, fast, compliant, and very flexible Web Server
MAINTAINER=		votdev@gmx.de

MASTER_SITES=	http://download.lighttpd.net/lighttpd/releases-1.4.x/ \
							http://mirrors.cat.pdx.edu/lighttpd/

LIB_DEPENDS+=			uuid.1:${PORTSDIR}/misc/e2fsprogs-libuuid \
									sqlite3.8:${PORTSDIR}/databases/sqlite3 \
									pcre.0:${PORTSDIR}/devel/pcre

USE_BZIP2=				yes
USE_AUTOTOOLS=	libtool:22
GNU_CONFIGURE=		yes
USE_OPENSSL=			yes
USE_GNOME+=				libxml2
CONFIGURE_ARGS+=	--sysconfdir=/var/etc/ \
									--enable-lfs \
									--without-mysql \
									--without-ldap \
									--with-openssl \
									--without-lua \
									--with-bzip2 \
									--with-webdav-props \
									--with-webdav-locks

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
