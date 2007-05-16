PKGNAME=	inadyn
COMMENT=	A simple DYNAMIC DNS client
MAINTAINER=	votdev@gmx.de

BUILD_DEPENDS=	inadyn:${PORTSDIR}/dns/inadyn

do-install:
	@${INSTALL_PROGRAM} ${PORTSDIR}/dns/inadyn/work/inadyn*/bin/linux/inadyn ${FREENAS_ROOTFS}/usr/local/bin

.include "bsd.freenas.mk"
