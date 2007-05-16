PKGNAME=	msntp
COMMENT=	A straightforward SNTP daemon/utility
MAINTAINER=	votdev@gmx.de

BUILD_DEPENDS=	msntp:${PORTSDIR}/net/msntp

do-install:
	@${INSTALL_PROGRAM} ${PORTSDIR}/net/msntp/work/msntp*/msntp ${FREENAS_ROOTFS}/usr/local/bin

	@${CP} -pv ${FILESDIR}/runmsntp.sh ${FREENAS_ROOTFS}/usr/local/bin
	@${CHMOD} +x ${FREENAS_ROOTFS}/usr/local/bin/runmsntp.sh

.include "bsd.freenas.mk"
