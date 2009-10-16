PORTNAME=			inadyn-mt
PORTVERSION=	02.14.06
CATEGORIES=		dns
COMMENT=			A simple DYNAMIC DNS client
MAINTAINER=		votdev@gmx.de

MASTER_SITES=	SF/${PORTNAME}/${PORTNAME}/${PORTNAME}.v.${PORTVERSION}
DISTNAME=			${PORTNAME}.v.${PORTVERSION}

WRKSRC=				${WRKDIR}/${PORTNAME}.v.${PORTVERSION}
USE_DOS2UNIX=	yes
USE_GMAKE=		yes
MAKEFILE=			makefile

do-install:
	@${INSTALL_PROGRAM} -v ${WRKSRC}/bin/linux/inadyn-mt ${FREENAS_ROOTFS}/usr/local/bin/inadyn
	@${INSTALL_SCRIPT} -v ${FILESDIR}/inadyn.in ${FREENAS_ROOTFS}/etc/rc.d/inadyn

.include <bsd.port.mk>
