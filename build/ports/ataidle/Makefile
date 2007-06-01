PORTNAME=			ataidle
PORTVERSION=	1.0
CATEGORIES=		sysutils
COMMENT=			Utility to set spindown timeout for ATA drives
MAINTAINER=		votdev@gmx.de

BUILD_DEPENDS=	ataidle:${PORTSDIR}/sysutils/ataidle
# No build is required for this port.
NO_BUILD?=			1

do-install:
	@${INSTALL_PROGRAM} -v ${PORTSDIR}/sysutils/ataidle/work/ataidle*/ataidle ${FREENAS_ROOTFS}/usr/local/sbin

.include "bsd.freenas.mk"
