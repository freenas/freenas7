PORTNAME=			php5
PORTVERSION=	5.2.16
CATEGORIES=		lang
COMMENT=			PHP5 scripting language
MAINTAINER=		michael.zoon@freenas.org

MASTER_SITES=				${MASTER_SITE_PHP:S,$,:release,}
MASTER_SITE_SUBDIR=	distributions/:release
DISTNAME=						php-${PORTVERSION:S/.r/RC/}
DISTFILES=					${DISTNAME}${EXTRACT_SUFX}:release
USE_BZIP2=					yes

BUILD_DEPENDS=		sqlite3>=3.0:${PORTSDIR}/databases/sqlite3

USE_AUTOTOOLS=	autoconf
AUTOTOOLSFILES=	scripts/phpize.in
USE_GETOPT_LONG=	yes
USE_GNOME=				libxml2
USE_OPENSSL=			yes
GNU_CONFIGURE=		yes

CONFIGURE_ARGS+=	--enable-fastcgi \
									--enable-discard-path \
									--enable-force-cgi-redirect \
									--without-mysql \
									--without-pear \
									--with-openssl \
									--without-sqlite \
									--with-pcre-regex=yes \
									--with-gettext \
									--enable-libxml \
									--with-libxml-dir=${LOCALBASE} \
									--with-config-file-path=/usr/local/etc \
									--enable-pdo \
									--with-pdo-sqlite=${LOCALBASE} \
									--with-iconv-dir=${LOCALBASE}



# Work around issues with newer (>=2.64) autoconf
pre-configure:
	@${REINPLACE_CMD} -E 's:^((m4_)?divert)[(]([0-9]*)[)]:\1(600\3):' \
		${WRKSRC}/configure.in `${FIND} ${WRKSRC} -name '*.m4'`
	@${RM} ${WRKSRC}/configure
	@${CAT} ${WRKSRC}/acinclude.m4 ${WRKSRC}/build/libtool.m4 > ${WRKSRC}/aclocal.m4



do-install:
	@${INSTALL_PROGRAM} -v ${WRKSRC}/sapi/cgi/php-cgi ${FREENAS_ROOTFS}/${PREFIX}/bin/php
	@${CP} -pv ${FILESDIR}/php.ini ${FREENAS_ROOTFS}/${PREFIX}/etc

.include <bsd.port.mk>
