PORTNAME=			gzip
PORTVERSION=	1.0
PORTREVISION=	0
PORTEPOCH=		1
CATEGORIES=		archivers
COMMENT=			A compression utility designed to be a replacement for compress
MAINTAINER=		votdev@gmx.de

# No files are needed to be fetched.
DISTFILES?=
# No build is required for this port.
NO_BUILD?=	yes
# Don't register a port installation as a package.
NO_PKG_REGISTER?=	yes
# Set default target to execute when a port is calling a dependency.
ALWAYS_BUILD_DEPENDS=	yes
DEPENDS_TARGET=	clean build

BUILD_DEPENDS=	gzip:${PORTSDIR}/archivers/gzip

do-install:
	@${INSTALL_PROGRAM} -v ${PORTSDIR}/archivers/gzip/work/gzip*/gzip ${FREENAS_ROOTFS}/usr/bin
	@${INSTALL_SCRIPT} -v ${PORTSDIR}/archivers/gzip/work/gzip*/gunzip ${FREENAS_ROOTFS}/usr/bin
	@${INSTALL_SCRIPT} -v ${PORTSDIR}/archivers/gzip/work/gzip*/zcat ${FREENAS_ROOTFS}/usr/bin

.include <bsd.port.mk>
