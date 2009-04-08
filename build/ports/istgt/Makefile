PORTNAME=				istgt
PORTVERSION=		20090326
CATEGORIES=			net
COMMENT=				Implementation of userland ISCSI target
MAINTAINER=			aoyama@peach.ne.jp

MASTER_SITES=		http://www.peach.ne.jp/archives/istgt/

GNU_CONFIGURE=	yes

do-install:
	@${INSTALL_PROGRAM} -v ${WRKSRC}/src/istgt ${FREENAS_ROOTFS}/${PREFIX}/bin
	@${INSTALL_PROGRAM} -v ${WRKSRC}/src/istgtcontrol ${FREENAS_ROOTFS}/${PREFIX}/bin

.include <bsd.port.mk>
