SRCDIR=	/usr/src/sbin/geom/class/eli

do-build:	clean
	# Patch geom eli sources if necessary.
	-@if [ ! -e $(SRCDIR)/geom_eli.c.orig ]; then \
		patch -f $(SRCDIR)/geom_eli.c ${FILESDIR}/patch-geom_eli.c; \
	fi

	make -C $(SRCDIR)

do-install:
	make -C $(SRCDIR) install

	install -vs /sbin/geli $(FREENAS)/sbin
	install -vs /lib/geom/geom_eli.so $(FREENAS)/lib/geom

do-clean:
	make -C $(SRCDIR) clean

.include "bsd.freenas.mk"
