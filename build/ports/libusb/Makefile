PORTNAME=			libusb
PORTVERSION=	0.1.12
PORTREVISION=	2
CATEGORIES=		devel
COMMENT=			Library giving userland programs access to USB devices
MAINTAINER=		votdev@gmx.de

MASTER_SITES=	${MASTER_SITE_SOURCEFORGE}
MASTER_SITE_SUBDIR=	${PORTNAME}

CONFIGURE_ARGS=	--disable-build-docs
USE_AUTOTOOLS=	libtool:15
USE_LDCONFIG=		yes

post-patch:
	@${REINPLACE_CMD} -e 's|{libdir}/pkgconfig|{prefix}/libdata/pkgconfig|g' ${WRKSRC}/configure
	@${REINPLACE_CMD} -e 's|<stdint.h>|<inttypes.h>|' ${WRKSRC}/descriptors.c

do-install:
	@${CP} -Rpv ${WRKSRC}/.libs/libusb-*.so* ${FREENAS_ROOTFS}/usr/local/lib
	@${CP} -Rpv ${WRKSRC}/.libs/libusbpp-*.so* ${FREENAS_ROOTFS}/usr/local/lib
# Install on host system too, otherwise depending ports fail during ./configure process. 
	@${CP} -Rpv ${WRKSRC}/.libs/libusb-*.so* ${PREFIX}/lib
	@${CP} -Rpv ${WRKSRC}/.libs/libusbpp-*.so* ${PREFIX}/lib

.include <bsd.port.mk>
