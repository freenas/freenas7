PORTNAME=			iscsi-target
PORTVERSION=	1.0
CATEGORIES=		net
COMMENT=			Implementation of userland ISCSI target (derived from NetBSD)
MAINTAINER=		votdev@gmx.de

BUILD_DEPENDS=	iscsi-target:${PORTSDIR}/net/iscsi-target
# No build is required for this port.
NO_BUILD?=			1

do-install:
	@${INSTALL_PROGRAM} ${PORTSDIR}/net/iscsi-target/work/netbsd-iscsi-*/bin/iscsi-target ${FREENAS_ROOTFS}/usr/local/bin/

.include "bsd.freenas.mk"
