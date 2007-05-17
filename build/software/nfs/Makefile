PORTNAME=			nfs
PORTVERSION=	1.0
CATEGORIES=		sysutils
COMMENT=			NFS Tools
MAINTAINER=		votdev@gmx.de

# No build is required for this port.
NO_BUILD?=		1

do-install:
	@${INSTALL_PROGRAM} -v /usr/sbin/nfsd ${FREENAS_ROOTFS}/usr/sbin
	@${INSTALL_PROGRAM} -v /usr/sbin/mountd ${FREENAS_ROOTFS}/usr/sbin
	@${INSTALL_PROGRAM} -v /usr/sbin/rpcbind ${FREENAS_ROOTFS}/usr/sbin

.include "bsd.freenas.mk"
