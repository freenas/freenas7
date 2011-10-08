PORTNAME=	netatalk
PORTVERSION=	2.2.1
PORTEPOCH=	1
CATEGORIES=	net print
MASTER_SITES=	SF

MAINTAINER=	aoyama@peach.ne.jp
COMMENT=	File server for Mac OS X

LIB_DEPENDS=	gcrypt:${PORTSDIR}/security/libgcrypt

USE_AUTOTOOLS=	libtool
USE_BDB=	46+
USE_BZIP2=	yes
USE_ICONV=	yes
GNU_CONFIGURE=	yes
USE_GMAKE=	yes
USE_PERL5=	yes
WANT_GNOME=	yes

CONFIGURE_ARGS+=	--bindir=/usr/local/bin \
			--sbindir=/usr/local/sbin \
			--sysconfdir=/var/etc \
			--localstatedir=/var \
			--enable-largefile \
			--disable-tcp-wrappers \
			--disable-cups \
			--with-pam=/etc/pam.d \
			--with-uams-path=/etc/uams/ \
			--with-libiconv=${LOCALBASE} \
			--with-libgcrypt-dir=${LOCALBASE} \
			--with-bdb=${LOCALBASE}

post-patch:
	@${REINPLACE_CMD} -e 's|%%DB_NAME%%|${BDB_INCLUDE_DIR:T}| ; \
	    	s|%%DB_LIB%%|-l${BDB_LIB_NAME}|g ; \
		s|%%LOCALBASE%%|${LOCALBASE}|g' \
		${WRKSRC}/configure

do-install:
	@${INSTALL_PROGRAM} -v ${WRKSRC}/etc/afpd/afpd ${FREENAS_ROOTFS}/usr/local/sbin
	@${INSTALL_PROGRAM} -v ${WRKSRC}/etc/cnid_dbd/cnid_metad ${FREENAS_ROOTFS}/usr/local/sbin
	@${INSTALL_PROGRAM} -v ${WRKSRC}/etc/cnid_dbd/cnid_dbd ${FREENAS_ROOTFS}/usr/local/sbin

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
