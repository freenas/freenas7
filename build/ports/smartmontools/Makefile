PORTNAME=			smartmontools
PORTVERSION=	5.38
CATEGORIES=		sysutils
COMMENT=			S.M.A.R.T. disk monitoring tools
MAINTAINER=		votdev@gmx.de

MASTER_SITES=		SF

USE_GMAKE=			yes
GNU_CONFIGURE=	yes

CFLAGS:=				${CFLAGS:S/-O2/-O/}

post-patch:
	@${REINPLACE_CMD} -e 's| install-initdDATA | |' ${WRKSRC}/Makefile.in

do-install:
	@${INSTALL_PROGRAM} -v ${WRKSRC}/smartctl ${FREENAS_ROOTFS}/usr/local/sbin
	@${INSTALL_PROGRAM} -v ${WRKSRC}/smartd ${FREENAS_ROOTFS}/usr/local/sbin

.include <bsd.port.mk>
