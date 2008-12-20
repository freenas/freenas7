PORTNAME=			pure-ftpd
PORTVERSION=	1.0.21
CATEGORIES=		ftp
COMMENT=			A fast and very secure FTP server
MAINTAINER=		votdev@gmx.de

MASTER_SITES=				${MASTER_SITE_SOURCEFORGE}
MASTER_SITE_SUBDIR=	pureftpd

USE_BZIP2=	yes
USE_ICONV=	yes

PUREFTPD_CERTFILE=	/var/etc/ssl/private/pure-ftpd.pem

GNU_CONFIGURE=		yes
CONFIGURE_ARGS+=	--with-rfc2640 \
									--with-largefile \
									--with-pam \
									--with-ftpwho \
									--with-tls \
									--with-certfile="${PUREFTPD_CERTFILE}" \
									--with-throttling \
									--without-inetd

do-install:
	@${INSTALL_SCRIPT} -v ${FILESDIR}/pureftpd.in ${FREENAS_ROOTFS}/etc/rc.d/pureftpd

	@${INSTALL_PROGRAM} -v ${WRKSRC}/src/pure-ftpd ${FREENAS_ROOTFS}/usr/local/sbin
	@${INSTALL_PROGRAM} -v ${WRKSRC}/src/pure-ftpwho ${FREENAS_ROOTFS}/usr/local/sbin

.include <bsd.port.mk>
