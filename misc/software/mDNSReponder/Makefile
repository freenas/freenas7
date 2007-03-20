PKGNAME=	mDNSResponder
COMMENT=	Apple's mDNSResponder
MAINTAINER=	votdev@gmx.de

BUILD_DEPENDS=	mDNSResponder:${PORTSDIR}/net/mDNSResponder

do-install:
	@${INSTALL_PROGRAM} ${PORTSDIR}/net/mDNSResponder/work/mDNSResponder-*/mDNSPosix/build/prod/mDNSResponderPosix $(FREENAS)/usr/local/sbin

.include "bsd.freenas.mk"
