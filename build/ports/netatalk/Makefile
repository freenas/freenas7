PORTNAME=			netatalk
PORTVERSION=	2.0.3
PORTREVISION=	4
PORTEPOCH=		1
CATEGORIES=		net
COMMENT=			File and print server for AppleTalk networks
MAINTAINER=		michael.zoon@freenas.org

MASTER_SITES=			SF
DISTNAME=					${PORTNAME}-${PORTVERSION}

USE_AUTOTOOLS=		libtool
GNU_CONFIGURE=		yes
USE_ICONV=				yes
CONFIGURE_TARGET=	--build=${MACHINE_ARCH}-portbld-freebsd${OSREL}
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

	@${MKDIR} -v ${FREENAS_ROOTFS}/etc/uams
	@${INSTALL_PROGRAM} -v ${WRKSRC}/etc/uams/.libs/uams_passwd.so ${FREENAS_ROOTFS}/etc/uams
	@${INSTALL_PROGRAM} -v ${WRKSRC}/etc/uams/.libs/uams_dhx_passwd.so ${FREENAS_ROOTFS}/etc/uams
	@${INSTALL_PROGRAM} -v ${WRKSRC}/etc/uams/.libs/uams_guest.so ${FREENAS_ROOTFS}/etc/uams
	@${INSTALL_PROGRAM} -v ${WRKSRC}/etc/uams/.libs/uams_randnum.so ${FREENAS_ROOTFS}/etc/uams

	@cd ${FREENAS_ROOTFS}/etc/uams && ${LN} -fsv uams_passwd.so uams_clrtxt.so && ${LN} -fsv uams_dhx_passwd.so uams_dhx.so

.include <bsd.port.mk>
