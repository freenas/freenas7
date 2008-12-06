PORTNAME=			nano
PORTVERSION=	2.0.9
CATEGORIES=		editors
COMMENT=			Nano's ANOther editor, an enhanced free Pico clone
MAINTAINER=		votdev@gmx.de

MASTER_SITES=	http://www.nano-editor.org/dist/v2.0/ \
							http://www.ewtoo.org/~astyanax/nano/dist/v2.0/

GNU_CONFIGURE=	yes
USE_GETTEXT=		yes
CONFIGURE_ARGS=	--enable-all
CONFIGURE_ENV=	CPPFLAGS="-I${LOCALBASE}/include" \
								LDFLAGS="-L${LOCALBASE}/lib"

.include <bsd.port.pre.mk>

# requires wide character curses
.if (${OSVERSION} < 602107) || (${OSVERSION} >= 700000 && ${OSVERSION} < 700033)
CONFIGURE_ARGS+=--disable-utf8
.else
CONFIGURE_ARGS+=--enable-utf8
.endif

do-install:
	${INSTALL_PROGRAM} -v ${WRKSRC}/src/nano ${FREENAS_ROOTFS}/usr/local/bin;

.include <bsd.port.post.mk>
