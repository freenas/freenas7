PORTNAME=						mt-daapd
PORTVERSION=				1586
CATEGORIES=					audio
COMMENT=						Multithread daapd yet another server for Apple iTunes
MAINTAINER=					votdev@gmx.de

MASTER_SITES=				http://nightlies.mt-daapd.org/
DISTNAME=						${PORTNAME}-svn-${PORTVERSION}

LIB_DEPENDS=				sqlite3:${PORTSDIR}/databases/sqlite3 \
										id3tag.0:${PORTSDIR}/audio/libid3tag

USE_AUTOTOOLS=			autoheader:261 autoconf:261:env
USE_ICONV=					yes
GNU_CONFIGURE=			yes
CONFIGURE_ENV=			CFLAGS="${CFLAGS} ${PTHREAD_CFLAGS}" \
										CPPFLAGS="${CPPFLAGS} -I${LOCALBASE}/include" \
										LDFLAGS="-L${LOCALBASE}/lib" \
										LIBS="-L${LOCALBASE}/lib ${PTHREAD_LIBS}"
CONFIGURE_TARGET=		--build=${MACHINE_ARCH}-portbld-freebsd${OSREL}
CONFIGURE_ARGS=			--disable-gdbm \
										--enable-sqlite3 \
										--with-id3tag=${PREFIX}

do-install:
	-@${MKDIR} -v ${FREENAS_ROOTFS}/usr/local/share/${PORTNAME}
	-@${MKDIR} -v ${FREENAS_ROOTFS}/usr/local/share/${PORTNAME}/plugins
	-@${MKDIR} -v ${FREENAS_ROOTFS}/usr/local/share/${PORTNAME}/admin-root

	@${INSTALL_PROGRAM} -v ${WRKSRC}/src/${PORTNAME} ${FREENAS_ROOTFS}/usr/local/sbin
	@${INSTALL_PROGRAM} -v ${WRKSRC}/src/plugins/.libs/out-daap.so ${FREENAS_ROOTFS}/usr/local/share/${PORTNAME}/plugins
	@${INSTALL_PROGRAM} -v ${WRKSRC}/src/plugins/.libs/rsp.so ${FREENAS_ROOTFS}/usr/local/share/${PORTNAME}/plugins
	@${INSTALL_SCRIPT} -v ${FILESDIR}/${PORTNAME}.playlist ${FREENAS_ROOTFS}/usr/local/share/${PORTNAME}
	@${INSTALL_SCRIPT} -v ${FILESDIR}/${PORTNAME}.sh.in ${FREENAS_ROOTFS}/etc/rc.d/${PORTNAME}.sh

	@${CP} -Rpv ${WRKSRC}/admin-root/ ${FREENAS_ROOTFS}/usr/local/share/${PORTNAME}/admin-root

.include <bsd.port.mk>
