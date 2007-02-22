PORTDIR=	/usr/ports/sysutils/ataidle

build:	clean
	make -C $(PORTDIR)

install:
	install -vs $(PORTDIR)/work/ataidle*/ataidle $(FREENAS)/usr/local/sbin

clean:
	make -C $(PORTDIR) clean
