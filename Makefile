PORTNAME=	rrdtool
PORTVERSION=	1.2.26
CATEGORIES=	databases graphics
MASTER_SITES=	http://oss.oetiker.ch/rrdtool/pub/

MAINTAINER=	bg1tpt@gmail.com
COMMENT=	Round Robin Database Tools

LIB_DEPENDS=	freetype.9:${PORTSDIR}/print/freetype2 \
		art_lgpl_2.5:${PORTSDIR}/graphics/libart_lgpl \
		png.5:${PORTSDIR}/graphics/png

USE_AUTOTOOLS=	libtool:15
USE_LDCONFIG=	yes
GNU_CONFIGURE=	yes
USE_GMAKE=	yes

CONFIGURE_ARGS=	--disable-tcl

.include <bsd.port.pre.mk>

CONFIGURE_TARGET=--build=${MACHINE_ARCH}-portbld-freebsd${OSREL}
CPPFLAGS+=	-I${LOCALBASE}/include -I${LOCALBASE}/include/libart-2.0 -I${LOCALBASE}/include/freetype2
LDFLAGS+=	-L${LOCALBASE}/lib
CFLAGS:=	${CFLAGS:N-ffast-math}
CONFIGURE_ENV+=	CPPFLAGS="${CPPFLAGS}" LDFLAGS="${LDFLAGS}"

post-extract:
.if defined(NOPORTDOCS)
	@${REINPLACE_CMD} -e 's/install-idocDATA install-ihtmlDATA//g' \
		-e 's/^	cd .* rrdtool.html index.html/	#/' \
		${WRKSRC}/doc/Makefile.in
.endif

.include <bsd.port.post.mk>
