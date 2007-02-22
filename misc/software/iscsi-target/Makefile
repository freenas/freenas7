PORTDIR=	/usr/ports/net/iscsi-target

all:	clean
	make -C $(PORTDIR)

install:
	install -vs $(PORTDIR)/work/netbsd-iscsi-*/bin/iscsi-target $(FREENAS)/usr/local/bin/

clean:
	make -C $(PORTDIR) clean
