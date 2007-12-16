PORTNAME=			sfdisk
PORTVERSION=	1.0
CATEGORIES=		sysutils
COMMENT=			Standalone sysinstall's fdisk
MAINTAINER=		votdev@gmx.de

# No files are needed to be fetched.
DISTFILES?=
# No build is required for this port.
NO_BUILD?=	yes
# Don't register a port installation as a package.
NO_PKG_REGISTER?=	yes
# Set default target to execute when a port is calling a dependency.
DEPENDS_TARGET=

BUILD_DEPENDS=	sfdisk:${PORTSDIR}/sysutils/sfdisk

do-install:
	@${INSTALL_PROGRAM} -v ${PORTSDIR}/sysutils/sfdisk/work/sfdisk*/sfdisk ${FREENAS_ROOTFS}/usr/local/sbin

.include <bsd.port.mk>
