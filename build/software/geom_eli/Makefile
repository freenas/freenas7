PORTNAME=			geom_eli
PORTVERSION=	1.0
CATEGORIES=		sysutils
COMMENT=			GEOM Eli filesystem encryption
MAINTAINER=		votdev@gmx.de

WRKSRC=	/usr/src/sbin/geom/class/eli

do-build:
# Patch geom eli sources if necessary.
	-@if [ ! -e ${WRKSRC}/geom_eli.c.orig ]; then \
		${PATCH} -f ${WRKSRC}/geom_eli.c ${FILESDIR}/geom_eli.c.diff; \
	fi

	@${MAKE} -C ${WRKSRC}

do-install:
	@${MAKE} -C ${WRKSRC} install

	@${INSTALL_PROGRAM} /sbin/geli ${FREENAS_ROOTFS}/sbin
	@${INSTALL_PROGRAM} /lib/geom/geom_eli.so ${FREENAS_ROOTFS}/lib/geom

pre-clean:
	@${MAKE} -C ${WRKSRC} clean

.include "bsd.freenas.mk"
