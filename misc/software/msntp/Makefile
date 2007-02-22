PORTDIR=	/usr/ports/net/msntp

build:	clean
	make -C $(PORTDIR)

install:
	install -vs $(PORTDIR)/work/msntp*/msntp $(FREENAS)/usr/local/bin

	cp -pv ${.CURDIR}/files/runmsntp.sh $(FREENAS)/usr/local/bin
	chmod +x $(FREENAS)/usr/local/bin/runmsntp.sh

clean:
	make -C $(PORTDIR) clean
