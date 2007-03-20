PKGNAME=	nano
COMMENT=	Nano's ANOther editor, an enhanced free Pico clone
MAINTAINER=	votdev@gmx.de

BUILD_DEPENDS=	nano:${PORTSDIR}/editors/nano

.include "bsd.freenas.macros.mk"

do-install:
.if ${ARCH} == "amd64" || ${ARCH} == "ia64"
	@${INSTALL_PROGRAM} ${PORTSDIR}/editors/nano/work/nano*/nano $(FREENAS)/usr/local/bin
.else
	@${INSTALL_PROGRAM} ${PORTSDIR}/editors/nano/work/nano*/src/nano $(FREENAS)/usr/local/bin
.endif

.include "bsd.freenas.mk"
