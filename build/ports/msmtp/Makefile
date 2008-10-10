PORTNAME=			msmtp
PORTVERSION=	1.4.16
CATEGORIES=		mail ipv6
COMMENT=			A SMTP client
MAINTAINER=		votdev@gmx.de

MASTER_SITES=	${MASTER_SITE_SOURCEFORGE}
MASTER_SITE_SUBDIR=	msmtp

USE_BZIP2=			yes
GNU_CONFIGURE=	yes
CPPFLAGS+=			-I${LOCALBASE}/include
LDFLAGS+=				-L${LOCALBASE}/lib
CONFIGURE_ENV=	CPPFLAGS="${CPPFLAGS}" LDFLAGS="${LDFLAGS}"
USE_OPENSSL=		yes
USE_GETTEXT=		yes
CONFIGURE_ARGS+=	--with-ssl=openssl \
									--without-gsasl \
									--without-libidn \
									--enable-nls

do-install:

.include <bsd.port.mk>
