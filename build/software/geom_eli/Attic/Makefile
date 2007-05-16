PKGNAME=	geom_eli
COMMENT=	GEOM Eli filesystem encryption
MAINTAINER=	votdev@gmx.de

SRCDIR=	/usr/src/sbin/geom/class/eli

do-build:
	# Patch geom eli sources if necessary.
	-@if [ ! -e $(SRCDIR)/geom_eli.c.orig ]; then \
		${PATCH} -f $(SRCDIR)/geom_eli.c ${FILESDIR}/patch-geom_eli.c; \
	fi

	@${MAKE} -C $(SRCDIR)

do-install:
	@${MAKE} -C $(SRCDIR) install

	@${INSTALL_PROGRAM} /sbin/geli ${FREENAS_ROOTFS}/sbin
	@${INSTALL_PROGRAM} /lib/geom/geom_eli.so ${FREENAS_ROOTFS}/lib/geom

do-clean:
	@${MAKE} -C $(SRCDIR) clean

.include "bsd.freenas.mk"
