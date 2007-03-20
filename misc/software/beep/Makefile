PKGNAME=	beep
COMMENT=	Beeps a certain duration and pitch out of the PC Speaker
MAINTAINER=	votdev@gmx.de

BUILD_DEPENDS=	beep:${PORTSDIR}/audio/beep

do-install:
	@${INSTALL_PROGRAM} ${PORTSDIR}/audio/beep/work/beep/beep $(FREENAS)/usr/local/bin

.include "bsd.freenas.mk"
