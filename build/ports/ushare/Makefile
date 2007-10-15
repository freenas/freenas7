PORTNAME=			ushare
PORTVERSION=	1.0
PORTREVISION=	0
PORTEPOCH=		1
CATEGORIES=		net
COMMENT=			A lightweight UPnP (TM) A/V Media Server
MAINTAINER=		votdev@gmx.de

BUILD_DEPENDS=	ushare:${PORTSDIR}/net/ushare
# No build is required for this port.
NO_BUILD?=			1

do-install:
	@${INSTALL_PROGRAM} -v ${PORTSDIR}/net/ushare/work/ushare-*/src/ushare ${FREENAS_ROOTFS}/usr/local/bin
	@${INSTALL_SCRIPT} ${FILESDIR}/${PORTNAME}.in ${FREENAS_ROOTFS}/etc/rc.d/${PORTNAME}

.include "bsd.freenas.mk"
