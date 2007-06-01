PORTNAME=			php5
PORTVERSION=	5.2.3
CATEGORIES=		lang
COMMENT=			PHP5 scripting language
MAINTAINER=		votdev@gmx.de

MASTER_SITES=				${MASTER_SITE_PHP:S,$,:release,}
MASTER_SITE_SUBDIR=	distributions/:release
DISTNAME=						php-${PORTVERSION:S/.r/RC/}
DISTFILES=					${DISTNAME}${EXTRACT_SUFX}:release
USE_BZIP2=					yes

USE_AUTOTOOLS=		autoconf:259
USE_GETOPT_LONG=	yes
USE_GNOME=				libxml2

GNU_CONFIGURE=		yes
CONFIGURE_ARGS+=	--enable-fastcgi \
									--enable-discard-path \
									--enable-force-cgi-redirect \
									--without-mysql \
									--without-pear \
									--with-openssl \
									--without-sqlite \
									--with-pcre-regex \
									--with-gettext \
									--enable-libxml \
									--with-libxml-dir=${LOCALBASE}

do-install:
	@${INSTALL_PROGRAM} -v ${WRKSRC}/sapi/cgi/php ${FREENAS_ROOTFS}/usr/local/bin
	@${CP} -pv ${FILESDIR}/php.ini ${FREENAS_ROOTFS}/usr/local/lib

.include "bsd.freenas.mk"
