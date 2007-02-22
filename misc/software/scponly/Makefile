PORTDIR=	/usr/ports/shells/scponly

all:	clean
	# Copy required options files
	mkdir -pv /var/db/ports/ocaml
	mkdir -pv /var/db/ports/rsync
	cp -pvr ${.CURDIR}/files/* /var/db/ports

	@export WITH_SCPONLY_RSYNC=YES
	@export WITH_SCPONLY_SCP=YES
	@export WITH_SCPONLY_WINSCP=YES
	@export WITH_SCPONLY_UNISON=YES

	make -C $(PORTDIR)

install:
	install -vs $(PORTDIR)/work/scponly-*/scponly $(FREENAS)/usr/local/bin/

clean:
	make -C $(PORTDIR) clean
