PORTNAME=			mDNSResponder
PORTVERSION=	1.0
CATEGORIES=		net
COMMENT=			Apple's mDNSResponder
MAINTAINER=		votdev@gmx.de

BUILD_DEPENDS=	mDNSResponder:${PORTSDIR}/net/mDNSResponder

pre-build:
# Copy modified 'dns-ds' Makefile
	@${CP} -pv ${FILESDIR}/Makefile ${PORTSDIR}/net/mDNSResponder/work/mDNSResponder-*/Clients

do-build:
	@${GMAKE} -C ${PORTSDIR}/net/mDNSResponder/work/mDNSResponder-*/Clients

do-install:
	@${INSTALL_PROGRAM} ${PORTSDIR}/net/mDNSResponder/work/mDNSResponder-*/mDNSPosix/build/prod/mDNSResponderPosix ${FREENAS_ROOTFS}/usr/local/sbin
	@${INSTALL_PROGRAM} ${PORTSDIR}/net/mDNSResponder/work/mDNSResponder-*/Clients/build/dns-sd ${FREENAS_ROOTFS}/usr/local/sbin
	@${INSTALL_PROGRAM} ${PORTSDIR}/net/mDNSResponder/work/mDNSResponder-*/mDNSPosix/build/prod/libdns_sd.so ${FREENAS_ROOTFS}/usr/local/lib

.include "bsd.freenas.mk"
