#!/usr/bin/env bash

build_pam_ldap() {
	cd /usr/ports/security/pam_ldap

	make clean
	make

	return $?
}

install_pam_ldap() {
	cd /usr/ports/security/pam_ldap

	install -vs work/pam_ldap-*/pam_ldap.so $FREENAS/usr/local/lib

	return 0
}
