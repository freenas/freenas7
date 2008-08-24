PORTNAME=			inadyn-mt
PORTVERSION=	02.12.24
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

do-install:
	@${INSTALL_PROGRAM} -v ${WRKSRC}/bin/linux/inadyn-mt ${FREENAS_ROOTFS}/usr/local/bin/inadyn
	@${INSTALL_SCRIPT} -v ${FILESDIR}/inadyn.in ${FREENAS_ROOTFS}/etc/rc.d/inadyn

.include <bsd.port.mk>
