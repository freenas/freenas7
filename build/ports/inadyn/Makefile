PORTNAME=			inadyn
PORTVERSION=	1.0
CATEGORIES=		dns
COMMENT=			A simple DYNAMIC DNS client
MAINTAINER=		votdev@gmx.de

# No files are needed to be fetched.
DISTFILES?=
# No build is required for this port.
NO_BUILD?=	yes
# Don't register a port installation as a package.
NO_PKG_REGISTER?=	yes
# Set default target to execute when a port is calling a dependency.
ALWAYS_BUILD_DEPENDS=	yes
DEPENDS_TARGET=	clean build

BUILD_DEPENDS=	inadyn:${PORTSDIR}/dns/inadyn

do-install:
	@${INSTALL_PROGRAM} -v ${PORTSDIR}/dns/inadyn/work/inadyn*/bin/linux/inadyn ${FREENAS_ROOTFS}/usr/local/bin

.include <bsd.port.mk>
