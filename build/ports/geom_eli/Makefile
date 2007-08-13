PORTNAME=			geom_eli
PORTVERSION=	1.0
PORTREVISION=	0
PORTEPOCH=		3
CATEGORIES=		sysutils
COMMENT=			GEOM Eli filesystem encryption
MAINTAINER=		votdev@gmx.de

WRKSRC=	/usr/src/sbin/geom/class/eli

do-build:
	-@if [ -e ${WRKSRC}/geom_eli.c.orig ]; then \
		${MV} ${WRKSRC}/geom_eli.c.orig ${WRKSRC}/geom_eli.c; \
	fi

	# Patch geom eli sources.
	${PATCH} -f ${WRKSRC}/geom_eli.c ${FILESDIR}/geom_eli.c.diff

	@${MAKE} -C ${WRKSRC}

do-install:
	# Install kernel module.
	@${CP} -pv ${MAKEOBJDIRPREFIX}/usr/src/sys/FREENAS-${ARCH}/modules/usr/src/sys/modules/geom/geom_eli/geom_eli.ko ${FREENAS_ROOTFS}/boot/kernel

	# Install application.
	@${LN} -f -v -s /sbin/geom ${FREENAS_ROOTFS}/sbin/geli
	@${CP} -pv /usr/src/sbin/geom/class/eli/geom_eli.so ${FREENAS_ROOTFS}/lib/geom

pre-clean:
	@${MAKE} -C ${WRKSRC} clean

.include "bsd.freenas.mk"
