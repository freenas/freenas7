BUILD_DEPENDS=	pam_ldap:${PORTSDIR}/security/pam_ldap

install:
	@${INSTALL_PROGRAM} ${PORTSDIR}/security/pam_ldap/work/pam_ldap-*/pam_ldap.so $(FREENAS)/usr/local/lib

.include "bsd.freenas.mk"
