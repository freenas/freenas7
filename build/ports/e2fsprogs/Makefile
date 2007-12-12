PORTNAME=			e2fsprogs
PORTVERSION=	1.0
CATEGORIES=		sysutils
COMMENT=			Utilities to manipulate ext2/ext3 filesystems
MAINTAINER=		votdev@gmx.de

BUILD_DEPENDS=	e2fsprogs:${PORTSDIR}/sysutils/e2fsprogs
# No build is required for this port.
NO_BUILD?=			1

post-patch:
# Copy options file
	@${MKDIR} -v /var/db/ports/${PORTNAME}
	@${CP} -pv ${FILESDIR}/${PORTNAME}-options /var/db/ports/${PORTNAME}/options

do-install:
	@${INSTALL_PROGRAM} -v ${PORTSDIR}/sysutils/e2fsprogs/work/e2fsprogs-*/e2fsck/e2fsck ${FREENAS_ROOTFS}/usr/local/sbin
	@${INSTALL_PROGRAM} -v ${PORTSDIR}/sysutils/e2fsprogs/work/e2fsprogs-*/misc/mke2fs ${FREENAS_ROOTFS}/usr/local/sbin
	@${INSTALL_PROGRAM} -v ${PORTSDIR}/sysutils/e2fsprogs/work/e2fsprogs-*/fsck_ext2fs ${FREENAS_ROOTFS}/usr/local/sbin

	@${LN} -s -f -v /usr/local/sbin/e2fsck ${FREENAS_ROOTFS}/sbin/e2fsck
	@${LN} -s -f -v /usr/local/sbin/fsck_ext2fs ${FREENAS_ROOTFS}/sbin/fsck_ext2fs

.include "bsd.freenas.mk"
