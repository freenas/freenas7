PORTNAME=			msntp
PORTVERSION=	1.0
PORTREVISION=	0
PORTEPOCH=		1
CATEGORIES=		net
COMMENT=			A straightforward SNTP daemon/utility
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

BUILD_DEPENDS=	msntp:${PORTSDIR}/net/msntp

do-install:
	@${INSTALL_PROGRAM} -v ${PORTSDIR}/net/msntp/work/msntp*/msntp ${FREENAS_ROOTFS}/usr/local/bin

.include <bsd.port.mk>
