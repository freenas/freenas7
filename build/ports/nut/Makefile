PORTNAME=			nut
PORTVERSION=	2.4.1
CATEGORIES=		sysutils
COMMENT=			Network UPS tools
MAINTAINER=		michael.zoon@freenas.org

MASTER_SITES=	http://www.networkupstools.org/source/${PORTVERSION:R}/
DISTFILES=		${DISTNAME}${EXTRACT_SUFX} ${DISTNAME}${EXTRACT_SUFX}.sig
EXTRACT_ONLY=	${DISTNAME}${EXTRACT_SUFX}

LIB_DEPENDS+=		usb-0.1.8:${PORTSDIR}/devel/libusb

USE_AUTOTOOLS=	autoconf:262
GNU_CONFIGURE=	yes
USE_GMAKE=			yes
USE_LDCONFIG=		yes
USE_GNOME=			pkgconfig

NUT_USER=				root
NUT_GROUP=			root
NUT_PIDDIR=			/var/run
NUT_STATEDIR=		/var/db/nut
NUT_SYSCONFDIR=	/var/etc
NUT_DATADIR=		/usr/local/etc/${PORTNAME}
NUT_DRVDIR=			/usr/local/libexec/${PORTNAME}

CONFIGURE_ARGS=	--sysconfdir=${NUT_SYSCONFDIR} \
								--program-transform-name="" \
								--localstatedir=${NUT_STATEDIR} \
								--datadir=${NUT_DATADIR} \
								--with-drvpath=${NUT_DRVDIR} \
								--with-statepath=${NUT_STATEDIR} \
								--with-altpidpath=${NUT_PIDDIR} \
								--with-pidpath=${NUT_PIDDIR} \
								--without-pkgconfig-dir \
								--without-cgi \
								--with-serial \
								--with-usb=auto \
								--without-snmp \
								--without-neonxml \
								--without-powerman \
								--without-hal \
								--with-ipv6 \
								--without-dev \
								--with-user=${NUT_USER} \
								--with-group=${NUT_GROUP} \
								--with-wrap

