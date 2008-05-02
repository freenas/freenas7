PORTNAME=						mt-daapd
PORTVERSION=				0.2.4.2
CATEGORIES=					audio
COMMENT=						Multithread daapd yet another server for Apple iTunes
MAINTAINER=					votdev@gmx.de

MASTER_SITES=	SF

LIB_DEPENDS=				gdbm.3:${PORTSDIR}/databases/gdbm \
										id3tag.0:${PORTSDIR}/audio/libid3tag

USE_AUTOTOOLS=			autoheader:261 autoconf:261:env
USE_ICONV=					yes
GNU_CONFIGURE=			yes
CONFIGURE_ENV=			CFLAGS="${CFLAGS} ${PTHREAD_CFLAGS}" \
										CPPFLAGS="${CPPFLAGS} -I${LOCALBASE}/include" \
										LDFLAGS="-L${LOCALBASE}/lib" \
										LIBS="-L${LOCALBASE}/lib ${PTHREAD_LIBS}"
CONFIGURE_TARGET=		--build=${MACHINE_ARCH}-portbld-freebsd${OSREL}
CONFIGURE_ARGS=			--with-gdbm-includes=${LOCALBASE}/include \
										--with-id3tag=${PREFIX}

PATCH_SITES+=				http://www.apionet.or.jp/~tomi/
PATCH_SITES+=				${MASTER_SITE_LOCAL}
PATCHFILES+=				mt-daapd-0.2.4.1-cp932.patch
PATCH_DIST_STRIP+=	-p1

do-install:
	-@${MKDIR} ${FREENAS_ROOTFS}/usr/local/share/${PORTNAME}/admin-root

	@${INSTALL_PROGRAM} -v ${WRKSRC}/src/${PORTNAME} ${FREENAS_ROOTFS}/usr/local/sbin
	@${INSTALL_SCRIPT} -v ${FILESDIR}/${PORTNAME}.sh.in ${FREENAS_ROOTFS}/etc/rc.d/${PORTNAME}.sh
	@${INSTALL_SCRIPT} -v ${FILESDIR}/${PORTNAME}.playlist ${FREENAS_ROOTFS}/usr/local/share/${PORTNAME}

	@${CP} -Rpv ${WRKSRC}/admin-root/ ${FREENAS_ROOTFS}/usr/local/share/${PORTNAME}/admin-root

.include <bsd.port.mk>
