PORTNAME=			unison
PORTVERSION=	2.13.16
CATEGORIES=		net
COMMENT=			A user-level file synchronization tool
MAINTAINER=		votdev@gmx.de

MASTER_SITES=	http://www.cis.upenn.edu/~bcpierce/unison/download/releases/stable/
DISTFILES=		${EXTRACT_ONLY}
DIST_SUBDIR=	${PORTNAME}-${PORTVERSION}
EXTRACT_ONLY=	${PORTNAME}-${PORTVERSION}.tar.gz

BUILD_DEPENDS=	ocamlc:${PORTSDIR}/lang/ocaml-nox11
PATCH_DEPENDS=	${BUILD_DEPENDS}

USE_GMAKE=	yes
MAKE_ARGS=	CFLAGS=""
MAKE_ARGS+=	UISTYLE=text

pre-everything::
# Copy ocaml options file
	@${MKDIR} -v /var/db/ports/ocaml
	@${CP} -pv ${FILESDIR}/ocaml-options.in /var/db/ports/ocaml/options

do-install:
	@${INSTALL_PROGRAM} -v ${WRKSRC}/unison ${FREENAS_ROOTFS}/usr/local/bin

.include <bsd.port.mk>
