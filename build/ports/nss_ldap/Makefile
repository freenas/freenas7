PORTNAME=			nss_ldap
PORTVERSION=	1.0
CATEGORIES=		net
COMMENT=			RFC 2307 NSS module
MAINTAINER=		votdev@gmx.de

BUILD_DEPENDS=	nss_ldap:${PORTSDIR}/net/nss_ldap
# No build is required for this port.
NO_BUILD?=			1

do-install:
	@${INSTALL_PROGRAM} -v ${PORTSDIR}/net/nss_ldap/work/nss_ldap-*/nss_ldap.so ${FREENAS_ROOTFS}/usr/local/lib/nss_ldap.so.1

.include "bsd.freenas.mk"
