PORTNAME=			pure-ftpd
PORTVERSION=	1.0.21
CATEGORIES=		ftp
COMMENT=			A fast and very secure FTP server
MAINTAINER=		votdev@gmx.de

MASTER_SITES=				${MASTER_SITE_SOURCEFORGE}
MASTER_SITE_SUBDIR=	pureftpd
USE_BZIP2=					yes

GNU_CONFIGURE=		yes
CONFIGURE_ARGS+=	--with-rfc2640 \
									--with-largefile \
									--with-pam \
									--with-ftpwho

do-install:
	@${INSTALL_PROGRAM} ${WRKSRC}/src/pure-ftpd ${FREENAS_ROOTFS}/usr/local/sbin
	@${INSTALL_PROGRAM} ${WRKSRC}/src/pure-ftpwho ${FREENAS_ROOTFS}/usr/local/sbin

.include "bsd.freenas.mk"
