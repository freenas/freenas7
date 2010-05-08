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
	@${INSTALL_PROGRAM} -v ${WRKSRC}/src/.libs/mod_access.so ${FREENAS_ROOTFS}/usr/local/lib/lighttpd
	@${INSTALL_PROGRAM} -v ${WRKSRC}/src/.libs/mod_accesslog.so ${FREENAS_ROOTFS}/usr/local/lib/lighttpd
	@${INSTALL_PROGRAM} -v ${WRKSRC}/src/.libs/mod_alias.so ${FREENAS_ROOTFS}/usr/local/lib/lighttpd
	@${INSTALL_PROGRAM} -v ${WRKSRC}/src/.libs/mod_auth.so ${FREENAS_ROOTFS}/usr/local/lib/lighttpd
	@${INSTALL_PROGRAM} -v ${WRKSRC}/src/.libs/mod_cgi.so ${FREENAS_ROOTFS}/usr/local/lib/lighttpd
	@${INSTALL_PROGRAM} -v ${WRKSRC}/src/.libs/mod_cml.so ${FREENAS_ROOTFS}/usr/local/lib/lighttpd
	@${INSTALL_PROGRAM} -v ${WRKSRC}/src/.libs/mod_compress.so ${FREENAS_ROOTFS}/usr/local/lib/lighttpd
	@${INSTALL_PROGRAM} -v ${WRKSRC}/src/.libs/mod_dirlisting.so ${FREENAS_ROOTFS}/usr/local/lib/lighttpd
	@${INSTALL_PROGRAM} -v ${WRKSRC}/src/.libs/mod_evasive.so ${FREENAS_ROOTFS}/usr/local/lib/lighttpd
	@${INSTALL_PROGRAM} -v ${WRKSRC}/src/.libs/mod_evhost.so ${FREENAS_ROOTFS}/usr/local/lib/lighttpd
	@${INSTALL_PROGRAM} -v ${WRKSRC}/src/.libs/mod_expire.so ${FREENAS_ROOTFS}/usr/local/lib/lighttpd
	@${INSTALL_PROGRAM} -v ${WRKSRC}/src/.libs/mod_extforward.so ${FREENAS_ROOTFS}/usr/local/lib/lighttpd
	@${INSTALL_PROGRAM} -v ${WRKSRC}/src/.libs/mod_fastcgi.so ${FREENAS_ROOTFS}/usr/local/lib/lighttpd
	@${INSTALL_PROGRAM} -v ${WRKSRC}/src/.libs/mod_flv_streaming.so ${FREENAS_ROOTFS}/usr/local/lib/lighttpd

	@${INSTALL_PROGRAM} -v ${WRKSRC}/src/.libs/mod_indexfile.so ${FREENAS_ROOTFS}/usr/local/lib/lighttpd
	@${INSTALL_PROGRAM} -v ${WRKSRC}/src/.libs/mod_magnet.so ${FREENAS_ROOTFS}/usr/local/lib/lighttpd
	@${INSTALL_PROGRAM} -v ${WRKSRC}/src/.libs/mod_mysql_vhost.so ${FREENAS_ROOTFS}/usr/local/lib/lighttpd
	@${INSTALL_PROGRAM} -v ${WRKSRC}/src/.libs/mod_proxy.so ${FREENAS_ROOTFS}/usr/local/lib/lighttpd
	@${INSTALL_PROGRAM} -v ${WRKSRC}/src/.libs/mod_redirect.so ${FREENAS_ROOTFS}/usr/local/lib/lighttpd
	@${INSTALL_PROGRAM} -v ${WRKSRC}/src/.libs/mod_rewrite.so ${FREENAS_ROOTFS}/usr/local/lib/lighttpd
	@${INSTALL_PROGRAM} -v ${WRKSRC}/src/.libs/mod_rrdtool.so ${FREENAS_ROOTFS}/usr/local/lib/lighttpd
	@${INSTALL_PROGRAM} -v ${WRKSRC}/src/.libs/mod_scgi.so ${FREENAS_ROOTFS}/usr/local/lib/lighttpd
	@${INSTALL_PROGRAM} -v ${WRKSRC}/src/.libs/mod_secdownload.so ${FREENAS_ROOTFS}/usr/local/lib/lighttpd
	@${INSTALL_PROGRAM} -v ${WRKSRC}/src/.libs/mod_setenv.so ${FREENAS_ROOTFS}/usr/local/lib/lighttpd
	@${INSTALL_PROGRAM} -v ${WRKSRC}/src/.libs/mod_simple_vhost.so ${FREENAS_ROOTFS}/usr/local/lib/lighttpd
	@${INSTALL_PROGRAM} -v ${WRKSRC}/src/.libs/mod_ssi.so ${FREENAS_ROOTFS}/usr/local/lib/lighttpd
	@${INSTALL_PROGRAM} -v ${WRKSRC}/src/.libs/mod_staticfile.so ${FREENAS_ROOTFS}/usr/local/lib/lighttpd
	@${INSTALL_PROGRAM} -v ${WRKSRC}/src/.libs/mod_status.so ${FREENAS_ROOTFS}/usr/local/lib/lighttpd
	@${INSTALL_PROGRAM} -v ${WRKSRC}/src/.libs/mod_trigger_b4_dl.so ${FREENAS_ROOTFS}/usr/local/lib/lighttpd
	@${INSTALL_PROGRAM} -v ${WRKSRC}/src/.libs/mod_userdir.so ${FREENAS_ROOTFS}/usr/local/lib/lighttpd
	@${INSTALL_PROGRAM} -v ${WRKSRC}/src/.libs/mod_usertrack.so ${FREENAS_ROOTFS}/usr/local/lib/lighttpd
	@${INSTALL_PROGRAM} -v ${WRKSRC}/src/.libs/mod_webdav.so ${FREENAS_ROOTFS}/usr/local/lib/lighttpd

.include <bsd.port.mk>
