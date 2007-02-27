PORTDIR=	/usr/ports/editors/nano

build:	clean
	make -C $(PORTDIR)

install:
.if ${ARCH} == "amd64" || ${ARCH} =="ia64"
	install -vs $(PORTDIR)/work/nano*/nano $(FREENAS)/usr/local/bin
.else
	install -vs $(PORTDIR)/work/nano*/src/nano $(FREENAS)/usr/local/bin
.endif

clean:
	make -C $(PORTDIR) clean

.include "bsd.freenas.mk"
