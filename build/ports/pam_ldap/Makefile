PORTNAME=			pam_ldap
PORTVERSION=	1.8.2
CATEGORIES=		security
COMMENT=			A pam module for authenticating with LDAP
MAINTAINER=		votdev@gmx.de

BUILD_DEPENDS=	pam_ldap:${PORTSDIR}/security/pam_ldap
# No build is required for this port.
NO_BUILD?=			1

do-install:
	@${INSTALL_PROGRAM} -v ${PORTSDIR}/security/pam_ldap/work/pam_ldap-*/pam_ldap.so ${FREENAS_ROOTFS}/usr/local/lib

.include "bsd.freenas.mk"
