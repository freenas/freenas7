#!/usr/bin/env bash

build_openssh-portable() {
	cd /usr/ports/security/openssh-portable/
	# MUST REPLACE A NO BY A YES in the Makefile !!!!!

	make clean
	make

	return 0
}

install_openssh-portable() {
	cd /usr/ports/security/openssh-portable/

	install -vs work/openssh-4.5p1/sshd $FREENAS/usr/sbin/
	install -vs work/openssh-4.5p1/sftp-server $FREENAS/usr/libexec/

	return 0
}
