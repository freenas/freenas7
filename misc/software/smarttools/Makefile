BUILD_DEPENDS=	smartmontools:${PORTSDIR}/sysutils/smartmontools

install:
	@${INSTALL_PROGRAM} ${PORTSDIR}/sysutils/smartmontools/work/smartmontools-*/smartctl $(FREENAS)/usr/local/sbin
	@${INSTALL_PROGRAM} ${PORTSDIR}/sysutils/smartmontools/work/smartmontools-*/smartd $(FREENAS)/usr/local/sbin

.include "bsd.freenas.mk"
