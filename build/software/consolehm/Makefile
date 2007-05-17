PORTNAME=			consolehm
PORTVERSION=	1.0
CATEGORIES=		sysutils
COMMENT=			Console based hardware monitor
MAINTAINER=		votdev@gmx.de

BUILD_DEPENDS=	consolehm:${PORTSDIR}/sysutils/consolehm
# No build is required for this port.
NO_BUILD?=			1

do-install:
	@${INSTALL_PROGRAM} -v ${PORTSDIR}/sysutils/consolehm/work/consolehm*/consolehm/chm ${FREENAS_ROOTFS}/usr/local/bin

.include "bsd.freenas.mk"
