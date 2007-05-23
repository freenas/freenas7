PORTNAME=			php5
PORTVERSION=	5.2.2
CATEGORIES=		lang
COMMENT=			PHP5 scripting language
MAINTAINER=		votdev@gmx.de

MASTER_SITES=				${MASTER_SITE_PHP:S,$,:release,}
MASTER_SITE_SUBDIR=	distributions/:release
DISTNAME=						php-${PORTVERSION:S/.r/RC/}
DISTFILES=					${DISTNAME}${EXTRACT_SUFX}:release
USE_BZIP2=					yes

LIB_DEPENDS=	xml2.5:${PORTSDIR}/textproc/libxml2

GNU_CONFIGURE=		yes
CONFIGURE_ARGS+=	--enable-fastcgi \
									--enable-discard-path \
									--enable-force-cgi-redirect \
									--without-mysql \
									--without-pear \
									--with-openssl \
									--without-sqlite \
									--with-pcre-regex \
									--with-gettext

do-install:
	@${INSTALL_PROGRAM} -v ${WRKSRC}/sapi/cgi/php ${FREENAS_ROOTFS}/usr/local/bin
	@${CP} -pv ${FILESDIR}/php.ini ${FREENAS_ROOTFS}/usr/local/lib

.include "bsd.freenas.mk"
