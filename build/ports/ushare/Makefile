PORTNAME=				ushare
PORTVERSION=		1.0
PORTREVISION=		3
CATEGORIES=			net
MASTER_SITES=		http://ushare.geexbox.org/releases/

MAINTAINER=			votdev@gmx.de
COMMENT=				A lightweight UPnP (TM) A/V Media Server

LIB_DEPENDS=		upnp.3:${PORTSDIR}/devel/upnp
BUILD_DEPENDS=	iconv:${PORTSDIR}/converters/libiconv

USE_BZIP2=			yes
GNU_CONFIGURE=	yes
USE_GNOME=			pkgconfig
CONFIGURE_ARGS=	--with-libiconv-prefix=${LOCALBASE}/lib \
								--with-libintl-prefix=${LOCALBASE}/lib \
								--with-libupnp-prefix=${LOCALBASE}
CFLAGS+=				${PTHREAD_LIBS}

do-install:
	@${INSTALL_PROGRAM} -v ${WRKSRC}/src/ushare ${FREENAS_ROOTFS}/usr/local/bin
	@${INSTALL_SCRIPT} ${FILESDIR}/${PORTNAME}.in ${FREENAS_ROOTFS}/etc/rc.d/${PORTNAME}

.include <bsd.port.mk>
