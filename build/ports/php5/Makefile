PORTNAME=			php5
PORTVERSION=	5.2.13
CATEGORIES=		lang
COMMENT=			PHP5 scripting language
MAINTAINER=		ma.zoon@quicknet.nl

MASTER_SITES=				${MASTER_SITE_PHP:S,$,:release,}
MASTER_SITE_SUBDIR=	distributions/:release
DISTNAME=						php-${PORTVERSION:S/.r/RC/}
DISTFILES=					${DISTNAME}${EXTRACT_SUFX}:release
USE_BZIP2=					yes

BUILD_DEPENDS=		sqlite3>=3.0:${PORTSDIR}/databases/sqlite3

USE_AUTOTOOLS=		autoconf:262
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

do-install:
	@${INSTALL_PROGRAM} -v ${WRKSRC}/sapi/cgi/php-cgi ${FREENAS_ROOTFS}/${PREFIX}/bin/php
	@${CP} -pv ${FILESDIR}/php.ini ${FREENAS_ROOTFS}/${PREFIX}/etc

.include <bsd.port.mk>
