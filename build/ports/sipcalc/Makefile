PORTNAME=			sipcalc
PORTVERSION=	1.0
CATEGORIES=		net-mgmt ipv6
COMMENT=			Sipcalc is an IP subnet calculator with IPv6 support
MAINTAINER=		votdev@gmx.de

BUILD_DEPENDS=	sipcalc:${PORTSDIR}/net-mgmt/sipcalc
# No build is required for this port.
NO_BUILD?=			1

do-install:
	@${INSTALL_PROGRAM} -v ${PORTSDIR}/net-mgmt/sipcalc/work/sipcalc-*/src/sipcalc ${FREENAS_ROOTFS}/usr/local/bin

.include "bsd.freenas.mk"
