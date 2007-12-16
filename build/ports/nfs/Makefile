PORTNAME=			nfs
PORTVERSION=	1.0
CATEGORIES=		sysutils
COMMENT=			NFS Tools
MAINTAINER=		votdev@gmx.de

# No files are needed to be fetched.
DISTFILES?=
# No build is required for this port.
NO_BUILD?=	yes
# Don't register a port installation as a package.
NO_PKG_REGISTER?=	yes
# Set default target to execute when a port is calling a dependency.
DEPENDS_TARGET=

do-install:
	@${INSTALL_PROGRAM} -v /usr/sbin/nfsd ${FREENAS_ROOTFS}/usr/sbin
	@${INSTALL_PROGRAM} -v /usr/sbin/mountd ${FREENAS_ROOTFS}/usr/sbin
	@${INSTALL_PROGRAM} -v /usr/sbin/rpcbind ${FREENAS_ROOTFS}/usr/sbin

.include <bsd.port.mk>
