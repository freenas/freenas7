PORTNAME=			rsync
PORTVERSION=	2.6.9
CATEGORIES=		net
COMMENT=			A network file distribution/sync utility
MAINTAINER=		votdev@gmx.de

MASTER_SITES=	http://rsync.samba.org/ftp/%SUBDIR%/ \
							ftp://ftp.samba.org/pub/%SUBDIR%/
MASTER_SITE_SUBDIR=	rsync

GNU_CONFIGURE=		yes
CONFIGURE_ARGS+=	--with-rsyncd-conf=/var/etc

do-install:
	@${INSTALL_PROGRAM} ${WRKSRC}/rsync ${FREENAS_ROOTFS}/usr/local/bin

.include "bsd.freenas.mk"
