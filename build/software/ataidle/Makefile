PKGNAME=	ataidle
COMMENT=	Utility to set spindown timeout for ATA drives
MAINTAINER=	votdev@gmx.de

BUILD_DEPENDS=	ataidle:${PORTSDIR}/sysutils/ataidle

do-install:
	@${INSTALL_PROGRAM} ${PORTSDIR}/sysutils/ataidle/work/ataidle*/ataidle $(FREENAS)/usr/local/sbin

.include "bsd.freenas.mk"
