PORTDIR=	/usr/ports/security/openssh-portable

all:	clean
	# Copy ocaml options file
	mkdir -pv /var/db/ports/openssh
	cp -pv ${.CURDIR}/files/options /var/db/ports/openssh

	make -C $(PORTDIR)

install:
	install -vs $(PORTDIR)/work/openssh-*/sshd $(FREENAS)/usr/sbin
	install -vs $(PORTDIR)/work/openssh-*/ssh $(FREENAS)/usr/bin
	install -vs $(PORTDIR)/work/openssh-*/sftp-server $(FREENAS)/usr/libexec

	# Create link to moduli file to prevent log entry:
	# WARNING: /usr/local/etc/ssh/moduli does not exist, using fixed modulus
	mkdir -pv $(FREENAS)/usr/local/etc/ssh
	ln -fsv $(FREENAS)/etc/ssh/moduli $(FREENAS)/usr/local/etc/ssh/moduli

clean:
	make -C $(PORTDIR) clean
