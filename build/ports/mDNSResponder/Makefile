PORTNAME=		mDNSResponder
PORTVERSION=		333.10
CATEGORIES=		net dns
COMMENT=		Apple's mDNSResponder
MAINTAINER=		aoyama@peach.ne.jp

MASTER_SITES=	http://www.opensource.apple.com/tarballs/mDNSResponder/
DISTNAME=		${PORTNAME}-${PORTVERSION}

USE_LDCONFIG=		YES
USE_GMAKE=		YES
MAKE_ARGS+=		os=freebsd LOCALBASE=${PREFIX}
BUILD_WRKSRC=		${WRKSRC}/mDNSPosix
CONFLICTS=		avahi-libdns-[0-9]*

.include <bsd.port.pre.mk>

pre-build:
	@${REINPLACE_CMD} -e 's|-lpthread|${PTHREAD_LIBS}|g' \
		-e 's|mDNSResponder.8|mDNSResponderPosix.8|g' \
		${WRKSRC}/mDNSPosix/Makefile

do-install:
	@${INSTALL_PROGRAM} -v ${BUILD_WRKSRC}/build/prod/mDNSResponderPosix ${FREENAS_ROOTFS}/usr/local/bin
	@${INSTALL_PROGRAM} -v ${BUILD_WRKSRC}/build/prod/mDNSIdentify ${FREENAS_ROOTFS}/usr/local/bin
	@${INSTALL_PROGRAM} -v ${BUILD_WRKSRC}/build/prod/mDNSNetMonitor ${FREENAS_ROOTFS}/usr/local/bin
	@${INSTALL_PROGRAM} -v ${BUILD_WRKSRC}/build/prod/libdns_sd.so ${FREENAS_ROOTFS}/usr/local/lib
	@${INSTALL_SCRIPT} -v ${FILESDIR}/mdnsresponder.in ${FREENAS_ROOTFS}/etc/rc.d/mdnsresponder

.include <bsd.port.post.mk>
