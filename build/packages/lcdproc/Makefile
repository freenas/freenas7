PORTNAME=	lcdproc
CATEGORIES=	sysutils

MAINTAINER=	votdev@gmx.de
COMMENT=	A client/server suite for LCD devices

# Package depends on port 'lcdproc'.
BUILD_DEPENDS=	lcdproc:${PORTSDIR}/sysutils/lcdproc
DEPENDS_ARGS+=	WITH_USB=YES

do-install:
	@${INSTALL_PROGRAM} ${PORTSDIR}/sysutils/lcdproc/work/lcdproc*/clients/lcdproc/lcdproc ${PREFIX}/bin
	@${INSTALL_PROGRAM} ${PORTSDIR}/sysutils/lcdproc/work/lcdproc*/clients/lcdexec/lcdexec ${PREFIX}/bin
	@${INSTALL_PROGRAM} ${PORTSDIR}/sysutils/lcdproc/work/lcdproc*/clients/lcdvc/lcdvc ${PREFIX}/bin
	@${INSTALL_PROGRAM} ${PORTSDIR}/sysutils/lcdproc/work/lcdproc*/server/LCDd ${PREFIX}/sbin

	@${MKDIR} -v ${PREFIX}/lib/lcdproc
	@${CP} -pv ${PORTSDIR}/sysutils/lcdproc/work/lcdproc*/server/drivers/*.so ${PREFIX}/lib/lcdproc

.include "bsd.freenas.pkg.mk"
