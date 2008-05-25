PORTNAME=			pccard
PORTVERSION=	1.0
PORTREVISION=	0
PORTEPOCH=		2
CATEGORIES=		sysutils
COMMENT=			pccard userland applications
MAINTAINER=		votdev@gmx.de

# No files are needed to be fetched.
DISTFILES?=

do-build:
	@cd /usr/src/usr.sbin/pccard/pccardc; make clean obj; make
	@cd /usr/src/usr.sbin/pccard/pccardd; make clean obj; make

do-install:
	@${INSTALL_PROGRAM} -v /usr/obj/usr/src/usr.sbin/pccard/pccardc/pccardc ${FREENAS_ROOTFS}/usr/sbin
	@${INSTALL_PROGRAM} -v /usr/obj/usr/src/usr.sbin/pccard/pccardd/pccardd ${FREENAS_ROOTFS}/usr/sbin

pre-clean:
	@cd /usr/src/usr.sbin/pccard/pccardc; make clean obj
	@cd /usr/src/usr.sbin/pccard/pccardd; make clean obj

.include <bsd.port.mk>
