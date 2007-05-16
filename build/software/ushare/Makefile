PORTNAME=			ushare
PORTVERSION=	1.0
CATEGORIES=		net
COMMENT=			A lightweight UPnP (TM) A/V Media Server
MAINTAINER=		votdev@gmx.de

BUILD_DEPENDS=	ushare:${PORTSDIR}/net/ushare
# No build is required for this port.
NO_BUILD?=			1

do-install:
	@${INSTALL_PROGRAM} ${PORTSDIR}/net/ushare/work/ushare-*/src/ushare ${FREENAS_ROOTFS}/usr/local/bin

.include "bsd.freenas.mk"
