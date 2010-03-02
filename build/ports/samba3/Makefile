PORTNAME=			samba
PORTVERSION=	3.0.37
PORTEPOCH=		1
CATEGORIES=		net
COMMENT=			A free SMB and CIFS client and server for UNIX
MAINTAINER=		votdev@gmx.de

MASTER_SITES=				${MASTER_SITE_SAMBA}
MASTER_SITE_SUBDIR=	. old-versions rc pre
DISTNAME=						${PORTNAME}-${PORTVERSION:S/.p/pre/:S/.r/rc/}

VARDIR?=						/var
SAMBA_LOGFILEBASE=	${VARDIR}/log/samba
SAMBA_PIDDIR=				${VARDIR}/run
SAMBA_PRIVATEDIR=		${VARDIR}/etc/private
SAMBA_CONFIGDIR=		${VARDIR}/etc
SAMBA_LOCKDIR=			${VARDIR}/db/samba
SAMBA_CONFIG?=			smb.conf
SAMBA_LIBDIR=				/usr/local/lib
SAMBA_MODULEDIR=		${SAMBA_LIBDIR}/samba

USE_ICONV=				yes
GNU_CONFIGURE=		yes
USE_AUTOTOOLS=		autoconf:262 autoheader:262
AUTOHEADER_ARGS=	-I${WRKSRC} -I${WRKSRC}/lib/replace
AUTOCONF_ARGS=		-I${WRKSRC} -I${WRKSRC}/lib/replace
CPPFLAGS+=				-I${LOCALBASE}/include
LDFLAGS+=					-L${LOCALBASE}/lib
CONFIGURE_ENV+=		CPPFLAGS="${CPPFLAGS}" LDFLAGS="${LDFLAGS}"
USE_OPENLDAP=			yes

WRKSRC=						${WRKDIR}/${DISTNAME}/source

CONFIGURE_ARGS+=	--localstatedir="${VARDIR}" \
									--enable-largefile \
									--disable-cups \
									--with-aio-support \
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
									--with-acl-support \
									--with-quotas \
									--with-readline \
									--without-utmp \
									--without-libsmbclient \
									--without-libmsrpc \
									--without-libsmbsharemodes \
									--without-python \
									--without-libaddns \
									--disable-debug \
									--disable-socket-wrapper \
									--disable-developer \
									--disable-krb5developer \
									--disable-dmalloc \
									--without-profiling-data \
									--without-cluster-support

post-patch:
		@${REINPLACE_CMD} -e 's/%%SAMBA_CONFIG%%/${SAMBA_CONFIG}/' \
		    ${WRKSRC}/Makefile.in

pre-build:
	cd ${WRKSRC} && ${MAKE} pch

do-install:
	@${INSTALL_SCRIPT} -v ${FILESDIR}/${PORTNAME}.in ${FREENAS_ROOTFS}/etc/rc.d/${PORTNAME}

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
