#!/usr/bin/env bash

build_nfs() {
	return 0
}

install_nfs() {
	install -vs /usr/sbin/nfsd $FREENAS/usr/sbin
	install -vs /usr/sbin/mountd $FREENAS/usr/sbin
	install -vs /usr/sbin/rpcbind $FREENAS/usr/sbin
	
	return 0
}
