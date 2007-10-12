PORTNAME=			zoneinfo
PORTVERSION=	1.0
CATEGORIES=		misc
COMMENT=			Updated timezone definitions
MAINTAINER=		votdev@gmx.de

BUILD_DEPENDS=	zoneinfo:${PORTSDIR}/misc/zoneinfo
# No build is required for this port.
NO_BUILD?=			1

do-install:
# Compress zoneinfo data, exclude some useless files.
	${MKDIR} ${WRKSRC}
	${ECHO_CMD} "Factory" > ${WRKSRC}/zoneinfo.exlude
	${ECHO_CMD} "posixrules" >> ${WRKSRC}/zoneinfo.exlude
	${ECHO_CMD} "zone.tab" >> ${WRKSRC}/zoneinfo.exlude
	${TAR} -c -v -f - -X ${WRKSRC}/zoneinfo.exlude -C ${PORTSDIR}/misc/zoneinfo/work/zoneinfo/ . | ${GZIP_CMD} -cv > ${FREENAS_ROOTFS}/usr/share/zoneinfo.tgz

.include "bsd.freenas.mk"
