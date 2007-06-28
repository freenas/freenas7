PORTNAME=			subcalc
PORTVERSION=	1.0
CATEGORIES=		net-mgmt ipv6
COMMENT=			Advanced IP/IPv6 subnet calculation and discovery
MAINTAINER=		votdev@gmx.de

BUILD_DEPENDS=	subcalc:${PORTSDIR}/net-mgmt/subcalc
# No build is required for this port.
NO_BUILD?=			1

do-install:
	@${INSTALL_PROGRAM} -v ${PORTSDIR}/net-mgmt/subcalc/work/subcalc-*/subcalc ${FREENAS_ROOTFS}/usr/local/bin

.include "bsd.freenas.mk"
