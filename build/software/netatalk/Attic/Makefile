PKGNAME=	netatalk
COMMENT=	File and print server for AppleTalk networks
MAINTAINER=	votdev@gmx.de

ARCHIVE=	netatalk-2.0.3.tar.gz
URL=	http://heanet.dl.sourceforge.net/sourceforge/netatalk/$(ARCHIVE)
SRCDIR=	${FREENAS_WORKINGDIR}/$$(basename $(ARCHIVE) .tar.gz)

do-build:
	-@if [ ! -f ${FREENAS_DISTFILES}/$(ARCHIVE) ]; then \
		cd ${FREENAS_DISTFILES} && fetch $(URL); \
	fi

	@${TAR} -zxvf ${FREENAS_DISTFILES}/$(ARCHIVE) -C ${FREENAS_WORKINGDIR}

	@cd $(SRCDIR) && ./configure --bindir=/usr/local/bin --sbindir=/usr/local/sbin --sysconfdir=/var/etc --localstatedir=/var --enable-largefile --disable-tcp-wrappers --disable-cups --with-pam --with-uams-path=/etc/uams/
	@${MAKE} -C $(SRCDIR)

do-install:
	@${INSTALL_PROGRAM} $(SRCDIR)/etc/afpd/afpd ${FREENAS_ROOTFS}/usr/local/sbin

	@${MKDIR} -v ${FREENAS_ROOTFS}/etc/uams
	@${CP} -pv $(SRCDIR)/etc/uams/.libs/uams_passwd.so ${FREENAS_ROOTFS}/etc/uams
	@${CP} -pv $(SRCDIR)/etc/uams/.libs/uams_dhx_passwd.so ${FREENAS_ROOTFS}/etc/uams
	@${CP} -pv $(SRCDIR)/etc/uams/.libs/uams_guest.so ${FREENAS_ROOTFS}/etc/uams
	@${CP} -pv $(SRCDIR)/etc/uams/.libs/uams_randnum.so ${FREENAS_ROOTFS}/etc/uams

	@cd ${FREENAS_ROOTFS}/etc/uams && ${LN} -fsv uams_passwd.so uams_clrtxt.so && ${LN} -fsv uams_dhx_passwd.so uams_dhx.so

do-clean:
	@${RM} -f -r $(SRCDIR)

.include "bsd.freenas.mk"
