PORTDIR=	/usr/ports/editors/nano

build:	clean
	make -C $(PORTDIR)

install:
	install -vs $(PORTDIR)/work/nano*/nano $(FREENAS)/usr/local/bin

clean:
	make -C $(PORTDIR) clean
