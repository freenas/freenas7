PORTNAME=			inadyn
PORTVERSION=	1.0
CATEGORIES=		dns
COMMENT=			A simple DYNAMIC DNS client
MAINTAINER=		votdev@gmx.de

BUILD_DEPENDS=	inadyn:${PORTSDIR}/dns/inadyn
# No build is required for this port.
NO_BUILD?=			1
DISTFILES?=

do-install:
	@${INSTALL_PROGRAM} -v ${PORTSDIR}/dns/inadyn/work/inadyn*/bin/linux/inadyn ${FREENAS_ROOTFS}/usr/local/bin

.include <bsd.port.mk>
