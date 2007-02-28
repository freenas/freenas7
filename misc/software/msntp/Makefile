BUILD_DEPENDS=	msntp:${PORTSDIR}/net/msntp

install:
	@${INSTALL_PROGRAM} ${PORTSDIR}/net/msntp/work/msntp*/msntp $(FREENAS)/usr/local/bin

	@${CP} -pv ${FILESDIR}/runmsntp.sh $(FREENAS)/usr/local/bin
	@${CHMOD} +x $(FREENAS)/usr/local/bin/runmsntp.sh

.include "bsd.freenas.mk"
