PORTNAME=	ataidle
PORTVERSION=	2.6
CATEGORIES=	sysutils
COMMENT=	Utility to set spindown timeout for ATA drives
MAINTAINER=	aoyama@peach.ne.jp

MASTER_SITES=	http://www.cran.org.uk/bruce/software/

do-install:
	@${INSTALL_SCRIPT} -v ${FILESDIR}/${PORTNAME}.in ${FREENAS_ROOTFS}/etc/rc.d/${PORTNAME}
	@${INSTALL_PROGRAM} -v ${WRKSRC}/ataidle ${FREENAS_ROOTFS}/usr/local/sbin

.include <bsd.port.mk>
