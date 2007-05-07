PKGNAME=	samba
COMMENT=	A free SMB and CIFS client and server for UNIX
MAINTAINER=	votdev@gmx.de

ARCHIVE=	samba-3.0.24.tar.gz
URL=	http://us3.samba.org/samba/ftp/stable/$(ARCHIVE)
SRCDIR=	${FREENAS_WORKINGDIR}/$$(basename $(ARCHIVE) .tar.gz)

SAMBA_LOGFILEBASE=	/var/log/samba
SAMBA_PIDDIR=	/var/run
SAMBA_PRIVATEDIR=	/var/etc/private
SAMBA_CONFIGDIR=	/var/etc
SAMBA_LOCKDIR=	/var/run
SAMBA_PIDDIR=	/var/run
SAMBA_PAMMODULESDIR=	/usr/local/lib

do-build:
	-@if [ ! -f ${FREENAS_DISTFILES}/$(ARCHIVE) ]; then \
		cd ${FREENAS_DISTFILES} && fetch $(URL); \
	fi

	@${TAR} -zxvf ${FREENAS_DISTFILES}/$(ARCHIVE) -C ${FREENAS_WORKINGDIR}

	@cd $(SRCDIR)/source && ./configure --without-cups --with-ads --disable-cups --with-pam --with-ldapsam --with-acl-support --with-winbind --with-pam_smbpass --with-logfilebase=${SAMBA_LOGFILEBASE} --with-piddir=${SAMBA_PIDDIR} --with-privatedir=${SAMBA_PRIVATEDIR} --with-configdir=${SAMBA_CONFIGDIR} --with-lockdir=${SAMBA_LOCKDIR} --with-piddir=${SAMBA_PIDDIR} --with-shared-modules=idmap_rid --with-pammodulesdir=${SAMBA_PAMMODULESDIR} --with-syslog --with-ldap
	@${MAKE} -C $(SRCDIR)/source

do-install:
	@${INSTALL_PROGRAM} $(SRCDIR)/source/bin/smbd ${FREENAS_ROOTFS}/usr/local/sbin
	@${INSTALL_PROGRAM} $(SRCDIR)/source/bin/nmbd ${FREENAS_ROOTFS}/usr/local/sbin
	@${INSTALL_PROGRAM} $(SRCDIR)/source/bin/winbindd ${FREENAS_ROOTFS}/usr/local/sbin
	@${INSTALL_PROGRAM} $(SRCDIR)/source/bin/wbinfo ${FREENAS_ROOTFS}/usr/local/bin
	@${INSTALL_PROGRAM} $(SRCDIR)/source/bin/net ${FREENAS_ROOTFS}/usr/local/bin
	@${INSTALL_PROGRAM} $(SRCDIR)/source/bin/smbpasswd ${FREENAS_ROOTFS}/usr/local/bin
	@${INSTALL_PROGRAM} $(SRCDIR)/source/bin/smbstatus ${FREENAS_ROOTFS}/usr/bin
	@${INSTALL_PROGRAM} $(SRCDIR)/source/bin/smbcontrol ${FREENAS_ROOTFS}/usr/bin
	@${INSTALL_PROGRAM} $(SRCDIR)/source/bin/smbtree ${FREENAS_ROOTFS}/usr/bin

	@${MKDIR} -v ${FREENAS_ROOTFS}/usr/local/lib/samba/charset
	@${MKDIR} -v ${FREENAS_ROOTFS}/usr/local/lib/samba/rpc
	@${MKDIR} -v ${FREENAS_ROOTFS}/usr/local/lib/samba/pdb
	@${MKDIR} -v ${FREENAS_ROOTFS}/usr/local/samba/lib/idmap
	@${MKDIR} -v ${FREENAS_ROOTFS}/usr/local/samba/lib/vfs

	@${CP} -pv $(SRCDIR)/source/bin/CP*.so ${FREENAS_ROOTFS}/usr/local/lib/samba/charset
	@${CP} -pv $(SRCDIR)/source/codepages/*.dat ${FREENAS_ROOTFS}/usr/local/lib/samba
	@${CP} -pv $(SRCDIR)/source/po/*.* ${FREENAS_ROOTFS}/usr/local/lib/samba
	@${CP} -pv $(SRCDIR)/source/bin/recycle.so ${FREENAS_ROOTFS}/usr/local/samba/lib/vfs
	@${CP} -pv $(SRCDIR)/source/bin/rid.so ${FREENAS_ROOTFS}/usr/local/samba/lib/idmap

	@${INSTALL_PROGRAM} $(SRCDIR)/source/bin/pam_smbpass.so ${FREENAS_ROOTFS}/usr/local/lib
	@${INSTALL_PROGRAM} $(SRCDIR)/source/bin/pam_winbind.so ${FREENAS_ROOTFS}/usr/local/lib
	@${INSTALL_PROGRAM} $(SRCDIR)/source/nsswitch/nss_winbind.so ${FREENAS_ROOTFS}/usr/local/lib/nss_winbind.so.1
	@${INSTALL_PROGRAM} $(SRCDIR)/source/nsswitch/nss_wins.so ${FREENAS_ROOTFS}/usr/local/lib/nss_wins.so.1

do-clean:
	@${RM} -f -r $(SRCDIR)

.include "bsd.freenas.mk"
