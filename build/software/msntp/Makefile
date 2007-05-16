PORTNAME=			msntp
PORTVERSION=	1.0
CATEGORIES=		net
COMMENT=			A straightforward SNTP daemon/utility
MAINTAINER=		votdev@gmx.de

BUILD_DEPENDS=	msntp:${PORTSDIR}/net/msntp
# No build is required for this port.
NO_BUILD?=			1

do-install:
	@${INSTALL_PROGRAM} ${PORTSDIR}/net/msntp/work/msntp*/msntp ${FREENAS_ROOTFS}/usr/local/bin

	@${CP} -pv ${FILESDIR}/runmsntp.sh ${FREENAS_ROOTFS}/usr/local/bin
	@${CHMOD} +x ${FREENAS_ROOTFS}/usr/local/bin/runmsntp.sh

.include "bsd.freenas.mk"
