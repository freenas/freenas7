PORTNAME=			netatalk
PORTVERSION=	2.0.5
PORTREVISION=	1
PORTEPOCH=		1
CATEGORIES=		net
COMMENT=			File and print server for AppleTalk networks
MAINTAINER=		votdev@gmx.de

MASTER_SITES=			SF

LIB_DEPENDS=    gcrypt:${PORTSDIR}/security/libgcrypt
USE_AUTOTOOLS=		libtool:22
GNU_CONFIGURE=		yes
USE_ICONV=				yes
USE_BDB=        41+
USE_GMAKE=      yes
USE_PERL5=      yes
CONFIGURE_ARGS+=	--bindir=/usr/local/bin \
									--sbindir=/usr/local/sbin \
									--sysconfdir=/var/etc \
									--localstatedir=/var \
									--enable-largefile \
									--disable-tcp-wrappers \
									--disable-cups \
									--with-pam=/etc/pam.d \
									--with-uams-path=/etc/uams/ \
									--with-libiconv=${LOCALBASE}

do-install:
	@${INSTALL_PROGRAM} -v ${WRKSRC}/etc/afpd/afpd ${FREENAS_ROOTFS}/usr/local/sbin
	@${INSTALL_PROGRAM} -v ${WRKSRC}/etc/cnid_dbd/cnid_metad ${FREENAS_ROOTFS}/usr/local/sbin

	@${MKDIR} -v ${FREENAS_ROOTFS}/etc/uams
	@${INSTALL_PROGRAM} -v ${WRKSRC}/etc/uams/.libs/uams_passwd.so ${FREENAS_ROOTFS}/etc/uams
	@${INSTALL_PROGRAM} -v ${WRKSRC}/etc/uams/.libs/uams_dhx_passwd.so ${FREENAS_ROOTFS}/etc/uams
	@${INSTALL_PROGRAM} -v ${WRKSRC}/etc/uams/.libs/uams_dhx2_passwd.so ${FREENAS_ROOTFS}/etc/uams
	@${INSTALL_PROGRAM} -v ${WRKSRC}/etc/uams/.libs/uams_guest.so ${FREENAS_ROOTFS}/etc/uams
	@${INSTALL_PROGRAM} -v ${WRKSRC}/etc/uams/.libs/uams_randnum.so ${FREENAS_ROOTFS}/etc/uams
	@${INSTALL_PROGRAM} -v ${WRKSRC}/etc/uams/.libs/uams_pam.so ${FREENAS_ROOTFS}/etc/uams
	@${INSTALL_PROGRAM} -v ${WRKSRC}/etc/uams/.libs/uams_dhx_pam.so ${FREENAS_ROOTFS}/etc/uams
	@${INSTALL_PROGRAM} -v ${WRKSRC}/etc/uams/.libs/uams_dhx2_pam.so ${FREENAS_ROOTFS}/etc/uams

	@cd ${FREENAS_ROOTFS}/etc/uams && ${LN} -fsv uams_passwd.so uams_clrtxt.so && ${LN} -fsv uams_dhx_passwd.so uams_dhx.so && ${LN} -fsv uams_dhx2_passwd.so uams_dhx2.so

.include <bsd.port.mk>
