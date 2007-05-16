PKGNAME=	php
COMMENT=	PHP5 scripting language
MAINTAINER=	votdev@gmx.de

ARCHIVE=	php-5.2.2.tar.gz
URL=	http://de2.php.net/distributions/$(ARCHIVE)
SRCDIR=	${FREENAS_WORKINGDIR}/$$(basename $(ARCHIVE) .tar.gz)

LIB_DEPENDS=	xml2.5:${PORTSDIR}/textproc/libxml2

do-build:
	-@if [ ! -f ${FREENAS_DISTFILES}/$(ARCHIVE) ]; then \
		cd ${FREENAS_DISTFILES} && fetch $(URL); \
	fi

	-${TAR} -zxvf ${FREENAS_DISTFILES}/$(ARCHIVE) -C ${FREENAS_WORKINGDIR}

	@cd $(SRCDIR) && ./configure --enable-fastcgi --enable-discard-path --enable-force-cgi-redirect --without-mysql --without-pear --with-openssl --without-sqlite --with-pcre-regex --with-gettext
	@${MAKE} -C $(SRCDIR)

do-install:
	@${INSTALL_PROGRAM} $(SRCDIR)/sapi/cgi/php ${FREENAS_ROOTFS}/usr/local/bin
	@${CP} -pv ${FILESDIR}/php.ini ${FREENAS_ROOTFS}/usr/local/lib

do-clean:
	@${RM} -f -r $(SRCDIR)

.include "bsd.freenas.mk"
