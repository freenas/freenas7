BUILD_DEPENDS=	smartmontools:${PORTSDIR}/sysutils/smartmontools

pre-depends:
# Copy smarttools options file
	@${MKDIR} -v /var/db/ports/smarttools
	@${CP} -pv ${FILESDIR}/options /var/db/ports/smarttools

do-install:
	@${INSTALL_PROGRAM} ${PORTSDIR}/sysutils/smartmontools/work/smartmontools-*/smartctl $(FREENAS)/usr/local/sbin
	@${INSTALL_PROGRAM} ${PORTSDIR}/sysutils/smartmontools/work/smartmontools-*/smartd $(FREENAS)/usr/local/sbin

.include "bsd.freenas.mk"
