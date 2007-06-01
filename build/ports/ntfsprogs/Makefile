PORTNAME=			ntfsprogs
PORTVERSION=	1.0
CATEGORIES=		sysutils
COMMENT=			Utilities to manipulaite NTFS filesystems
MAINTAINER=		FreeNAS team

BUILD_DEPENDS=	ntfsprogs:${PORTSDIR}/sysutils/ntfsprogs
# No build is required for this port.
NO_BUILD?=			1

do-install:
	@${INSTALL_PROGRAM} -v ${PORTSDIR}/sysutils/ntfsprogs/work/ntfsprogs-*/ntfsprogs/.libs/ntfsfix ${FREENAS_ROOTFS}/usr/local/bin
	@${CP} -pv ${PORTSDIR}/sysutils/ntfsprogs/work/ntfsprogs-*/libntfs/.libs/libntfs.so.9 ${FREENAS_ROOTFS}/usr/local/lib/libntfs.so.9

.include "bsd.freenas.mk"
