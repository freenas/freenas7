PORTNAME=			pccard
PORTVERSION=	1.0
PORTREVISION=	0
PORTEPOCH=		1
CATEGORIES=		sysutils
COMMENT=			pccard userland applications
MAINTAINER=		votdev@gmx.de

# No files are needed to be fetched.
DISTFILES?=

do-build:
	@${MAKE} -C /usr/src/usr.sbin/pccard/pccardc
	@${MAKE} -C /usr/src/usr.sbin/pccard/pccardd

do-install:
	
	 -@if [ -f /usr/src/usr.sbin/pccard/pccardc/pccardc ]; then \
	    @${INSTALL_PROGRAM} -v /usr/src/usr.sbin/pccard/pccardc/pccardc ${FREENAS_ROOTFS}/usr/sbin; \
	else
	    @${INSTALL_PROGRAM} -v /usr/obj/usr/src/usr.sbin/pccard/pccardc/pccardc ${FREENAS_ROOTFS}/usr/sbin; \
	fi
	@${INSTALL_PROGRAM} -v /usr/src/usr.sbin/pccard/pccardd/pccardd ${FREENAS_ROOTFS}/usr/sbin; \

.include <bsd.port.mk>
