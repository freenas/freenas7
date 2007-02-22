PORTDIR=	/usr/ports/net/mDNSResponder

build:	clean
	make -C $(PORTDIR)

install:
	install -vs $(PORTDIR)/work/mDNSResponder-*/mDNSPosix/build/prod/mDNSResponderPosix $(FREENAS)/usr/local/sbin

clean:
	make -C $(PORTDIR) clean
