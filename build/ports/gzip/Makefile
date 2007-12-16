PORTNAME=			gzip
PORTVERSION=	1.0
PORTREVISION=	0
PORTEPOCH=		1
CATEGORIES=		archivers
COMMENT=			A compression utility designed to be a replacement for compress
MAINTAINER=		votdev@gmx.de

BUILD_DEPENDS=	gzip:${PORTSDIR}/archivers/gzip
# No build is required for this port.
NO_BUILD?=			1
DISTFILES?=

do-install:
	@${INSTALL_PROGRAM} -v ${PORTSDIR}/archivers/gzip/work/gzip*/gzip ${FREENAS_ROOTFS}/usr/bin
	@${INSTALL_SCRIPT} -v ${PORTSDIR}/archivers/gzip/work/gzip*/gunzip ${FREENAS_ROOTFS}/usr/bin
	@${INSTALL_SCRIPT} -v ${PORTSDIR}/archivers/gzip/work/gzip*/zcat ${FREENAS_ROOTFS}/usr/bin

.include <bsd.port.mk>
