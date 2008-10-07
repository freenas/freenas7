PORTNAME=			mkpw
PORTVERSION=	1.0
CATEGORIES=		sysutils
COMMENT=			Password generation tool
MAINTAINER=		votdev@gmx.de

DISTFILES?=

do-build:
	@${MKDIR} -p ${WRKSRC}
	@${TAR} -zxvf ${FILESDIR}/${PORTNAME}.tgz -C ${WRKSRC}
	@${MAKE} -C ${WRKSRC}

do-install:
	@${INSTALL_PROGRAM} -v ${WRKSRC}/${PORTNAME} ${FREENAS_ROOTFS}/usr/sbin

.include <bsd.port.mk>
