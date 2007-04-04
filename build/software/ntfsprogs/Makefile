PKGNAME=	ntfsprogs
COMMENT=	Utilities to manipulaite NTFS filesystems
MAINTAINER=	FreeNAS team

BUILD_DEPENDS=	ntfsprogs:${PORTSDIR}/sysutils/ntfsprogs

do-install:
	@${INSTALL_PROGRAM} ${PORTSDIR}/sysutils/ntfsprogs/work/ntfsprogs-*/ntfsprogs/.libs/ntfsfix $(FREENAS)/usr/local/bin
	@${CP} -pv ${PORTSDIR}/sysutils/ntfsprogs/work/ntfsprogs-*/libntfs/.libs/libntfs.so.9 $(FREENAS)/usr/local/lib/libntfs.so.9

.include "bsd.freenas.mk"
