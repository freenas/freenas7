PORTNAME=			samba
PORTVERSION=	3.0.28a
PORTREVISION=	1
PORTEPOCH=		1
CATEGORIES=		net
COMMENT=			A free SMB and CIFS client and server for UNIX
MAINTAINER=		votdev@gmx.de

MASTER_SITES=				${MASTER_SITE_SAMBA}
MASTER_SITE_SUBDIR=	. old-versions rc pre
DISTNAME=						${PORTNAME}-${PORTVERSION:S/.p/pre/:S/.r/rc/}

SAMBA_LOGFILEBASE=		/var/log/samba
SAMBA_PIDDIR=					/var/run
SAMBA_PRIVATEDIR=			/var/etc/private
SAMBA_CONFIGDIR=			/var/etc
SAMBA_LOCKDIR=				/var/run
SAMBA_LIBDIR=					/usr/local/lib
SAMBA_MODULEDIR=			${SAMBA_LIBDIR}/samba

USE_ICONV=				yes
GNU_CONFIGURE=		yes
USE_AUTOTOOLS=		autoconf:261 autoheader:261
AUTOHEADER_ARGS=	-I${WRKSRC} -I${WRKSRC}/lib/replace
AUTOCONF_ARGS=		-I${WRKSRC} -I${WRKSRC}/lib/replace

WRKSRC=						${WRKDIR}/${DISTNAME}/source

CONFIGURE_ARGS+=	--disable-cups \
									--with-ads \
									--with-pam \
									--with-ldapsam \
									--with-winbind \
									--with-pam_smbpass \
									--with-libdir="${SAMBA_MODULEDIR}" \
									--with-logfilebase="${SAMBA_LOGFILEBASE}" \
									--with-privatedir="${SAMBA_PRIVATEDIR}" \
									--with-configdir="${SAMBA_CONFIGDIR}" \
									--with-lockdir="${SAMBA_LOCKDIR}" \
									--with-piddir="${SAMBA_PIDDIR}" \
									--with-shared-modules=idmap_rid \
									--with-pammodulesdir="${SAMBA_LIBDIR}" \
									--with-syslog \
									--with-ldap \
									--with-libiconv="${LOCALBASE}" \
									--with-sendfile-support \
									--with-acl-support

do-install:
	@${INSTALL_PROGRAM} -v ${WRKSRC}/bin/smbd ${FREENAS_ROOTFS}/usr/local/sbin
	@${INSTALL_PROGRAM} -v ${WRKSRC}/bin/nmbd ${FREENAS_ROOTFS}/usr/local/sbin
	@${INSTALL_PROGRAM} -v ${WRKSRC}/bin/winbindd ${FREENAS_ROOTFS}/usr/local/sbin
	@${INSTALL_PROGRAM} -v ${WRKSRC}/bin/wbinfo ${FREENAS_ROOTFS}/usr/local/bin
	@${INSTALL_PROGRAM} -v ${WRKSRC}/bin/net ${FREENAS_ROOTFS}/usr/local/bin
	@${INSTALL_PROGRAM} -v ${WRKSRC}/bin/smbpasswd ${FREENAS_ROOTFS}/usr/local/bin
	@${INSTALL_PROGRAM} -v ${WRKSRC}/bin/smbstatus ${FREENAS_ROOTFS}/usr/bin
	@${INSTALL_PROGRAM} -v ${WRKSRC}/bin/smbcontrol ${FREENAS_ROOTFS}/usr/bin
	@${INSTALL_PROGRAM} -v ${WRKSRC}/bin/smbtree ${FREENAS_ROOTFS}/usr/bin

	@${MKDIR} -v ${FREENAS_ROOTFS}${SAMBA_MODULEDIR}/charset
	@${MKDIR} -v ${FREENAS_ROOTFS}${SAMBA_MODULEDIR}/rpc
	@${MKDIR} -v ${FREENAS_ROOTFS}${SAMBA_MODULEDIR}/pdb
	@${MKDIR} -v ${FREENAS_ROOTFS}${SAMBA_MODULEDIR}/idmap
	@${MKDIR} -v ${FREENAS_ROOTFS}${SAMBA_MODULEDIR}/vfs

	@${CP} -pv ${WRKSRC}/bin/CP*.so ${FREENAS_ROOTFS}${SAMBA_MODULEDIR}/charset
	@${CP} -pv ${WRKSRC}/codepages/*.dat ${FREENAS_ROOTFS}${SAMBA_MODULEDIR}
	@${CP} -pv ${WRKSRC}/bin/recycle.so ${FREENAS_ROOTFS}${SAMBA_MODULEDIR}/vfs
	@${CP} -pv $(WRKSRC)/bin/netatalk.so ${FREENAS_ROOTFS}${SAMBA_MODULEDIR}/vfs
	@${CP} -pv ${WRKSRC}/bin/rid.so ${FREENAS_ROOTFS}${SAMBA_MODULEDIR}/idmap

	@${INSTALL_PROGRAM} -v ${WRKSRC}/bin/pam_smbpass.so ${FREENAS_ROOTFS}${SAMBA_LIBDIR}
	@${INSTALL_PROGRAM} -v ${WRKSRC}/bin/pam_winbind.so ${FREENAS_ROOTFS}${SAMBA_LIBDIR}
	@${INSTALL_PROGRAM} -v ${WRKSRC}/nsswitch/nss_winbind.so ${FREENAS_ROOTFS}${SAMBA_LIBDIR}
	@${INSTALL_PROGRAM} -v ${WRKSRC}/nsswitch/nss_wins.so ${FREENAS_ROOTFS}${SAMBA_LIBDIR}
	@${LN} -f -v -s ${SAMBA_LIBDIR}/nss_winbind.so ${FREENAS_ROOTFS}${SAMBA_LIBDIR}/nss_winbind.so.1
	@${LN} -f -v -s ${SAMBA_LIBDIR}/nss_wins.so ${FREENAS_ROOTFS}${SAMBA_LIBDIR}/nss_wins.so.1

.include <bsd.port.mk>
