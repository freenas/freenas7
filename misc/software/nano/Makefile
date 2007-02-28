BUILD_DEPENDS=	nano:${PORTSDIR}/editors/nano

.include "bsd.freenas.mk"

install:
.if ${ARCH} == "amd64" || ${ARCH} == "ia64"
	@${INSTALL_PROGRAM} ${PORTSDIR}/editors/nano/work/nano*/nano $(FREENAS)/usr/local/bin
.else
	@${INSTALL_PROGRAM} ${PORTSDIR}/editors/nano/work/nano*/src/nano $(FREENAS)/usr/local/bin
.endif
