PKGNAME=	fusefs-ntfs
COMMENT=	Mount NTFS partitions and disk images
MAINTAINER=	votdev@gmx.de

BUILD_DEPENDS=	fusefs-ntfs:${PORTSDIR}/sysutils/fusefs-ntfs \
		fusefs-kmod:${PORTSDIR}/sysutils/fusefs-kmod

pre-depends:
# Patch fusefs-ntfs port.
	-@if [ ! -e /usr/ports/sysutils/fusefs-ntfs.orig ]; then \
		cp -r /usr/ports/sysutils/fusefs-ntfs /usr/ports/sysutils/fusefs-ntfs.orig; \
		${PATCH} -E -u -d /usr/ports/sysutils < ${FILESDIR}/patch-fusefs-ntfs-1.417.diff; \
	fi

# Copy options file
	@${MKDIR} -v /var/db/ports/fusefs
	@${CP} -pv ${FILESDIR}/options /var/db/ports/fusefs

do-install:
	@${INSTALL_PROGRAM} ${PORTSDIR}/sysutils/fusefs-kmod/work/fuse4bsd*/mount_fusefs/mount_fusefs ${FREENAS_ROOTFS}/usr/local/sbin
	@${CP} -pv ${PORTSDIR}/sysutils/fusefs-kmod/work/fuse4bsd*/fuse_module/fuse.ko ${FREENAS_ROOTFS}/boot/kernel
	@${CP} -pv ${PORTSDIR}/sysutils/fusefs-ntfs/work/ntfs-3g*/libntfs-3g/.libs/libntfs-3g.so ${FREENAS_ROOTFS}/usr/local/lib/libntfs-3g.so.0

	@${INSTALL_PROGRAM} ${PORTSDIR}/sysutils/fusefs-ntfs/work/ntfs-3g*/src/.libs/ntfs-3g ${FREENAS_ROOTFS}/usr/local/bin
	@${CP} -pv /usr/local/lib/libfuse.so.2 ${FREENAS_ROOTFS}/usr/local/lib

.include "bsd.freenas.mk"
