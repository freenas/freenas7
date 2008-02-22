PORTNAME=			rsync
PORTVERSION=	2.6.9
PORTREVISION=	2
CATEGORIES=		net
COMMENT=			A network file distribution/sync utility
MAINTAINER=		votdev@gmx.de

MASTER_SITES=	http://rsync.samba.org/ftp/%SUBDIR%/ \
							ftp://ftp.samba.org/pub/%SUBDIR%/
MASTER_SITE_SUBDIR=	rsync

GNU_CONFIGURE=		yes
CONFIGURE_ARGS+=	--disable-debug \
									--enable-ipv6 \
									--with-rsyncd-conf=/var/etc
CONFIGURE_TARGET=	--build=${MACHINE_ARCH}-portbld-freebsd${OSREL}
PATCH_STRIP=			-p1

# Install on host system too, otherwise depending ports fail during ./configure process (e.g. scponly).
FORCE_PKG_REGISTER=	yes

do-install:
	@${INSTALL_PROGRAM} -v ${WRKSRC}/rsync ${FREENAS_ROOTFS}/usr/local/bin
# Install on host system too, otherwise depending ports fail during ./configure process (e.g. scponly). 
	@${INSTALL_PROGRAM} -v ${WRKSRC}/rsync ${PREFIX}/bin

.include <bsd.port.mk>
