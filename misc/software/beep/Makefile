PORTDIR=	/usr/ports/audio/beep

all:	clean
	make -C $(PORTDIR)

install:
	install -vs $(PORTDIR)/work/beep/beep $(FREENAS)/usr/local/bin

clean:
	make -C $(PORTDIR) clean
