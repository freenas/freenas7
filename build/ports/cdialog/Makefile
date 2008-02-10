PORTNAME=			cdialog
PORTVERSION=	1.1.20071028
PORTEPOCH=		1
CATEGORIES=		devel
COMMENT=			An enhanced version of 'dialog' to work with ncurses
MAINTAINER=		votdev@gmx.de

MASTER_SITES=	ftp://invisible-island.net/dialog/ \
							http://www.carpetsmoker.net/distfiles/ \
							http://www.xs4all.nl/~tournoij/distfiles/
DISTNAME=			${PORTNAME:S|^c||}-${PORTVERSION:R}-${PORTVERSION:E}

EXTRACT_SUFX=			.tgz
GNU_CONFIGURE=		yes
CONFIGURE_ARGS=		--includedir=${PREFIX}/include/${PORTNAME}
CONFIGURE_TARGET=	--build=${MACHINE_ARCH}-portbld-freebsd${OSREL}
MAKEFILE=					makefile

post-patch:
	@${FIND} ${WRKSRC}/samples/ -type f -exec ${REINPLACE_CMD} -i "" "s|dialog|cdialog|g" {} \;

do-install:
	@${INSTALL_PROGRAM} -v ${WRKSRC}/dialog ${FREENAS_ROOTFS}/usr/local/bin/cdialog

.include <bsd.port.pre.mk>

.if ${OSVERSION} > 700032 || (${OSVERSION} < 700000 && ${OSVERSION} > 602106)
CONFIGURE_ARGS+=	--with-ncursesw --enable-widec
.else
CONFIGURE_ARGS+=	--with-ncurses
.endif

.include <bsd.port.post.mk>
