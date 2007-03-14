ARCHIVE=	netatalk-2.0.3.tar.gz
URL=	http://heanet.dl.sourceforge.net/sourceforge/netatalk/$(ARCHIVE)
SRCDIR=	$(WORKINGDIR)/$$(basename $(ARCHIVE) .tar.gz)

do-build:
	-@if [ ! -f $(FREENASDISTFILES)/$(ARCHIVE) ]; then \
		cd $(FREENASDISTFILES) && fetch $(URL); \
	fi

	@${TAR} -zxvf $(FREENASDISTFILES)/$(ARCHIVE) -C $(WORKINGDIR)

	@cd $(SRCDIR) && ./configure --bindir=/usr/local/bin --sbindir=/usr/local/sbin --sysconfdir=/var/etc --localstatedir=/var --enable-largefile --disable-tcp-wrappers --disable-cups --with-pam --with-uams-path=/etc/uams/
	@${MAKE} -C $(SRCDIR)

do-install:
	@${INSTALL_PROGRAM} $(SRCDIR)/etc/afpd/afpd $(FREENAS)/usr/local/sbin

	@${MKDIR} -v $(FREENAS)/etc/uams
	@${CP} -pv $(SRCDIR)/etc/uams/.libs/uams_passwd.so $(FREENAS)/etc/uams
	@${CP} -pv $(SRCDIR)/etc/uams/.libs/uams_dhx_passwd.so $(FREENAS)/etc/uams
	@${CP} -pv $(SRCDIR)/etc/uams/.libs/uams_guest.so $(FREENAS)/etc/uams
	@${CP} -pv $(SRCDIR)/etc/uams/.libs/uams_randnum.so $(FREENAS)/etc/uams

	@cd $(FREENAS)/etc/uams && ${LN} -fsv uams_passwd.so uams_clrtxt.so && ${LN} -fsv uams_dhx_passwd.so uams_dhx.so

do-clean:
	@${RM} -f -r $(SRCDIR)

.include "bsd.freenas.mk"
