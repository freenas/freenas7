PORTNAME=		istgt
PORTVERSION=		20100522
CATEGORIES=		net
COMMENT=		Implementation of userland ISCSI target
MAINTAINER=		aoyama@peach.ne.jp

MASTER_SITES=		http://www.peach.ne.jp/archives/istgt/

GNU_CONFIGURE=	yes
CONFIGURE_ARGS+= --with-logfacility="daemon" --with-logpriority="info"

do-install:
	@${INSTALL_PROGRAM} -v ${WRKSRC}/src/istgt ${FREENAS_ROOTFS}/${PREFIX}/bin
	@${INSTALL_PROGRAM} -v ${WRKSRC}/src/istgtcontrol ${FREENAS_ROOTFS}/${PREFIX}/bin
	@${INSTALL_SCRIPT} -v ${FILESDIR}/iscsi_target.in ${FREENAS_ROOTFS}/etc/rc.d/iscsi_target

.include <bsd.port.mk>
