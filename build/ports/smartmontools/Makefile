PORTNAME=			smartmontools
PORTVERSION=	5.39.1
CATEGORIES=		sysutils
COMMENT=			S.M.A.R.T. disk monitoring tools
MAINTAINER=		ma.zoon@quicknet.nl

MASTER_SITES=		SF

CONFLICTS=		smartmontools-devel-[0-9]*

USE_GMAKE=			yes
# hack to prevent gmake failure on 6.xx
GMAKE=		make
GNU_CONFIGURE=	yes

CFLAGS:=			${CFLAGS:S/-O2/-O/} -Wno-write-strings

post-patch:
	@${REINPLACE_CMD} -e 's| install-initdDATA | |' ${WRKSRC}/Makefile.in

do-install:
	@${INSTALL_PROGRAM} -v ${WRKSRC}/smartctl ${FREENAS_ROOTFS}/usr/local/sbin
	@${INSTALL_PROGRAM} -v ${WRKSRC}/smartd ${FREENAS_ROOTFS}/usr/local/sbin

.include <bsd.port.mk>
