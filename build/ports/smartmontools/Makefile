PORTNAME=			smartmontools
PORTVERSION=	5.38
CATEGORIES=		sysutils
COMMENT=	S.M.A.R.T. disk monitoring tools
MAINTAINER=		votdev@gmx.de

MASTER_SITES=				${MASTER_SITE_SOURCEFORGE}
MASTER_SITE_SUBDIR=	${PORTNAME}

USE_GMAKE=				yes
GNU_CONFIGURE=		yes

post-patch:
	@${REINPLACE_CMD} -e 's| install-initdDATA | |' ${WRKSRC}/Makefile.in

do-install:
	@${INSTALL_PROGRAM} -v ${WRKSRC}/smartctl ${FREENAS_ROOTFS}/usr/local/sbin
	@${INSTALL_PROGRAM} -v ${WRKSRC}/smartd ${FREENAS_ROOTFS}/usr/local/sbin

.include <bsd.port.mk>
