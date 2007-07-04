PORTNAME=			msntp
PORTVERSION=	1.0
PORTREVISION=	0
PORTEPOCH=		1
CATEGORIES=		net
COMMENT=			A straightforward SNTP daemon/utility
MAINTAINER=		votdev@gmx.de

BUILD_DEPENDS=	msntp:${PORTSDIR}/net/msntp
# No build is required for this port.
NO_BUILD?=			1

do-install:
	@${INSTALL_PROGRAM} -v ${PORTSDIR}/net/msntp/work/msntp*/msntp ${FREENAS_ROOTFS}/usr/local/bin

.include "bsd.freenas.mk"
