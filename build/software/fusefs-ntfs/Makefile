PORTNAME=			fusefs-ntfs
PORTVERSION=	1.0
CATEGORIES=		sysutils
COMMENT=			Mount NTFS partitions and disk images
MAINTAINER=		votdev@gmx.de

BUILD_DEPENDS=	fusefs-ntfs:${PORTSDIR}/sysutils/fusefs-ntfs \
								fusefs-kmod:${PORTSDIR}/sysutils/fusefs-kmod \
								fusefs-libs:${PORTSDIR}/sysutils/fusefs-libs
# No build is required for this port.
NO_BUILD?=			1

pre-depends:
# Copy options file
	@${MKDIR} -v /var/db/ports/fusefs
	@${CP} -pv ${FILESDIR}/options /var/db/ports/fusefs

do-install:
	@${INSTALL_PROGRAM} -v ${PORTSDIR}/sysutils/fusefs-kmod/work/fuse4bsd*/mount_fusefs/mount_fusefs ${FREENAS_ROOTFS}/usr/local/sbin
	@${INSTALL_PROGRAM} -v ${PORTSDIR}/sysutils/fusefs-ntfs/work/ntfs-3g*/src/.libs/ntfs-3g ${FREENAS_ROOTFS}/usr/local/bin
	@${CP} -pv ${PORTSDIR}/sysutils/fusefs-kmod/work/fuse4bsd*/fuse_module/fuse.ko ${FREENAS_ROOTFS}/boot/kernel
	@${CP} -pv ${PORTSDIR}/sysutils/fusefs-ntfs/work/ntfs-3g*/libntfs-3g/.libs/libntfs-3g.so.1 ${FREENAS_ROOTFS}/usr/local/lib
	@${CP} -pv ${PORTSDIR}/sysutils/fusefs-libs/work/fuse-*/lib/.libs/libfuse.so.2 ${FREENAS_ROOTFS}/usr/local/lib

.include "bsd.freenas.mk"
