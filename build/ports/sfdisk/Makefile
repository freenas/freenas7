PORTNAME=			sfdisk
PORTVERSION=	0.2
CATEGORIES=		sysutils
COMMENT=			Standalone sysinstall's fdisk
MAINTAINER=		votdev@gmx.de

MASTER_SITES=	http://src4.narod.ru

USE_BZIP2=			yes
MAKEFILE=				Makefile.bsd
NOT_FOR_ARCHS=	ia64

do-install:
	@${INSTALL_PROGRAM} -v ${WRKSRC}/sfdisk ${FREENAS_ROOTFS}/usr/local/sbin

.include <bsd.port.mk>
