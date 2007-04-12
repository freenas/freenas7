PORTNAME=	lcdproc
PORTVERSION=	0.1
CATEGORIES=	sysutils

MAINTAINER=	votdev@gmx.de
COMMENT=	A client/server suite for LCD devices

BUILD_DEPENDS=	lcdproc:${PORTSDIR}/sysutils/lcdproc

################################################################
# Special configuration if package depends on a port:
# - Always clean and build port but do not install it.
################################################################
.if defined(BUILD_DEPENDS)
ALWAYS_BUILD_DEPENDS=	1
DEPENDS_TARGET=	clean build
.endif

# No files are needed to be fetched for this package.
DISTFILES=	
# No build is required for this package.	
NO_BUILD=	1
# No manpages are installed from this package.
NO_INSTALL_MANPAGES=	1
# Overwrite any existing package registration information.
FORCE_PKG_REGISTER=	1
# The temporary working directory.
WRKDIR=	/usr/local/freenas/packages/${PORTNAME}/work
# A top level directory where all packages go.
PACKAGES=	/usr/local/freenas/packages

# Package dependend arguments.
DEPENDS_ARGS+=	WITH_USB=YES

do-install:
	@${INSTALL_PROGRAM} ${PORTSDIR}/sysutils/lcdproc/work/lcdproc*/clients/lcdproc/lcdproc ${PREFIX}/bin
	@${INSTALL_PROGRAM} ${PORTSDIR}/sysutils/lcdproc/work/lcdproc*/clients/lcdexec/lcdexec ${PREFIX}/bin
	@${INSTALL_PROGRAM} ${PORTSDIR}/sysutils/lcdproc/work/lcdproc*/clients/lcdvc/lcdvc ${PREFIX}/bin
	@${INSTALL_PROGRAM} ${PORTSDIR}/sysutils/lcdproc/work/lcdproc*/server/LCDd ${PREFIX}/sbin

	@${MKDIR} -v ${PREFIX}/lib/lcdproc
	@${CP} -pv ${PORTSDIR}/sysutils/lcdproc/work/lcdproc*/server/drivers/*.so ${PREFIX}/lib/lcdproc

.include <bsd.port.mk>
