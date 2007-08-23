PORTNAME=			nano
PORTVERSION=	1.0
CATEGORIES=		editors
COMMENT=			Nano's ANOther editor, an enhanced free Pico clone
MAINTAINER=		votdev@gmx.de

BUILD_DEPENDS=	nano:${PORTSDIR}/editors/nano
# No build is required for this port.
NO_BUILD?=			1

do-install:
	${INSTALL_PROGRAM} -v ${PORTSDIR}/editors/nano/work/nano*/src/nano ${FREENAS_ROOTFS}/usr/local/bin; \

.include "bsd.freenas.mk"
