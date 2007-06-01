PKGNAME=	gzip
COMMENT=	A compression utility designed to be a replacement for compress
MAINTAINER=	votdev@gmx.de

BUILD_DEPENDS=	ataidle:${PORTSDIR}/archivers/gzip

do-install:
	@${INSTALL_PROGRAM} ${PORTSDIR}/archivers/gzip/work/gzip*/gzip ${FREENAS_ROOTFS}/usr/bin

.include "bsd.freenas.mk"
