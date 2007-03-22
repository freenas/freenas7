#!/usr/bin/env bash

# Description displayed in dialog menu (max. 60 characters)
MENUDESC="nss_ldap - RFC 2307 NSS module"
# Is dialog menu selected [ ON | OFF ]
MENUSTATUS="ON"

build_nss_ldap() {
	cd /usr/ports/net/nss_ldap

	make clean
	make

	return $?
}

install_nss_ldap() {
	cd /usr/ports/net/nss_ldap

	install -vs work/nss_ldap-*/nss_ldap.so $FREENAS/usr/local/lib/nss_ldap.so.1

	return 0
}
