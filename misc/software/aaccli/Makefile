PORTDIR=	/usr/ports/sysutils/aaccli

build:	clean
	make -C $(PORTDIR)

install:
	tar zxvf $(PORTDIR)/work/aaccli-1.0_0.tgz -C /usr/ports/sysutils/aaccli/work/
	install -vs $(PORTDIR)/work/bin/aaccli $(FREENAS)/usr/local/bin

clean:
	make -C $(PORTDIR) clean
