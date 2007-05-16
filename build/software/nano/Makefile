PORTNAME=			nano
PORTVERSION=	1.0
CATEGORIES=		editors
COMMENT=			Nano's ANOther editor, an enhanced free Pico clone
MAINTAINER=		votdev@gmx.de

BUILD_DEPENDS=	nano:${PORTSDIR}/editors/nano
# No build is required for this port.
NO_BUILD?=			1

do-install:
	@if [ "$${ARCH}" = "amd64" -o "$${ARCH}" = "ia64" ]; then \
		${INSTALL_PROGRAM} ${PORTSDIR}/editors/nano/work/nano*/nano ${FREENAS_ROOTFS}/usr/local/bin; \
	else \
		${INSTALL_PROGRAM} ${PORTSDIR}/editors/nano/work/nano*/src/nano ${FREENAS_ROOTFS}/usr/local/bin; \
	fi

.include "bsd.freenas.mk"
