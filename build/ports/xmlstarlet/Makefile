PORTNAME=			xmlstarlet
PORTVERSION=	1.0.1
CATEGORIES=		textproc
COMMENT=			Command line XML toolkit
MAINTAINER=		votdev@gmx.de

BUILD_DEPENDS=	xml:${PORTSDIR}/textproc/xmlstarlet
# No build is required for this port.
NO_BUILD?=			1

post-patch:
# Copy libxslt options file
	@${MKDIR} -v /var/db/ports/libxslt
	@${CP} -pv ${FILESDIR}/libxslt-options /var/db/ports/ocaml/options

do-install:
	@${INSTALL_PROGRAM} -v ${PORTSDIR}/textproc/xmlstarlet/work/xmlstarlet-*/src/xml ${FREENAS_ROOTFS}/usr/local/bin

.include "bsd.freenas.mk"
