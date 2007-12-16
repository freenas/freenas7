PORTNAME=			netatalk
PORTVERSION=	2.0.3
CATEGORIES=		editors
COMMENT=			File and print server for AppleTalk networks
MAINTAINER=		votdev@gmx.de

MASTER_SITES=				${MASTER_SITE_SOURCEFORGE}
MASTER_SITE_SUBDIR=	netatalk

GNU_CONFIGURE=		yes
CONFIGURE_ARGS+=	--bindir=/usr/local/bin \
									--sbindir=/usr/local/sbin \
									--sysconfdir=/var/etc \
									--localstatedir=/var \
									--enable-largefile \
									--disable-tcp-wrappers \
									--disable-cups \
									--with-pam \
									--with-uams-path=/etc/uams/

do-install:
	@${INSTALL_PROGRAM} -v ${WRKSRC}/etc/afpd/afpd ${FREENAS_ROOTFS}/usr/local/sbin

	@${MKDIR} -v ${FREENAS_ROOTFS}/etc/uams
	@${CP} -pv ${WRKSRC}/etc/uams/.libs/uams_passwd.so ${FREENAS_ROOTFS}/etc/uams
	@${CP} -pv ${WRKSRC}/etc/uams/.libs/uams_dhx_passwd.so ${FREENAS_ROOTFS}/etc/uams
	@${CP} -pv ${WRKSRC}/etc/uams/.libs/uams_guest.so ${FREENAS_ROOTFS}/etc/uams
	@${CP} -pv ${WRKSRC}/etc/uams/.libs/uams_randnum.so ${FREENAS_ROOTFS}/etc/uams

	@cd ${FREENAS_ROOTFS}/etc/uams && ${LN} -fsv uams_passwd.so uams_clrtxt.so && ${LN} -fsv uams_dhx_passwd.so uams_dhx.so

.include <bsd.port.mk>
