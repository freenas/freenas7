PORTNAME=			consolehm
PORTVERSION=	1.31
PORTREVISION=	1
CATEGORIES=		sysutils
COMMENT=			Console based hardware monitor
MAINTAINER=		votdev@gmx.de

MASTER_SITES=				${MASTER_SITE_LOCAL}
MASTER_SITE_SUBDIR=	fenner

WRKSRC=					${WRKDIR}/consolehm/consolehm
GNU_CONFIGURE=	yes

.include <bsd.port.pre.mk>

.if ${ARCH} == "ia64" || ${ARCH} == "sparc64"
BROKEN=		"Does not compile on ia64 or sparc64"
.endif

do-install:
	@${INSTALL_PROGRAM} -v ${WRKSRC}/chm ${FREENAS_ROOTFS}/usr/local/bin

.include <bsd.port.post.mk>
