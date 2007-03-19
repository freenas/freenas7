BUILD_DEPENDS=	e2fsprogs:${PORTSDIR}/sysutils/e2fsprogs

do-install:
	@${INSTALL_PROGRAM} ${PORTSDIR}/sysutils/e2fsprogs/work/e2fsprogs-*/e2fsck/e2fsck $(FREENAS)/usr/local/sbin
	@${INSTALL_PROGRAM} ${PORTSDIR}/sysutils/e2fsprogs/work/e2fsprogs-*/misc/mke2fs $(FREENAS)/usr/local/sbin

.include "bsd.freenas.mk"
