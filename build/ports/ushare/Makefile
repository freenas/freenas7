PORTNAME=	ushare
PORTVERSION=	1.0
CATEGORIES=	net
MASTER_SITES=	http://ushare.geexbox.org/releases/

MAINTAINER=	votdev@gmx.de
COMMENT=	A lightweight UPnP (TM) A/V Media Server

LIB_DEPENDS=		upnp.2:${.CURDIR}/../upnp:install
BUILD_DEPENDS=	iconv:${PORTSDIR}/converters/libiconv

USE_BZIP2=			yes
GNU_CONFIGURE=	yes
CONFIGURE_ARGS=	--with-libiconv-prefix=${LOCALBASE}/lib \
								--with-libintl-prefix=${LOCALBASE}/lib \
								--mandir=${LOCALBASE}/man
USE_GNOME=			pkgconfig

do-install:
	@${INSTALL_PROGRAM} -v ${PORTSDIR}/net/ushare/work/ushare-*/src/ushare ${FREENAS_ROOTFS}/usr/local/bin

.include "bsd.freenas.mk"
