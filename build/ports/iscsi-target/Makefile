PORTNAME=			iscsi-target
PORTVERSION=	1.0
PORTREVISION=	0
PORTEPOCH=		1
CATEGORIES=		net
COMMENT=			Implementation of userland ISCSI target (derived from NetBSD)
MAINTAINER=		votdev@gmx.de

BUILD_DEPENDS=	iscsi-target:${PORTSDIR}/net/iscsi-target
# No build is required for this port.
NO_BUILD?=			1

do-install:
	@${INSTALL_PROGRAM} -v ${PORTSDIR}/net/iscsi-target/work/netbsd-iscsi-*/bin/iscsi-target ${FREENAS_ROOTFS}/usr/local/bin/

	@${MKDIR} ${FREENAS_ROOTFS}/etc/iscsi
	@${LN} -s -f -v /var/etc/iscsi/auths ${FREENAS_ROOTFS}/etc/iscsi/auths
	@${LN} -s -f -v /var/etc/iscsi/passwd ${FREENAS_ROOTFS}/etc/iscsi/passwd
	@${LN} -s -f -v /var/etc/iscsi/targets ${FREENAS_ROOTFS}/etc/iscsi/targets

.include "bsd.freenas.mk"
