PKGNAME=	aaccli
COMMENT=	Adaptec SCSI RAID administration tool
MAINTAINER=	votdev@gmx.de

BUILD_DEPENDS=	aaccli:${PORTSDIR}/sysutils/aaccli

do-install:
	@${TAR} -zxvf ${PORTSDIR}/sysutils/aaccli/work/aaccli-1.0_0.tgz -C ${PORTSDIR}/sysutils/aaccli/work
	@${INSTALL_PROGRAM} ${PORTSDIR}/sysutils/aaccli/work/bin/aaccli ${FREENAS_ROOTFS}/usr/local/bin

.include "bsd.freenas.mk"
