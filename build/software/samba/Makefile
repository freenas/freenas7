PORTNAME=			samba
PORTVERSION=	3.0.25
CATEGORIES=		net
COMMENT=			A free SMB and CIFS client and server for UNIX
MAINTAINER=		votdev@gmx.de

MASTER_SITES=	http://us3.samba.org/samba/ftp/stable/

SAMBA_LOGFILEBASE=		/var/log/samba
SAMBA_PIDDIR=					/var/run
SAMBA_PRIVATEDIR=			/var/etc/private
SAMBA_CONFIGDIR=			/var/etc
SAMBA_LOCKDIR=				/var/run
SAMBA_PIDDIR=					/var/run
SAMBA_PAMMODULESDIR=	/usr/local/lib

GNU_CONFIGURE=		yes
CONFIGURE_ARGS+=	--without-cups \
									--with-ads \
									--disable-cups \
									--with-pam \
									--with-ldapsam \
									--with-winbind \
									--with-pam_smbpass \
									--with-logfilebase=${SAMBA_LOGFILEBASE} \
									--with-piddir=${SAMBA_PIDDIR} \
									--with-privatedir=${SAMBA_PRIVATEDIR} \
									--with-configdir=${SAMBA_CONFIGDIR} \
									--with-lockdir=${SAMBA_LOCKDIR} \
									--with-piddir=${SAMBA_PIDDIR} \
									--with-shared-modules=idmap_rid \
									--with-pammodulesdir=${SAMBA_PAMMODULESDIR} \
									--with-syslog \
									--with-ldap
#									--with-acl-support

WRKSRC=	${WRKDIR}/${DISTNAME}/source

do-install:
	@${INSTALL_PROGRAM} ${WRKSRC}/bin/smbd ${FREENAS_ROOTFS}/usr/local/sbin
	@${INSTALL_PROGRAM} ${WRKSRC}/bin/nmbd ${FREENAS_ROOTFS}/usr/local/sbin
	@${INSTALL_PROGRAM} ${WRKSRC}/bin/winbindd ${FREENAS_ROOTFS}/usr/local/sbin
	@${INSTALL_PROGRAM} ${WRKSRC}/bin/wbinfo ${FREENAS_ROOTFS}/usr/local/bin
	@${INSTALL_PROGRAM} ${WRKSRC}/bin/net ${FREENAS_ROOTFS}/usr/local/bin
	@${INSTALL_PROGRAM} ${WRKSRC}/bin/smbpasswd ${FREENAS_ROOTFS}/usr/local/bin
	@${INSTALL_PROGRAM} ${WRKSRC}/bin/smbstatus ${FREENAS_ROOTFS}/usr/bin
	@${INSTALL_PROGRAM} ${WRKSRC}/bin/smbcontrol ${FREENAS_ROOTFS}/usr/bin
	@${INSTALL_PROGRAM} ${WRKSRC}/bin/smbtree ${FREENAS_ROOTFS}/usr/bin

	@${MKDIR} -v ${FREENAS_ROOTFS}/usr/local/lib/samba/charset
	@${MKDIR} -v ${FREENAS_ROOTFS}/usr/local/lib/samba/rpc
	@${MKDIR} -v ${FREENAS_ROOTFS}/usr/local/lib/samba/pdb
	@${MKDIR} -v ${FREENAS_ROOTFS}/usr/local/samba/lib/idmap
	@${MKDIR} -v ${FREENAS_ROOTFS}/usr/local/samba/lib/vfs

	@${CP} -pv ${WRKSRC}/bin/CP*.so ${FREENAS_ROOTFS}/usr/local/lib/samba/charset
	@${CP} -pv ${WRKSRC}/codepages/*.dat ${FREENAS_ROOTFS}/usr/local/lib/samba
	@${CP} -pv ${WRKSRC}/po/*.* ${FREENAS_ROOTFS}/usr/local/lib/samba
	@${CP} -pv ${WRKSRC}/bin/recycle.so ${FREENAS_ROOTFS}/usr/local/samba/lib/vfs
	@${CP} -pv ${WRKSRC}/bin/netatalk.so ${FREENAS_ROOTFS}/usr/local/samba/lib/vfs
	@${CP} -pv ${WRKSRC}/bin/rid.so ${FREENAS_ROOTFS}/usr/local/samba/lib/idmap

	@${INSTALL_PROGRAM} ${WRKSRC}/bin/pam_smbpass.so ${FREENAS_ROOTFS}/usr/local/lib
	@${INSTALL_PROGRAM} ${WRKSRC}/bin/pam_winbind.so ${FREENAS_ROOTFS}/usr/local/lib
	@${INSTALL_PROGRAM} ${WRKSRC}/nsswitch/nss_winbind.so ${FREENAS_ROOTFS}/usr/local/lib/nss_winbind.so.1
	@${INSTALL_PROGRAM} ${WRKSRC}/nsswitch/nss_wins.so ${FREENAS_ROOTFS}/usr/local/lib/nss_wins.so.1

.include "bsd.freenas.mk"
