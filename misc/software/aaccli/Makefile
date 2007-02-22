PORTDIR=	/usr/ports/sysutils/aaccli

all:	clean
	make -C $(PORTDIR)

install:
	tar zxvf $(PORTDIR)/work/aaccli-1.0_0.tgz
	install -vs $(PORTDIR)/bin/aaccli $(FREENAS)/usr/local/bin

clean:
	make -C $(PORTDIR) clean
