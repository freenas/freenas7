PORTNAME=			sfdisk
PORTVERSION=	1.0
CATEGORIES=		sysutils
COMMENT=			Standalone sysinstall's fdisk
MAINTAINER=		votdev@gmx.de

BUILD_DEPENDS=	sfdisk:${PORTSDIR}/sysutils/sfdisk
# No build is required for this port.
NO_BUILD?=			1

do-install:
	@${INSTALL_PROGRAM} ${PORTSDIR}/sysutils/sfdisk/work/sfdisk*/sfdisk ${FREENAS_ROOTFS}/usr/local/sbin

.include "bsd.freenas.mk"
