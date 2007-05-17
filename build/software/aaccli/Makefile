PORTNAME=			aaccli
PORTVERSION=	1.0
CATEGORIES=		sysutils
COMMENT=			Adaptec SCSI RAID administration tool
MAINTAINER=		votdev@gmx.de

BUILD_DEPENDS=	aaccli:${PORTSDIR}/sysutils/aaccli
# No build is required for this port.
NO_BUILD?=			1

do-install:
	@${TAR} -zxf ${PORTSDIR}/sysutils/aaccli/work/aaccli-*.tgz -C ${PORTSDIR}/sysutils/aaccli/work
	@${INSTALL_PROGRAM} -v ${PORTSDIR}/sysutils/aaccli/work/bin/aaccli ${FREENAS_ROOTFS}/usr/local/bin

.include "bsd.freenas.mk"
