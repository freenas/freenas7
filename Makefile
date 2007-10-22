PORTNAME=			aria2
PORTVERSION=	0.11.3
CATEGORIES=		www
COMMENT=			Yet another download tool
MAINTAINER=		votdev@gmx.de

MASTER_SITES=				${MASTER_SITE_SOURCEFORGE}
MASTER_SITE_SUBDIR=	${PORTNAME}

CONFIGURE_ARGS=	--with-libintl-prefix=${LOCALBASE} --with-openssl \
								--with-libxml2 --without-gnutls --without-libares \
								--program-transform-name="" --without-libcares \
								--enable-bittorrent --enable-metalink --disable-rpath

USE_GCC=				3.4+
USE_BZIP2=			yes
USE_GETTEXT=		yes
GNU_CONFIGURE=	yes
USE_GNOME=			gnomehack libxml2

post-configure:
	@${REINPLACE_CMD} -e '/SETMODE/d' ${WRKSRC}/config.h

do-install:

.include <bsd.port.mk>
#.include "bsd.freenas.mk"
