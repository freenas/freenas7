PORTNAME=	pam_mkhomedir
PORTVERSION=	0.1
CATEGORIES=		security
COMMENT=		Create HOME with a PAM module on demand
MAINTAINER=		olivier@freenas.org

BUILD_DEPENDS=	pam_mkhomedir:${PORTSDIR}/security/pam_mkhomedir
# No build is required for this port.
NO_BUILD?=			1

do-install:
	@${INSTALL_PROGRAM} -v ${PORTSDIR}/security/pam_mkhomedir/work/pam_mkhomedir-*/pam_mkhomedir.so ${FREENAS_ROOTFS}/usr/local/lib

.include "bsd.freenas.mk"
