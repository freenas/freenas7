PORTNAME=			msmtp
PORTVERSION=	1.4.17
CATEGORIES=		mail ipv6
COMMENT=			A SMTP client
MAINTAINER=		votdev@gmx.de

MASTER_SITES=				${MASTER_SITE_SOURCEFORGE}
MASTER_SITE_SUBDIR=	msmtp

USE_BZIP2=				yes
GNU_CONFIGURE=		yes
CPPFLAGS+=				-I${LOCALBASE}/include
LDFLAGS+=					-L${LOCALBASE}/lib
CONFIGURE_ENV=		CPPFLAGS="${CPPFLAGS}" LDFLAGS="${LDFLAGS}"
USE_OPENSSL=			yes
USE_GETTEXT=			yes
CONFIGURE_ARGS+=	--with-ssl=openssl \
									--without-gsasl \
									--without-libidn \
									--enable-nls

do-install:
	@${INSTALL_PROGRAM} -v ${WRKSRC}/src/${PORTNAME} ${FREENAS_ROOTFS}/usr/local/bin
	@${INSTALL_SCRIPT} -v ${FILESDIR}/${PORTNAME}.in ${FREENAS_ROOTFS}/etc/rc.d/${PORTNAME}

.include <bsd.port.mk>
