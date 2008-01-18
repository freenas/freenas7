PORTNAME=			inadyn
PORTVERSION=	1.96.2
PORTREVISION=	1
CATEGORIES=		dns
COMMENT=			A simple DYNAMIC DNS client
MAINTAINER=		votdev@gmx.de

MASTER_SITES=	http://inadyn.ina-tech.net/ \
							http://www.mirrorservice.org/sites/ftp.freebsd.org/pub/FreeBSD/ports/distfiles/
DISTNAME=			${PORTNAME}.v${PORTVERSION}

WRKSRC=				${WRKDIR}/${PORTNAME}
USE_ZIP=			yes
USE_DOS2UNIX=	yes
USE_GMAKE=		yes
MAKEFILE=			makefile
EXTRACT_SUFX=	.zip

post-patch:
	@${REINPLACE_CMD} -e 's|gcc|$$\(CC\)|g' ${WRKSRC}/makefile
	${REINPLACE_CMD} -e "s,%%PREFIX%%,${PREFIX},g" \
		${WRKSRC}/src/dyndns.h

do-install:
	@${INSTALL_PROGRAM} -v ${WRKSRC}/bin/linux/inadyn ${FREENAS_ROOTFS}/usr/local/bin
	@${INSTALL_SCRIPT} -v ${FILESDIR}/inadyn.in ${FREENAS_ROOTFS}/etc/rc.d/inadyn

.include <bsd.port.mk>
