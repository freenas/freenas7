PORTNAME=			fuppes
PORTVERSION=	0.7.2.3
CATEGORIES=		net
COMMENT=			Free UPnP Entertainment Service
MAINTAINER=		votdev@gmx.de

MASTER_SITES=				${MASTER_SITE_SOURCEFORGE}
MASTER_SITE_SUBDIR=	${PORTNAME}
DISTNAME=						${PORTNAME}-SVN-578

LIB_DEPENDS+=			sqlite3:${PORTSDIR}/databases/sqlite3 \
									tag:${PORTSDIR}/audio/taglib \
									pcre.0:${PORTSDIR}/devel/pcre

USE_AUTOTOOLS=		autoconf:261
USE_GNOME=				libxml2
USE_ICONV=				yes
GNU_CONFIGURE=		yes
CONFIGURE_TARGET=	--build="${MACHINE_ARCH}-portbld-freebsd${OSREL}"
CONFIGURE_ARGS+=	--disable-transcoding \
									--disable-lame \
									--disable-twolame \
									--disable-libnotify \
									--disable-imagemagick \
									--disable-libavformat
CONFIGURE_ENV=		CPPFLAGS="-I${LOCALBASE}/include ${PTHREAD_CFLAGS}" \
									LIBS="-L${LOCALBASE}/lib ${PTHREAD_LIBS}"

post-patch:
	@${REINPLACE_CMD} -e 's|-ldl ||' ${WRKSRC}/configure.in

# Copy sqlite3 options file
	@${MKDIR} -v /var/db/ports/sqlite3
	@${CP} -pv ${FILESDIR}/sqlite3-options.in /var/db/ports/sqlite3/options

do-install:
	@${INSTALL_PROGRAM} -v ${WRKSRC}/src/.libs/${PORTNAME}d ${FREENAS_ROOTFS}/usr/local/bin
	@${INSTALL_PROGRAM} -v ${WRKSRC}/src/.libs/libfuppes.so.* ${FREENAS_ROOTFS}/usr/local/lib
	@${INSTALL_SCRIPT} -v ${FILESDIR}/${PORTNAME}.in ${FREENAS_ROOTFS}/etc/rc.d/${PORTNAME}
	@${MKDIR} -v ${FREENAS_ROOTFS}/usr/local/etc/${PORTNAME}
	@${INSTALL_DATA} -v ${FILESDIR}/vfolder.cfg ${FREENAS_ROOTFS}/usr/local/share/${PORTNAME}

.include <bsd.port.mk>
