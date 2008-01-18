PORTNAME=			inadyn-mt
PORTVERSION=	02.01.13
CATEGORIES=		dns
COMMENT=			A simple DYNAMIC DNS client
MAINTAINER=		votdev@gmx.de

MASTER_SITES=				${MASTER_SITE_SOURCEFORGE}
MASTER_SITE_SUBDIR=	${PORTNAME}
DISTNAME=						${PORTNAME}.v.${PORTVERSION}

WRKSRC=				${WRKDIR}/${PORTNAME}
USE_DOS2UNIX=	yes
USE_GMAKE=		yes
MAKEFILE=			makefile

post-patch:
	@${REINPLACE_CMD} -e 's|gcc|$$\(CC\)|g' ${WRKSRC}/makefile

do-install:
	@${INSTALL_PROGRAM} -v ${WRKSRC}/bin/linux/inadyn ${FREENAS_ROOTFS}/usr/local/bin
	@${INSTALL_SCRIPT} -v ${FILESDIR}/inadyn.in ${FREENAS_ROOTFS}/etc/rc.d/inadyn

.include <bsd.port.mk>
