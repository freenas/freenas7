PORTNAME=			smartmontools
PORTVERSION=	1.0
PORTREVISION=	0
PORTEPOCH=		1
CATEGORIES=		sysutils
COMMENT=			S.M.A.R.T. disk monitoring tools
MAINTAINER=		votdev@gmx.de

# No files are needed to be fetched.
DISTFILES?=
# No build is required for this port.
NO_BUILD?=	yes
# Don't register a port installation as a package.
NO_PKG_REGISTER?=	yes
# Set default target to execute when a port is calling a dependency.
DEPENDS_TARGET=

BUILD_DEPENDS=	smartmontools:${PORTSDIR}/sysutils/smartmontools

post-patch:
# Copy smarttools options file
	@${MKDIR} -v /var/db/ports/smartmontools
	@${CP} -pv ${FILESDIR}/options /var/db/ports/smartmontools

do-install:
	@${INSTALL_PROGRAM} -v ${PORTSDIR}/sysutils/smartmontools/work/smartmontools-*/smartctl ${FREENAS_ROOTFS}/usr/local/sbin
	@${INSTALL_PROGRAM} -v ${PORTSDIR}/sysutils/smartmontools/work/smartmontools-*/smartd ${FREENAS_ROOTFS}/usr/local/sbin

.include <bsd.port.mk>
