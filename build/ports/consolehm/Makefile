PORTNAME=			consolehm
PORTVERSION=	1.0
CATEGORIES=		sysutils
COMMENT=			Console based hardware monitor
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

BUILD_DEPENDS=	consolehm:${PORTSDIR}/sysutils/consolehm

do-install:
	@${INSTALL_PROGRAM} -v ${PORTSDIR}/sysutils/consolehm/work/consolehm*/consolehm/chm ${FREENAS_ROOTFS}/usr/local/bin

.include <bsd.port.mk>
