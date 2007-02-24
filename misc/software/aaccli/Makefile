PORTDIR=	/usr/ports/sysutils/aaccli

build:	clean
	make -C $(PORTDIR)

install:
	tar zxvf $(PORTDIR)/work/aaccli-1.0_0.tgz -C $(PORTDIR)/work
	install -vs $(PORTDIR)/work/bin/aaccli $(FREENAS)/usr/local/bin

clean:
	make -C $(PORTDIR) clean
