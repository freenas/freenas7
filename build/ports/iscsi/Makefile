PORTNAME=			iscsi
PORTVERSION=	2.0.92
CATEGORIES=		sysutils
COMMENT=			iSCSI tools
MAINTAINER=		votdev@gmx.de

MASTER_SITES=	ftp://ftp.cs.huji.ac.il/users/danny/freebsd/

do-build:
	@cd ${WRKDIR}/sys && ln -sv /sys/kern . && ln -sv /sys/tools .
	@cd ${WRKDIR}/sys/modules/iscsi_initiator && ln -sv ../.. @

	@${MAKE} -C ${WRKDIR}/sys/modules/iscsi_initiator
	@${MAKE} -C ${WRKDIR}/iscontrol

do-install:
	@${INSTALL_PROGRAM} -v ${WRKDIR}/iscontrol/iscontrol ${FREENAS_ROOTFS}/usr/local/sbin
	@${INSTALL_KLD} -v ${WRKDIR}/sys/modules/iscsi_initiator/iscsi_initiator.ko ${FREENAS_ROOTFS}/boot/kernel

.include <bsd.port.mk>
