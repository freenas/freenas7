PKGNAME=	sfdisk
COMMENT=	Standalone sysinstall's fdisk
MAINTAINER=	votdev@gmx.de

BUILD_DEPENDS=	sfdisk:${PORTSDIR}/sysutils/sfdisk

do-install:
	@${INSTALL_PROGRAM} ${PORTSDIR}/sysutils/sfdisk/work/sfdisk*/sfdisk $(FREENAS)/usr/local/sbin

.include "bsd.freenas.mk"
