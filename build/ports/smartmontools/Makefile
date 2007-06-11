PORTNAME=			smartmontools
PORTVERSION=	1.0
PORTREVISION=	0
PORTEPOCH=		1
CATEGORIES=		sysutils
COMMENT=			S.M.A.R.T. disk monitoring tools
MAINTAINER=		votdev@gmx.de

BUILD_DEPENDS=	smartmontools:${PORTSDIR}/sysutils/smartmontools
# No build is required for this port.
NO_BUILD?=			1

post-patch:
# Copy smarttools options file
	@${MKDIR} -v /var/db/ports/smartmontools
	@${CP} -pv ${FILESDIR}/options /var/db/ports/smartmontools

do-install:
	@${INSTALL_PROGRAM} -v ${PORTSDIR}/sysutils/smartmontools/work/smartmontools-*/smartctl ${FREENAS_ROOTFS}/usr/local/sbin
	@${INSTALL_PROGRAM} -v ${PORTSDIR}/sysutils/smartmontools/work/smartmontools-*/smartd ${FREENAS_ROOTFS}/usr/local/sbin

.include "bsd.freenas.mk"
