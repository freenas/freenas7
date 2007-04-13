PKGNAME=	ushare
COMMENT=	A lightweight UPnP (TM) A/V Media Server
MAINTAINER=	votdev@gmx.de

BUILD_DEPENDS=	ushare:${PORTSDIR}/net/ushare

do-install:
	@${INSTALL_PROGRAM} ${PORTSDIR}/net/ushare/work/ushare-*/src/ushare ${FREENAS_ROOTFS}/usr/local/bin

.include "bsd.freenas.mk"
