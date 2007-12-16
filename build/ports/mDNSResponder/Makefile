PORTNAME=			mDNSResponder
PORTVERSION=	1.0
CATEGORIES=		net
COMMENT=			Apple's mDNSResponder
MAINTAINER=		votdev@gmx.de

# No files are needed to be fetched.
DISTFILES?=
# No build is required for this port.
NO_BUILD?=	yes
# Don't register a port installation as a package.
NO_PKG_REGISTER?=	yes
# Set default target to execute when a port is calling a dependency.
DEPENDS_TARGET=

BUILD_DEPENDS=	mDNSResponder:${PORTSDIR}/net/mDNSResponder

pre-build:
# Copy modified 'dns-ds' Makefile
	@${CP} -pv ${FILESDIR}/Makefile ${PORTSDIR}/net/mDNSResponder/work/mDNSResponder-*/Clients

do-build:
	@${GMAKE} -C ${PORTSDIR}/net/mDNSResponder/work/mDNSResponder-*/Clients

do-install:
	@${INSTALL_PROGRAM} -v ${PORTSDIR}/net/mDNSResponder/work/mDNSResponder-*/mDNSPosix/build/prod/mDNSResponderPosix ${FREENAS_ROOTFS}/usr/local/sbin
	@${INSTALL_PROGRAM} -v ${PORTSDIR}/net/mDNSResponder/work/mDNSResponder-*/Clients/build/dns-sd ${FREENAS_ROOTFS}/usr/local/sbin
	@${INSTALL_PROGRAM} -v ${PORTSDIR}/net/mDNSResponder/work/mDNSResponder-*/mDNSPosix/build/prod/libdns_sd.so ${FREENAS_ROOTFS}/usr/local/lib

.include <bsd.port.mk>
