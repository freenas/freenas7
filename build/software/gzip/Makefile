PORTNAME=			gzip
PORTVERSION=	1.0
CATEGORIES=		archivers
COMMENT=			A compression utility designed to be a replacement for compress
MAINTAINER=		votdev@gmx.de

BUILD_DEPENDS=	gzip:${PORTSDIR}/archivers/gzip
# No build is required for this port.
NO_BUILD?=			1

do-install:
	@${INSTALL_PROGRAM} ${PORTSDIR}/archivers/gzip/work/gzip*/gzip ${FREENAS_ROOTFS}/usr/bin

.include "bsd.freenas.mk"
