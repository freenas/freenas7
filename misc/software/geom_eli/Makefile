DIR=	/usr/src/sbin/geom/class/eli

build:	clean
	# Patch geom eli sources if necessary.
	-@if [ ! -e $(DIR)/geom_eli.c.orig ]; then \
		@patch -f $(DIR)/geom_eli.c ${.CURDIR}/files/patch-geom_eli.c; \
	fi

	make -C $(DIR)

install:
	make -C $(DIR) install

	install -vs /sbin/geli $(FREENAS)/sbin
	install -vs /lib/geom/geom_eli.so $(FREENAS)/lib/geom

clean:
	make -C $(DIR) clean
