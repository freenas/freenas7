BUILD_DEPENDS=	nss_ldap:${PORTSDIR}/net/nss_ldap

install:
	@${INSTALL_PROGRAM} ${PORTSDIR}/net/nss_ldap/work/nss_ldap-*/nss_ldap.so $(FREENAS)/usr/local/lib/nss_ldap.so.1

.include "bsd.freenas.mk"
