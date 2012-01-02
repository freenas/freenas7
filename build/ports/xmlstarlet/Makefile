PORTNAME=		xmlstarlet
PORTVERSION=		1.3.0
CATEGORIES=		textproc
COMMENT=		Command line XML toolkit
MAINTAINER=		aoyama@peach.ne.jp

MASTER_SITES=		SF/xmlstar/${PORTNAME}/${PORTVERSION}

USE_GNOME=		libxslt
USE_ICONV=		yes
GNU_CONFIGURE=		yes
CONFIGURE_ARGS=		--with-libxml-prefix=${LOCALBASE} \
			--with-libxslt-prefix=${LOCALBASE} \
			--with-libiconv-prefix=${LOCALBASE}

post-patch:
	@${REINPLACE_CMD} -e "s|%%PTHREAD%%|${PTHREAD_LIBS}|g" \
		${WRKSRC}/configure

do-install:
	@${INSTALL_PROGRAM} -v ${WRKSRC}/xml ${FREENAS_ROOTFS}/${PREFIX}/bin

.include <bsd.port.mk>
