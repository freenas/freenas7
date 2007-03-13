ARCHIVE=	samba-3.0.24.tar.gz
URL=	http://us3.samba.org/samba/ftp/stable/$(ARCHIVE)
SRCDIR=	$(WORKINGDIR)/$$(basename $(ARCHIVE) .tar.gz)

do-build:
	-@if [ ! -f $(FREENASDISTFILES)/$(ARCHIVE) ]; then \
		cd $(FREENASDISTFILES) && fetch $(URL); \
	fi

	@${TAR} -zxvf $(FREENASDISTFILES)/$(ARCHIVE) -C $(WORKINGDIR)

	@cd $(SRCDIR)/source && ./configure --without-cups --with-ads --disable-cups --with-pam --with-ldapsam --with-acl-support --with-winbind --with-pam_smbpass --with-logfilebase=/var/log/samba --with-piddir=/var/run --with-privatedir=/var/etc/private --with-configdir=/var/etc --with-lockdir=/var/run --with-piddir=/var/run --with-shared-modules=idmap_rid --with-pammodulesdir=/usr/local/lib --with-syslog --with-ldap
	@${MAKE} -C $(SRCDIR)/source

do-install:
	@${INSTALL_PROGRAM} $(SRCDIR)/source/bin/smbd $(FREENAS)/usr/local/sbin
	@${INSTALL_PROGRAM} $(SRCDIR)/source/bin/nmbd $(FREENAS)/usr/local/sbin
	@${INSTALL_PROGRAM} $(SRCDIR)/source/bin/winbindd $(FREENAS)/usr/local/sbin
	@${INSTALL_PROGRAM} $(SRCDIR)/source/bin/wbinfo $(FREENAS)/usr/local/bin
	@${INSTALL_PROGRAM} $(SRCDIR)/source/bin/net $(FREENAS)/usr/local/bin
	@${INSTALL_PROGRAM} $(SRCDIR)/source/bin/smbpasswd $(FREENAS)/usr/local/bin
	@${INSTALL_PROGRAM} $(SRCDIR)/source/bin/smbstatus $(FREENAS)/usr/bin
	@${INSTALL_PROGRAM} $(SRCDIR)/source/bin/smbcontrol $(FREENAS)/usr/bin
	@${INSTALL_PROGRAM} $(SRCDIR)/source/bin/smbtree $(FREENAS)/usr/bin

	@${MKDIR} -v $(FREENAS)/usr/local/lib/samba/charset
	@${MKDIR} -v $(FREENAS)/usr/local/lib/samba/rpc
	@${MKDIR} -v $(FREENAS)/usr/local/lib/samba/pdb
	@${MKDIR} -v $(FREENAS)/usr/local/samba/lib/idmap
	@${MKDIR} -v $(FREENAS)/usr/local/samba/lib/vfs

	@${INSTALL_PROGRAM} $(SRCDIR)/source/bin/CP*.so $(FREENAS)/usr/local/lib/samba/charset
	@${INSTALL_DATA} $(SRCDIR)/source/codepages/*.dat $(FREENAS)/usr/local/lib/samba
	@${INSTALL_DATA} $(SRCDIR)/source/po/*.* $(FREENAS)/usr/local/lib/samba
	@${INSTALL_PROGRAM} $(SRCDIR)/source/bin/recycle.so $(FREENAS)/usr/local/samba/lib/vfs
	@${INSTALL_PROGRAM} $(SRCDIR)/source/bin/rid.so $(FREENAS)/usr/local/samba/lib/idmap

do-clean:
	@${RM} -f -r $(SRCDIR)

.include "bsd.freenas.mk"
