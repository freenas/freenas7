PKGNAME=	nfs
COMMENT=	NFS Tools
MAINTAINER=	votdev@gmx.de

do-install:
	@${INSTALL_PROGRAM} /usr/sbin/nfsd ${FREENAS_ROOTFS}/usr/sbin
	@${INSTALL_PROGRAM} /usr/sbin/mountd ${FREENAS_ROOTFS}/usr/sbin
	@${INSTALL_PROGRAM} /usr/sbin/rpcbind ${FREENAS_ROOTFS}/usr/sbin

.include "bsd.freenas.mk"
