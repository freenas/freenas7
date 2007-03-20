PKGNAME=	consolehm
COMMENT=	Console based hardware monitor
MAINTAINER=	votdev@gmx.de

BUILD_DEPENDS=	consolehm:${PORTSDIR}/sysutils/consolehm

do-install:
	@${INSTALL_PROGRAM} ${PORTSDIR}/sysutils/consolehm/work/consolehm*/consolehm/chm $(FREENAS)/usr/local/bin

.include "bsd.freenas.mk"