do-install:
	@${MKDIR} -pv ${FREENAS_ROOTFS}/${NUT_DATADIR}
	@${INSTALL_DATA} -v ${WRKSRC}/data/cmdvartab ${FREENAS_ROOTFS}/${NUT_DATADIR}
	@${INSTALL_DATA} -v ${WRKSRC}/data/driver.list ${FREENAS_ROOTFS}/${NUT_DATADIR}

	@${MKDIR} -pv ${FREENAS_ROOTFS}/${NUT_DRVDIR}
	@${INSTALL_PROGRAM} -v ${WRKSRC}/drivers/apcsmart ${FREENAS_ROOTFS}/${NUT_DRVDIR}
	@${INSTALL_PROGRAM} -v ${WRKSRC}/drivers/bcmxcp ${FREENAS_ROOTFS}/${NUT_DRVDIR}
	@${INSTALL_PROGRAM} -v ${WRKSRC}/drivers/bcmxcp_usb ${FREENAS_ROOTFS}/${NUT_DRVDIR}
	@${INSTALL_PROGRAM} -v ${WRKSRC}/drivers/belkin ${FREENAS_ROOTFS}/${NUT_DRVDIR}
	@${INSTALL_PROGRAM} -v ${WRKSRC}/drivers/belkinunv ${FREENAS_ROOTFS}/${NUT_DRVDIR}
	@${INSTALL_PROGRAM} -v ${WRKSRC}/drivers/bestfcom ${FREENAS_ROOTFS}/${NUT_DRVDIR}
	@${INSTALL_PROGRAM} -v ${WRKSRC}/drivers/bestuferrups ${FREENAS_ROOTFS}/${NUT_DRVDIR}
	@${INSTALL_PROGRAM} -v ${WRKSRC}/drivers/bestups ${FREENAS_ROOTFS}/${NUT_DRVDIR}
	@${INSTALL_PROGRAM} -v ${WRKSRC}/drivers/blazer_ser ${FREENAS_ROOTFS}/${NUT_DRVDIR}
	@${INSTALL_PROGRAM} -v ${WRKSRC}/drivers/blazer_usb ${FREENAS_ROOTFS}/${NUT_DRVDIR}
	@${INSTALL_PROGRAM} -v ${WRKSRC}/drivers/cyberpower ${FREENAS_ROOTFS}/${NUT_DRVDIR}
	@${INSTALL_PROGRAM} -v ${WRKSRC}/drivers/etapro ${FREENAS_ROOTFS}/${NUT_DRVDIR}
	@${INSTALL_PROGRAM} -v ${WRKSRC}/drivers/everups ${FREENAS_ROOTFS}/${NUT_DRVDIR}
	@${INSTALL_PROGRAM} -v ${WRKSRC}/drivers/gamatronic ${FREENAS_ROOTFS}/${NUT_DRVDIR}
	@${INSTALL_PROGRAM} -v ${WRKSRC}/drivers/genericups ${FREENAS_ROOTFS}/${NUT_DRVDIR}
	@${INSTALL_PROGRAM} -v ${WRKSRC}/drivers/isbmex ${FREENAS_ROOTFS}/${NUT_DRVDIR}
	@${INSTALL_PROGRAM} -v ${WRKSRC}/drivers/liebert ${FREENAS_ROOTFS}/${NUT_DRVDIR}
	@${INSTALL_PROGRAM} -v ${WRKSRC}/drivers/masterguard ${FREENAS_ROOTFS}/${NUT_DRVDIR}
	@${INSTALL_PROGRAM} -v ${WRKSRC}/drivers/megatec ${FREENAS_ROOTFS}/${NUT_DRVDIR}
	@${INSTALL_PROGRAM} -v ${WRKSRC}/drivers/megatec_usb ${FREENAS_ROOTFS}/${NUT_DRVDIR}
	@${INSTALL_PROGRAM} -v ${WRKSRC}/drivers/metasys ${FREENAS_ROOTFS}/${NUT_DRVDIR}
	@${INSTALL_PROGRAM} -v ${WRKSRC}/drivers/mge-shut ${FREENAS_ROOTFS}/${NUT_DRVDIR}
	@${INSTALL_PROGRAM} -v ${WRKSRC}/drivers/mge-utalk ${FREENAS_ROOTFS}/${NUT_DRVDIR}
	@${INSTALL_PROGRAM} -v ${WRKSRC}/drivers/microdowell ${FREENAS_ROOTFS}/${NUT_DRVDIR}
	@${INSTALL_PROGRAM} -v ${WRKSRC}/drivers/newmge-shut ${FREENAS_ROOTFS}/${NUT_DRVDIR}
	@${INSTALL_PROGRAM} -v ${WRKSRC}/drivers/oneac ${FREENAS_ROOTFS}/${NUT_DRVDIR}
	@${INSTALL_PROGRAM} -v ${WRKSRC}/drivers/optiups ${FREENAS_ROOTFS}/${NUT_DRVDIR}
	@${INSTALL_PROGRAM} -v ${WRKSRC}/drivers/powercom ${FREENAS_ROOTFS}/${NUT_DRVDIR}
	@${INSTALL_PROGRAM} -v ${WRKSRC}/drivers/powerpanel ${FREENAS_ROOTFS}/${NUT_DRVDIR}
	@${INSTALL_PROGRAM} -v ${WRKSRC}/drivers/rhino ${FREENAS_ROOTFS}/${NUT_DRVDIR}
	@${INSTALL_PROGRAM} -v ${WRKSRC}/drivers/richcomm_usb ${FREENAS_ROOTFS}/${NUT_DRVDIR}
	@${INSTALL_PROGRAM} -v ${WRKSRC}/drivers/safenet ${FREENAS_ROOTFS}/${NUT_DRVDIR}
	@${INSTALL_PROGRAM} -v ${WRKSRC}/drivers/skel ${FREENAS_ROOTFS}/${NUT_DRVDIR}
	@${INSTALL_PROGRAM} -v ${WRKSRC}/drivers/solis ${FREENAS_ROOTFS}/${NUT_DRVDIR}
	@${INSTALL_PROGRAM} -v ${WRKSRC}/drivers/tripplite ${FREENAS_ROOTFS}/${NUT_DRVDIR}
	@${INSTALL_PROGRAM} -v ${WRKSRC}/drivers/tripplite_usb ${FREENAS_ROOTFS}/${NUT_DRVDIR}
	@${INSTALL_PROGRAM} -v ${WRKSRC}/drivers/tripplitesu ${FREENAS_ROOTFS}/${NUT_DRVDIR}
	@${INSTALL_PROGRAM} -v ${WRKSRC}/drivers/upscode2 ${FREENAS_ROOTFS}/${NUT_DRVDIR}
	@${INSTALL_PROGRAM} -v ${WRKSRC}/drivers/upsdrvctl ${FREENAS_ROOTFS}/${NUT_DRVDIR}
	@${INSTALL_PROGRAM} -v ${WRKSRC}/drivers/usbhid-ups ${FREENAS_ROOTFS}/${NUT_DRVDIR}
	@${INSTALL_PROGRAM} -v ${WRKSRC}/drivers/victronups ${FREENAS_ROOTFS}/${NUT_DRVDIR}

	@${INSTALL_PROGRAM} -v ${WRKSRC}/clients/.libs/libupsclient.so.1 ${FREENAS_ROOTFS}/usr/local/lib
	@${LN} -f -v -s /usr/local/lib/libupsclient.so.1 ${FREENAS_ROOTFS}/usr/local/lib/libupsclient.so

	@${INSTALL_PROGRAM} -v ${WRKSRC}/clients/.libs/upsc ${FREENAS_ROOTFS}/usr/local/bin
	@${INSTALL_PROGRAM} -v ${WRKSRC}/clients/.libs/upscmd ${FREENAS_ROOTFS}/usr/local/bin
	@${INSTALL_PROGRAM} -v ${WRKSRC}/clients/.libs/upslog ${FREENAS_ROOTFS}/usr/local/bin
	@${INSTALL_PROGRAM} -v ${WRKSRC}/clients/.libs/upsrw ${FREENAS_ROOTFS}/usr/local/bin
	@${INSTALL_SCRIPT} -v ${FILESDIR}/upssched-cmd.in ${FREENAS_ROOTFS}/usr/local/bin/upssched-cmd

	@${INSTALL_PROGRAM} -v ${WRKSRC}/server/upsd ${FREENAS_ROOTFS}/usr/local/sbin
	@${INSTALL_PROGRAM} -v ${WRKSRC}/clients/.libs/upsmon ${FREENAS_ROOTFS}/usr/local/sbin
	@${INSTALL_PROGRAM} -v ${WRKSRC}/clients/upssched ${FREENAS_ROOTFS}/usr/local/sbin

	@${INSTALL_SCRIPT} -v ${FILESDIR}/nut.sh.in ${FREENAS_ROOTFS}/etc/rc.d/nut
	@${INSTALL_SCRIPT} -v ${FILESDIR}/nut_upslog.sh.in ${FREENAS_ROOTFS}/etc/rc.d/nut_upslog
	@${INSTALL_SCRIPT} -v ${FILESDIR}/nut_upsmon.sh.in ${FREENAS_ROOTFS}/etc/rc.d/nut_upsmon

.include <bsd.port.mk>
