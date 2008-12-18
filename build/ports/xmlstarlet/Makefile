PORTNAME=			xmlstarlet
PORTVERSION=	1.0.1
CATEGORIES=		textproc
COMMENT=			Command line XML toolkit
MAINTAINER=		votdev@gmx.de

MASTER_SITES=				${MASTER_SITE_SOURCEFORGE_EXTENDED}
MASTER_SITE_SUBDIR=	xmlstar

USE_GNOME=			libxslt
USE_ICONV=			yes
GNU_CONFIGURE=	yes
CONFIGURE_ARGS=	--with-libxml-prefix=${LOCALBASE} \
								--with-libxslt-prefix=${LOCALBASE} \
								--with-libiconv-prefix=${LOCALBASE}

post-patch:
	@${REINPLACE_CMD} -e "s|%%PTHREAD%%|${PTHREAD_LIBS}|g" \
		${WRKSRC}/configure

do-install:
	@${INSTALL_PROGRAM} -v ${WRKSRC}/src/xml ${FREENAS_ROOTFS}/${PREFIX}/bin

.include <bsd.port.mk>
