#!/usr/bin/env bash

build_openssh-portable() {
	cd /usr/ports/security/openssh-portable/

	# Copy options file
	mkdir /var/db/ports/openssh/
	cp -v -p $SVNDIR/misc/software/openssh-portable/files/options /var/db/ports/openssh/

	make clean
	make

	return 0
}

install_openssh-portable() {
	cd /usr/ports/security/openssh-portable/

	install -vs work/openssh-*/sshd $FREENAS/usr/sbin/
	install -vs work/openssh-*/sftp-server $FREENAS/usr/libexec/

	return 0
}
