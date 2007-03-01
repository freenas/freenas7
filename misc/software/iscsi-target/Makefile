BUILD_DEPENDS=	iscsi-target:${PORTSDIR}/net/iscsi-target

do-install:
	@${INSTALL_PROGRAM} ${PORTSDIR}/net/iscsi-target/work/netbsd-iscsi-*/bin/iscsi-target $(FREENAS)/usr/local/bin/

.include "bsd.freenas.mk"
