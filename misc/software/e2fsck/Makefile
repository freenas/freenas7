PORTDIR=	/usr/ports/sysutils/e2fsprogs

build:	clean
	make -C $(PORTDIR)

install:
	install -vs $(PORTDIR)/work/e2fsprogs-*/e2fsck/e2fsck $(FREENAS)/usr/local/sbin

clean:
	make -C $(PORTDIR) clean
