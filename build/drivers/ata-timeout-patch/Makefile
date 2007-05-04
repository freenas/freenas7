ARCHIVE= ata-all.c.diff
SRCDIR=	${FREENAS_WORKINGDIR}/ata-timeout-patch

install:	clean
	@mkdir -pv $(SRCDIR)
	@cp -pv ${.CURDIR}/files/$(ARCHIVE) -C $(SRCDIR)
	@patch /usr/src/sys/dev/ata/ata-all.c $(SRCDIR)/$(ARCHIVE)

clean:
    @rm -f -r $(SRCDIR)
