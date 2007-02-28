BUILD_DEPENDS=	fusefs-ntfs:${PORTSDIR}/sysutils/fusefs-ntfs
RUN_DEPENDS=	${LOCALBASE}/modules/fuse.ko:${PORTSDIR}/sysutils/fusefs-kmod
BUILD_DEPENDS=	fusefs-libs>=2.6.0:${PORTSDIR}/sysutils/fusefs-libs

pre-depends:
# Copy options file
	@${MKDIR} -v /var/db/ports/fusefs
	@${CP} -pv ${FILESDIR}/options /var/db/ports/fusefs

install:
	@${INSTALL_PROGRAM} ${PORTSDIR}/sysutils/fusefs-kmod/work/fuse4bsd*/mount_fusefs/mount_fusefs $(FREENAS)/usr/local/sbin
	@${CP} -pv ${PORTSDIR}/sysutils/fusefs-kmod/work/fuse4bsd*/fuse_module/fuse.ko $(FREENAS)/boot/kernel
	@${CP} -pv ${PORTSDIR}/sysutils/fusefs-ntfs/work/ntfs-3g*/libntfs-3g/.libs/libntfs-3g.so $(FREENAS)/usr/local/lib/libntfs-3g.so.0

	@${INSTALL_PROGRAM} ${PORTSDIR}/sysutils/fusefs-ntfs/work/ntfs-3g*/src/.libs/ntfs-3g $(FREENAS)/usr/local/bin
	@${INSTALL_PROGRAM} /usr/local/lib/libfuse.so.2 $(FREENAS)/usr/local/lib

.include "bsd.freenas.mk"
