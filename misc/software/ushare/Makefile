PORTDIR=	/usr/ports/net/ushare

all:	clean
	make -C $(PORTDIR)

install:
	install -vs $(PORTDIR)/work/ushare-*/src/ushare $(FREENAS)/usr/local/bin

clean:
	make -C $(PORTDIR) clean
