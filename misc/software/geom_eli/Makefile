.include "bsd.freenas.mk"

SRCDIR=	/usr/src/sbin/geom/class/eli

build:	clean
	# Patch geom eli sources if necessary.
	-@if [ ! -e $(SRCDIR)/geom_eli.c.orig ]; then \
		@patch -f $(SRCDIR)/geom_eli.c ${FILESDIR}/patch-geom_eli.c; \
	fi

	make -C $(SRCDIR)

install:
	make -C $(SRCDIR) install

	install -vs /sbin/geli $(FREENAS)/sbin
	install -vs /lib/geom/geom_eli.so $(FREENAS)/lib/geom

clean:
	make -C $(SRCDIR) clean
