PORTNAME=			ataidle
PORTVERSION=	1.0
PORTREVISION=	1
CATEGORIES=		sysutils
COMMENT=			Utility to set spindown timeout for ATA drives
MAINTAINER=		votdev@gmx.de

MASTER_SITES=	http://www.cran.org.uk/bruce/software/

do-install:
	@${INSTALL_PROGRAM} -v ${WRKSRC}/ataidle ${FREENAS_ROOTFS}/usr/local/sbin

.include <bsd.port.mk>
