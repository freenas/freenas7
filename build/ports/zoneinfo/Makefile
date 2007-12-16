PORTNAME=			zoneinfo
PORTVERSION=	1.0
CATEGORIES=		misc
COMMENT=			Updated timezone definitions
MAINTAINER=		votdev@gmx.de

# No files are needed to be fetched.
DISTFILES?=
# No build is required for this port.
NO_BUILD?=	yes
# Don't register a port installation as a package.
NO_PKG_REGISTER?=	yes
# Set default target to execute when a port is calling a dependency.
DEPENDS_TARGET=

BUILD_DEPENDS=	zoneinfo:${PORTSDIR}/misc/zoneinfo

do-install:
# Compress zoneinfo data, exclude some useless files.
	${MKDIR} ${WRKSRC}
	${ECHO_CMD} "Factory" > ${WRKSRC}/zoneinfo.exlude
	${ECHO_CMD} "posixrules" >> ${WRKSRC}/zoneinfo.exlude
	${ECHO_CMD} "zone.tab" >> ${WRKSRC}/zoneinfo.exlude
	${TAR} -c -v -f - -X ${WRKSRC}/zoneinfo.exlude -C ${PORTSDIR}/misc/zoneinfo/work/zoneinfo/ . | ${GZIP_CMD} -cv > ${FREENAS_ROOTFS}/usr/share/zoneinfo.tgz

.include <bsd.port.mk>
