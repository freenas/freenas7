PORTDIR=	/usr/ports/sysutils/smartmontools

all:	clean
	make -C $(PORTDIR)

install:
	install -vs $(PORTDIR)/work/smartmontools-*/smartctl $(FREENAS)/usr/local/sbin
	install -vs $(PORTDIR)/work/smartmontools-*/smartd $(FREENAS)/usr/local/sbin

clean:
	make -C $(PORTDIR) clean
