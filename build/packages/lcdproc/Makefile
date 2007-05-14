PORTNAME=	lcdproc
CATEGORIES=	sysutils

MAINTAINER=	votdev@gmx.de
COMMENT=	A client/server suite for LCD devices

# Package depends on port 'lcdproc' and 'libusb'.
LIB_DEPENDS+=	usb-0.1.8:${PORTSDIR}/devel/libusb
BUILD_DEPENDS=	lcdproc:${PORTSDIR}/sysutils/lcdproc
DEPENDS_ARGS+=	WITH_USB=yes

do-patch:
	-@if [ ! -e ${PORTSDIR}/sysutils/lcdproc.orig ]; then \
		${CP} -r ${PORTSDIR}/sysutils/lcdproc ${PORTSDIR}/sysutils/lcdproc.orig; \
		${PATCH} -E -u -d ${PORTSDIR}/sysutils/lcdproc -i ${FILESDIR}/patch-lcdproc-0.5.2.diff; \
	fi

do-install:
	@${INSTALL_PROGRAM} ${PORTSDIR}/sysutils/lcdproc/work/lcdproc*/clients/lcdproc/lcdproc ${PREFIX}/bin
	@${INSTALL_PROGRAM} ${PORTSDIR}/sysutils/lcdproc/work/lcdproc*/clients/lcdexec/lcdexec ${PREFIX}/bin
	@${INSTALL_PROGRAM} ${PORTSDIR}/sysutils/lcdproc/work/lcdproc*/clients/lcdvc/lcdvc ${PREFIX}/bin
	@${INSTALL_PROGRAM} ${PORTSDIR}/sysutils/lcdproc/work/lcdproc*/server/LCDd ${PREFIX}/sbin

	@${MKDIR} -v ${PREFIX}/lib/lcdproc
	@${CP} -pv ${PORTSDIR}/sysutils/lcdproc/work/lcdproc*/server/drivers/*.so ${PREFIX}/lib/lcdproc

	@${CP} -pv ${PORTSDIR}/devel/libusb/work/libusb*/.libs/libusb-0.1.so.8 ${PREFIX}/lib
	@${CP} -pv ${PORTSDIR}/devel/libusb/work/libusb*/.libs/libusbpp-0.1.so.8 ${PREFIX}/lib

.include "bsd.freenas.pkg.mk"
