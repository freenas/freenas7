PORTNAME=			iscsi-target
PORTVERSION=	20070815
CATEGORIES=		net
COMMENT=			Implementation of userland ISCSI target (derived from NetBSD)
MAINTAINER=		votdev@gmx.de

MASTER_SITES=	http://www.alistaircrooks.co.uk/src/ \
							http://www.magnesium.net/~mdf/distfiles/
DISTNAME=			netbsd-iscsi-${PORTVERSION}

WRKSRC=						${WRKDIR}/${DISTNAME}/src
GNU_CONFIGURE=		yes
CONFIGURE_TARGET=	--build=${MACHINE_ARCH}-portbld-freebsd${OSREL}

post-patch:
	@${REINPLACE_CMD} -e 's|-lpthread|${PTHREAD_LIBS}|' ${WRKSRC}/Makefile.in
	@${REINPLACE_CMD} -e 's|_LDFLAGS= -pthread|_LDFLAGS= ${PTHREAD_CFLAGS}|' ${WRKSRC}/Makefile.in
	@${REINPLACE_CMD} -e 's|_FLAGS= -pthread|_FLAGS= ${PTHREAD_CFLAGS}|' ${WRKSRC}/Makefile.in

do-install:
	@${INSTALL_PROGRAM} -v ${PORTSDIR}/net/iscsi-target/work/netbsd-iscsi-*/bin/iscsi-target ${FREENAS_ROOTFS}/usr/local/bin/

	@${MKDIR} ${FREENAS_ROOTFS}/etc/iscsi
	@${LN} -s -f -v /var/etc/iscsi/auths ${FREENAS_ROOTFS}/etc/iscsi/auths
	@${LN} -s -f -v /var/etc/iscsi/passwd ${FREENAS_ROOTFS}/etc/iscsi/passwd
	@${LN} -s -f -v /var/etc/iscsi/targets ${FREENAS_ROOTFS}/etc/iscsi/targets

.include "bsd.freenas.mk"
