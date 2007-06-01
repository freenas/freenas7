#!/usr/bin/env bash

# Description displayed in dialog menu (max. 60 characters)
MENUDESC="nfs - NFS Tools"
# Is dialog menu selected [ ON | OFF ]
MENUSTATUS="ON"

build_nfs() {
	return 0
}

install_nfs() {
	install -vs /usr/sbin/nfsd $FREENAS/usr/sbin
	install -vs /usr/sbin/mountd $FREENAS/usr/sbin
	install -vs /usr/sbin/rpcbind $FREENAS/usr/sbin
	
	return 0
}
