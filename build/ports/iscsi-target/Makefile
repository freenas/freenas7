PORTNAME=			iscsi-target
PORTVERSION=	20080207
CATEGORIES=		net
COMMENT=			Implementation of userland ISCSI target (derived from NetBSD)
MAINTAINER=		votdev@gmx.de

# Look here for latest NetBSD patches: http://pkgsrc.se/devel/netbsd-iscsi-target

MASTER_SITES=	http://www.alistaircrooks.co.uk/src/ \
							ftp://ftp.netbsd.org/pub/pkgsrc/distfiles/ \
							http://www.magnesium.net/~mdf/distfiles/
DISTNAME=			netbsd-iscsi-${PORTVERSION}

WRKSRC=					${WRKDIR}/${DISTNAME}/src
GNU_CONFIGURE=	yes

post-patch:
	@${REINPLACE_CMD} -e 's|-lpthread|${PTHREAD_LIBS}|' ${WRKSRC}/Makefile.in
	@${REINPLACE_CMD} -e 's|_LDFLAGS= -pthread|_LDFLAGS= ${PTHREAD_CFLAGS}|' ${WRKSRC}/Makefile.in
	@${REINPLACE_CMD} -e 's|_FLAGS= -pthread|_FLAGS= ${PTHREAD_CFLAGS}|' ${WRKSRC}/Makefile.in

do-install:
	@${INSTALL_PROGRAM} -v ${WRKDIR}/netbsd-iscsi-*/src/iscsi-target ${FREENAS_ROOTFS}/usr/local/bin
	@${INSTALL_SCRIPT} -v ${FILESDIR}/iscsi_target.in ${FREENAS_ROOTFS}/etc/rc.d/iscsi_target

	@${MKDIR} ${FREENAS_ROOTFS}/etc/iscsi
.for i in targets passwd auths
	@${LN} -s -f -v /var/etc/iscsi/${i} ${FREENAS_ROOTFS}/etc/iscsi/${i}
.endfor

.include <bsd.port.mk>
