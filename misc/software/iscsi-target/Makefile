PORTDIR=	/usr/ports/net/iscsi-target

build:	clean
	make -C $(PORTDIR)

install:
	install -vs $(PORTDIR)/work/netbsd-iscsi-*/bin/iscsi-target $(FREENAS)/usr/local/bin/

clean:
	make -C $(PORTDIR) clean
