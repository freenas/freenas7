#!/usr/bin/env bash

build_openssh-portable() {
	cd /usr/ports/security/openssh-portable/

	# Copy options file
	mkdir -pv /var/db/ports/openssh
	cp -pv $SVNDIR/misc/software/openssh-portable/files/options /var/db/ports/openssh

	make clean
	make

	return $?
}

install_openssh-portable() {
	cd /usr/ports/security/openssh-portable/

	install -vs work/openssh-*/sshd $FREENAS/usr/sbin
	install -vs work/openssh-*/ssh $FREENAS/usr/bin
	install -vs work/openssh-*/sftp-server $FREENAS/usr/libexec

	# Create link to moduli file to prevent log entry:
	# WARNING: /usr/local/etc/ssh/moduli does not exist, using fixed modulus
	mkdir -pv $FREENAS/usr/local/etc/ssh
	ln -sv $FREENAS/etc/ssh/moduli $FREENAS/usr/local/etc/ssh/moduli

	return 0
}
