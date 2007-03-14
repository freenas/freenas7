BUILD_DEPENDS=	lcdproc:${PORTSDIR}/sysutils/lcdproc

pre-depends:
# Enable USB support.
	@export WITH_USB=YES

do-install:
	@${INSTALL_PROGRAM} ${PORTSDIR}/sysutils/lcdproc/work/lcdproc*/clients/lcdproc/lcdproc $(FREENAS)/usr/local/bin
	@${INSTALL_PROGRAM} ${PORTSDIR}/sysutils/lcdproc/work/lcdproc*/clients/lcdexec/lcdexec $(FREENAS)/usr/local/bin
	@${INSTALL_PROGRAM} ${PORTSDIR}/sysutils/lcdproc/work/lcdproc*/clients/lcdvc/lcdvc $(FREENAS)/usr/local/bin
	@${INSTALL_PROGRAM} ${PORTSDIR}/sysutils/lcdproc/work/lcdproc*/server/LCDd $(FREENAS)/usr/local/sbin

	@${MKDIR} -v $(FREENAS)/usr/local/lib/lcdproc
	@${CP} -pv ${PORTSDIR}/sysutils/lcdproc/work/lcdproc*/server/drivers/*.so $(FREENAS)/usr/local/lib/lcdproc

.include "bsd.freenas.mk"
