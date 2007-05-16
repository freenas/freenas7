PORTNAME=			iscsi
PORTVERSION=	2.0.1
CATEGORIES=		sysutils
COMMENT=			iSCSI tools
MAINTAINER=		votdev@gmx.de

MASTER_SITES=	ftp://ftp.cs.huji.ac.il/users/danny/freebsd/
USE_BZIP2=		yes

do-build:
	@cd ${WRKDIR}/sys && ln -sv /sys/kern . && ln -sv /sys/tools .
	@cd ${WRKDIR}/sys/modules/iscsi_initiator && ln -sv ../.. @

	@${MAKE} -C ${WRKDIR}/sys/modules/iscsi_initiator
	@${MAKE} -C ${WRKDIR}/iscontrol

do-install:
	@${INSTALL_PROGRAM} ${WRKDIR}/iscontrol/iscontrol ${FREENAS_ROOTFS}/usr/local/sbin
	@${CP} -pv ${WRKDIR}/sys/modules/iscsi_initiator/iscsi_initiator.ko ${FREENAS_ROOTFS}/boot/kernel

.include "bsd.freenas.mk"
