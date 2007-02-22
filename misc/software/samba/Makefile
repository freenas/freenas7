ARCNAME=	samba-3.0.24.tar.gz
URL=	http://us3.samba.org/samba/ftp/stable/$(ARCNAME)
SRCDIR=	$(WORKINGDIR)/$$(basename $(ARCNAME) .tar.gz)

all:	clean
	-@if [ ! -f $(FREENASDISTFILES)/$(ARCNAME) ]; then \
		cd $(FREENASDISTFILES) && fetch $(URL); \
	fi

	tar -zxvf $(FREENASDISTFILES)/$(ARCNAME) -C $(WORKINGDIR)

	cd $(SRCDIR)/source && ./configure --without-cups --with-ads --disable-cups --with-pam --with-ldapsam --with-acl-support --with-winbind --with-pam_smbpass --with-logfilebase=/var/log/samba --with-piddir=/var/run --with-privatedir=/var/etc/private --with-configdir=/var/etc --with-lockdir=/var/run --with-piddir=/var/run --with-shared-modules=idmap_rid --with-pammodulesdir=/usr/local/lib --with-syslog --with-ldap
	make -C $(SRCDIR)/source

install:
	install -vs $(SRCDIR)/source/bin/smbd $(FREENAS)/usr/local/sbin/
	install -vs $(SRCDIR)/source/bin/nmbd $(FREENAS)/usr/local/sbin/
	install -vs $(SRCDIR)/source/bin/winbindd $(FREENAS)/usr/local/sbin/
	install -vs $(SRCDIR)/source/bin/wbinfo $(FREENAS)/usr/local/bin/
	install -vs $(SRCDIR)/source/bin/net $(FREENAS)/usr/local/bin/
	install -vs $(SRCDIR)/source/bin/smbpasswd $(FREENAS)/usr/local/bin/
	install -vs $(SRCDIR)/source/bin/smbstatus $(FREENAS)/usr/bin/
	install -vs $(SRCDIR)/source/bin/smbcontrol $(FREENAS)/usr/bin/
	install -vs $(SRCDIR)/source/bin/smbtree $(FREENAS)/usr/bin/

	mkdir -vp $(FREENAS)/usr/local/lib/samba/charset
	mkdir -vp $(FREENAS)/usr/local/lib/samba/rpc
	mkdir -vp $(FREENAS)/usr/local/lib/samba/pdb
	mkdir -vp $(FREENAS)/usr/local/samba/lib/idmap
	mkdir -vp $(FREENAS)/usr/local/samba/lib/vfs

	cp -vp $(SRCDIR)/source/bin/CP*.so $(FREENAS)/usr/local/lib/samba/charset
	cp -vp $(SRCDIR)/source/codepages/*.dat $(FREENAS)/usr/local/lib/samba
	cp -vp $(SRCDIR)/source/po/*.* $(FREENAS)/usr/local/lib/samba
	cp -vp $(SRCDIR)/source/bin/recycle.so $(FREENAS)/usr/local/samba/lib/vfs
	cp -vp $(SRCDIR)/source/bin/rid.so $(FREENAS)/usr/local/samba/lib/idmap

clean:
	@rm -f -r $(SRCDIR)
