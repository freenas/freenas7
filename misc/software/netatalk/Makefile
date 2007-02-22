ARCNAME=	netatalk-2.0.3.tar.gz
URL=	http://heanet.dl.sourceforge.net/sourceforge/netatalk/$(ARCNAME)
SRCDIR=	$(WORKINGDIR)/$$(basename $(ARCNAME) .tar.gz)

all:	clean
	-@if [ ! -f $(FREENASDISTFILES)/$(ARCNAME) ]; then \
		cd $(FREENASDISTFILES) && fetch $(URL); \
	fi

	tar -zxvf $(FREENASDISTFILES)/$(ARCNAME) -C $(WORKINGDIR)

	cd $(SRCDIR) && ./configure --bindir=/usr/local/bin --sbindir=/usr/local/sbin --sysconfdir=/var/etc --localstatedir=/var --enable-largefile --disable-tcp-wrappers --disable-cups --with-pam --with-uams-path=/etc/uams/
	make -C $(SRCDIR)

install:
	install -vs $(SRCDIR)/etc/afpd/afpd $(FREENAS)/usr/local/sbin

	mkdir -pv $(FREENAS)/etc/uams
	cp -pv $(SRCDIR)/etc/uams/.libs/uams_passwd.so $(FREENAS)/etc/uams
	cp -pv $(SRCDIR)/etc/uams/.libs/uams_dhx_passwd.so $(FREENAS)/etc/uams
	cp -pv $(SRCDIR)/etc/uams/.libs/uams_guest.so $(FREENAS)/etc/uams
	cp -pv $(SRCDIR)/etc/uams/.libs/uams_randnum.so $(FREENAS)/etc/uams

	cd $(FREENAS)/etc/uams && ln -fsv uams_passwd.so uams_clrtxt.so && ln -fsv uams_dhx_passwd.so uams_dhx.so

clean:
	@rm -f -r $(SRCDIR)
