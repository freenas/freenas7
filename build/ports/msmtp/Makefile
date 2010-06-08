PORTNAME=			msmtp
PORTVERSION=	1.4.20
CATEGORIES=		mail ipv6
COMMENT=			A SMTP client
MAINTAINER=		michael.zoon@freenas.org

MASTER_SITES=			SF

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
