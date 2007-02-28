BUILD_DEPENDS=	openssh-portable:${PORTSDIR}/security/openssh-portable

pre-depends:
# Copy ocaml options file
	@${MKDIR} -v /var/db/ports/openssh
	@${CP} -pv ${FILESDIR}/options /var/db/ports/openssh

install:
	@${INSTALL_PROGRAM} ${PORTSDIR}/security/openssh-portable/work/openssh-*/sshd $(FREENAS)/usr/sbin
	@${INSTALL_PROGRAM} ${PORTSDIR}/security/openssh-portable/work/openssh-*/ssh $(FREENAS)/usr/bin
	@${INSTALL_PROGRAM} ${PORTSDIR}/security/openssh-portable/work/openssh-*/sftp-server $(FREENAS)/usr/libexec

# Create link to moduli file to prevent log entry:
# WARNING: /usr/local/etc/ssh/moduli does not exist, using fixed modulus
	@${MKDIR} -v $(FREENAS)/usr/local/etc/ssh
	@${LN} -fsv $(FREENAS)/etc/ssh/moduli $(FREENAS)/usr/local/etc/ssh/moduli

.include "bsd.freenas.mk"
