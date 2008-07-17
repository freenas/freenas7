PORTNAME=			ctorrent
PORTVERSION=	3.3.2
CATEGORIES=		net-p2p
COMMENT=			BitTorrent client written in C for FreeBSD and Linux
MAINTAINER=		votdev@gmx.de

MASTER_SITES=	http://www.rahul.net/dholmes/ctorrent/
DISTNAME=			${PORTNAME}-dnh${PORTVERSION}

GNU_CONFIGURE=	yes
CONFIGURE_TARGET=	--build=${ARCH}-portbld-freebsd${OSREL}

do-install:
	@${INSTALL_PROGRAM} -v ${WRKSRC}/ctorrent ${FREENAS_ROOTFS}/usr/local/bin/ctorrent

.include <bsd.port.mk>
