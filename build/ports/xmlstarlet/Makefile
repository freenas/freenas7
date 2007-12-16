PORTNAME=			xmlstarlet
PORTVERSION=	1.0.1
CATEGORIES=		textproc
COMMENT=			Command line XML toolkit
MAINTAINER=		votdev@gmx.de

# No files are needed to be fetched.
DISTFILES?=
# No build is required for this port.
NO_BUILD?=	yes
# Don't register a port installation as a package.
NO_PKG_REGISTER?=	yes
# Set default target to execute when a port is calling a dependency.
DEPENDS_TARGET=

BUILD_DEPENDS=	xml:${PORTSDIR}/textproc/xmlstarlet

post-patch:
# Copy libxslt options file
	@${MKDIR} -v /var/db/ports/libxslt
	@${CP} -pv ${FILESDIR}/libxslt-options /var/db/ports/libxslt/options

do-install:
	@${INSTALL_PROGRAM} -v ${PORTSDIR}/textproc/xmlstarlet/work/xmlstarlet-*/src/xml ${FREENAS_ROOTFS}/usr/local/bin

.include <bsd.port.mk>
