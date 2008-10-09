PORTNAME=			mkpw
PORTVERSION=	1.0
PORTEPOCH=		1
CATEGORIES=		sysutils
COMMENT=			Password generation tool
MAINTAINER=		votdev@gmx.de

DISTFILES?=

do-build:
	@${MKDIR} -p ${WRKSRC}
	@${TAR} -xvf ${FILESDIR}/${PORTNAME}.tar -C ${WRKSRC}
	@${MAKE} -C ${WRKSRC}

do-install:
	@${INSTALL_PROGRAM} -v ${WRKSRC}/${PORTNAME} ${FREENAS_ROOTFS}/usr/sbin

.include <bsd.port.mk>
