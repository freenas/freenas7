PORTNAME=			geom_eli
PORTVERSION=	1.0
PORTREVISION=	0
PORTEPOCH=		4
CATEGORIES=		sysutils
COMMENT=			GEOM Eli filesystem encryption
MAINTAINER=		votdev@gmx.de

DISTFILES?=
WRKSRC=	/usr/src/sbin/geom/class/eli

do-build:
	-@if [ -e ${WRKSRC}/geom_eli.c.orig ]; then \
		${MV} ${WRKSRC}/geom_eli.c.orig ${WRKSRC}/geom_eli.c; \
	fi

	# Patch geom eli sources.
	${PATCH} -f ${WRKSRC}/geom_eli.c ${FILESDIR}/geom_eli.c.diff

	@${MAKE} -C ${WRKSRC}

do-install:
	# Install application.
	@${CP} -pv /usr/src/sbin/geom/class/eli/geom_eli.so ${FREENAS_ROOTFS}/lib/geom

pre-clean:
	@${MAKE} -C ${WRKSRC} clean

.include <bsd.port.mk>
