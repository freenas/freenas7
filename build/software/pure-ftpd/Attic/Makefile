PKGNAME=	pureftpd
COMMENT=	A fast and very secure FTP server
MAINTAINER=	votdev@gmx.de

ARCHIVE=	pure-ftpd-1.0.21.tar.gz
URL=	ftp://ftp.pureftpd.org/pub/pure-ftpd/releases/$(ARCHIVE)
SRCDIR=	${FREENAS_WORKINGDIR}/$$(basename $(ARCHIVE) .tar.gz)

do-build:
	-@if [ ! -f ${FREENAS_DISTFILES}/$(ARCHIVE) ]; then \
		cd ${FREENAS_DISTFILES} && fetch $(URL); \
	fi

	${TAR} -zxvf ${FREENAS_DISTFILES}/$(ARCHIVE) -C ${FREENAS_WORKINGDIR}

	@cd $(SRCDIR) && ./configure --with-rfc2640 --with-largefile --with-pam --with-ftpwho
	@${MAKE} -C $(SRCDIR)

do-install:
	@${INSTALL_PROGRAM} $(SRCDIR)/src/pure-ftpd ${FREENAS_ROOTFS}/usr/local/sbin
	@${INSTALL_PROGRAM} $(SRCDIR)/src/pure-ftpwho ${FREENAS_ROOTFS}/usr/local/sbin

do-clean:
	@${RM} -f -r $(SRCDIR)

.include "bsd.freenas.mk"
