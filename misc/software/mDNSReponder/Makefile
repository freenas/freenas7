BUILD_DEPENDS=	mDNSResponder:${PORTSDIR}/net/mDNSResponder

install:
	@${INSTALL_PROGRAM} ${PORTSDIR}/net/mDNSResponder/work/mDNSResponder-*/mDNSPosix/build/prod/mDNSResponderPosix $(FREENAS)/usr/local/sbin

.include "bsd.freenas.mk"
