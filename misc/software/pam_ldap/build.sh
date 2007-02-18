#!/usr/bin/env bash

# Description displayed in dialog menu (max. 60 characters)
MENUDESC="pam_ldap - A pam module for authenticating with LDAP"
# Is dialog menu selected [ ON | OFF ]
MENUSTATUS="ON"

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
