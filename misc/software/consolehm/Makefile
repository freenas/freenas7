PORTDIR=	/usr/ports/sysutils/consolehm

all:	clean
	make -C $(PORTDIR)

install:
	install -vs $(PORTDIR)/work/consolehm*/consolehm/chm $(FREENAS)/usr/local/bin

clean:
	make -C $(PORTDIR) clean
