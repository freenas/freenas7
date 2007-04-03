PKGNAME=	mDNSResponder
COMMENT=	Apple's mDNSResponder
MAINTAINER=	votdev@gmx.de

BUILD_DEPENDS=	mDNSResponder:${PORTSDIR}/net/mDNSResponder

pre-build:
# Copy modified 'dns-ds' Makefile
	@${CP} -pv ${FILESDIR}/Makefile ${PORTSDIR}/net/mDNSResponder/work/mDNSResponder-*/Clients

do-build:
	@${GMAKE} -C ${PORTSDIR}/net/mDNSResponder/work/mDNSResponder-*/Clients

do-install:
	@${INSTALL_PROGRAM} ${PORTSDIR}/net/mDNSResponder/work/mDNSResponder-*/mDNSPosix/build/prod/mDNSResponderPosix $(FREENAS)/usr/local/sbin
	@${INSTALL_PROGRAM} ${PORTSDIR}/net/mDNSResponder/work/mDNSResponder-*/Clients/build/dns-sd $(FREENAS)/usr/local/sbin
	@${INSTALL_PROGRAM} ${PORTSDIR}/net/mDNSResponder/work/mDNSResponder-*/mDNSPosix/build/prod/libdns_sd.so $(FREENAS)/usr/local/lib

.include "bsd.freenas.mk"
