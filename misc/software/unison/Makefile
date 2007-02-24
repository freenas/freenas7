.include "bsd.freenas.mk"

PORTDIR=	/usr/ports/net/unison

build:	clean
	# Copy ocaml options file
	mkdir -pv /var/db/ports/ocaml
	cp -pv ${FILESDIR}/ocaml/options /var/db/ports/ocaml

	make -C $(PORTDIR)

install:
	install -vs $(PORTDIR)/work/unison-*/unison $(FREENAS)/usr/local/bin

clean:
	make -C $(PORTDIR) clean
