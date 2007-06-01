PORTNAME=			geom_raid5
PORTVERSION=	1.0
CATEGORIES=		sysutils
COMMENT=			GEOM RAID5 filesystem
MAINTAINER=		votdev@gmx.de

do-build:
	@${TAR} -zxvf ${FILESDIR}/geom_raid5.tbz -C /usr/src

	# Make kernel module.
	@${MAKE} -C /usr/src/sys/modules/geom/geom_raid5 depend
	@${MAKE} -C /usr/src/sys/modules/geom/geom_raid5

	# Make application.
	@${MKDIR} -v /usr/include/geom/raid5
	@${CP} -pv /usr/src/sys/geom/raid5/g_raid5.h /usr/include/geom/raid5
	@${MAKE} -C /usr/src/sbin/geom/class/raid5 depend
	@${MAKE} -C /usr/src/sbin/geom/class/raid5

do-install:
	@${CP} -pv /usr/src/sys/modules/geom/geom_raid5/geom_raid5.ko ${FREENAS_ROOTFS}/boot/kernel
	
	# How to create application binary without this command? Any ideas?
	@${MAKE} -C /usr/src/sbin/geom/class/raid5 install
	
	@${INSTALL_PROGRAM} /sbin/graid5 ${FREENAS_ROOTFS}/sbin
	@${CP} -pv /usr/src/sbin/geom/class/raid5/geom_raid5.so ${FREENAS_ROOTFS}/lib/geom

.include "bsd.freenas.mk"
