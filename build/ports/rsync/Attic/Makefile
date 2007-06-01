PKGNAME=	rsync
COMMENT=	A network file distribution/sync utility
MAINTAINER=	votdev@gmx.de

ARCHIVE=	rsync-2.6.9.tar.gz
URL=	http://samba.anu.edu.au/ftp/rsync/$(ARCHIVE)
SRCDIR=	${FREENAS_WORKINGDIR}/$$(basename $(ARCHIVE) .tar.gz)

do-build:
	-@if [ ! -f ${FREENAS_DISTFILES}/$(ARCHIVE) ]; then \
		cd ${FREENAS_DISTFILES} && fetch $(URL); \
	fi

	${TAR} -zxvf ${FREENAS_DISTFILES}/$(ARCHIVE) -C ${FREENAS_WORKINGDIR}

	@cd $(SRCDIR) && ./configure --with-rsyncd-conf=/var/etc
	@${MAKE} -C $(SRCDIR)

do-install:
	@${INSTALL_PROGRAM} $(SRCDIR)/rsync ${FREENAS_ROOTFS}/usr/local/bin

do-clean:
	@${RM} -f -r $(SRCDIR)

.include "bsd.freenas.mk"
