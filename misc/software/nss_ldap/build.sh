#!/usr/bin/env bash

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
