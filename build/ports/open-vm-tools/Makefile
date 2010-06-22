PORTNAME=		freenas-open-vm-tools
PORTVERSION=		1.0
#PORTREVISION=		0
CATEGORIES=		emulators
MAINTAINER=		aoyama@peach.ne.jp
COMMENT=		Open VMware tools for FreeBSD VMware guests

DISTFILES=
NO_BUILD=		yes
NO_PKG_REGISTER=	yes
ALWAYS_BUILD_DEPENDS=	yes
DEPENDS_TARGET=		clean build

BUILD_DEPENDS=	${NONEXISTENT}:${PORTSDIR}/emulators/open-vm-tools-nox11:build

BLDSRC=${PORTSDIR}/emulators/open-vm-tools-nox11/work/open-vm-tools-*/
WRKSRC=${WRKDIR}/open-vm-tools-nox11

do-install:
	@${INSTALL_PROGRAM} -v ${BLDSRC}/libguestlib/.libs/libguestlib.so.0 ${FREENAS_ROOTFS}/${PREFIX}/lib
	@${LN} -f -v -s libguestlib.so.0 ${FREENAS_ROOTFS}/${PREFIX}/lib/libguestlib.so
	@${INSTALL_PROGRAM} -v ${BLDSRC}/libvmtools/.libs/libvmtools.so.0 ${FREENAS_ROOTFS}/${PREFIX}/lib
	@${LN} -f -v -s libvmtools.so.0 ${FREENAS_ROOTFS}/${PREFIX}/lib/libvmtools.so

	@${MKDIR} ${FREENAS_ROOTFS}/${PREFIX}/lib/open-vm-tools/plugins/common
	@${MKDIR} ${FREENAS_ROOTFS}/${PREFIX}/lib/open-vm-tools/plugins/vmsvc
	@${INSTALL_PROGRAM} -v ${BLDSRC}/services/plugins/hgfsServer/.libs/libhgfsServer.so ${FREENAS_ROOTFS}/${PREFIX}/lib/open-vm-tools/plugins/common
	@${INSTALL_PROGRAM} -v ${BLDSRC}/services/plugins/vix/.libs/libvix.so ${FREENAS_ROOTFS}/${PREFIX}/lib/open-vm-tools/plugins/common
	@${INSTALL_PROGRAM} -v ${BLDSRC}/services/plugins/guestInfo/.libs/libguestInfo.so ${FREENAS_ROOTFS}/${PREFIX}/lib/open-vm-tools/plugins/vmsvc
	@${INSTALL_PROGRAM} -v ${BLDSRC}/services/plugins/powerOps/.libs/libpowerOps.so ${FREENAS_ROOTFS}/${PREFIX}/lib/open-vm-tools/plugins/vmsvc
	@${INSTALL_PROGRAM} -v ${BLDSRC}/services/plugins/timeSync/.libs/libtimeSync.so ${FREENAS_ROOTFS}/${PREFIX}/lib/open-vm-tools/plugins/vmsvc
	@${INSTALL_PROGRAM} -v ${BLDSRC}/services/plugins/vmbackup/.libs/libvmbackup.so ${FREENAS_ROOTFS}/${PREFIX}/lib/open-vm-tools/plugins/vmsvc

	@${MKDIR} ${FREENAS_ROOTFS}/${PREFIX}/lib/vmware-tools/modules/drivers
	@${INSTALL_DATA} -v ${BLDSRC}/modules/freebsd/vmmemctl.ko ${FREENAS_ROOTFS}/${PREFIX}/lib/vmware-tools/modules/drivers/vmmemctl.ko
	@${INSTALL_DATA} -v ${BLDSRC}/modules/freebsd/vmxnet.ko ${FREENAS_ROOTFS}/${PREFIX}/lib/vmware-tools/modules/drivers/vmxnet.ko
	@${INSTALL_DATA} -v ${BLDSRC}/modules/freebsd/vmhgfs.ko ${FREENAS_ROOTFS}/${PREFIX}/lib/vmware-tools/modules/drivers/vmhgfs.ko
	@${INSTALL_DATA} -v ${BLDSRC}/modules/freebsd/vmblock.ko ${FREENAS_ROOTFS}/${PREFIX}/lib/vmware-tools/modules/drivers/vmblock.ko

	@${INSTALL_PROGRAM} -v ${BLDSRC}/services/vmtoolsd/.libs/vmtoolsd ${FREENAS_ROOTFS}/${PREFIX}/bin
	@${INSTALL_PROGRAM} -v ${BLDSRC}/checkvm/vmware-checkvm ${FREENAS_ROOTFS}/${PREFIX}/bin
	@${INSTALL_PROGRAM} -v ${BLDSRC}/hgfsclient/.libs/vmware-hgfsclient ${FREENAS_ROOTFS}/${PREFIX}/bin
	@${INSTALL_PROGRAM} -v ${BLDSRC}/rpctool/vmware-rpctool ${FREENAS_ROOTFS}/${PREFIX}/bin
	@${INSTALL_PROGRAM} -v ${BLDSRC}/toolbox/.libs/vmware-toolbox-cmd ${FREENAS_ROOTFS}/${PREFIX}/bin
	@${INSTALL_PROGRAM} -v ${BLDSRC}/vmblock-fuse/vmware-vmblock-fuse ${FREENAS_ROOTFS}/${PREFIX}/bin
	@${INSTALL_PROGRAM} -v ${BLDSRC}/xferlogs/vmware-xferlogs ${FREENAS_ROOTFS}/${PREFIX}/bin

	@${INSTALL_PROGRAM} -v ${BLDSRC}/hgfsmounter/mount.vmhgfs ${FREENAS_ROOTFS}/${PREFIX}/sbin/mount_vmhgfs

	@${MKDIR} ${FREENAS_ROOTFS}/${PREFIX}/share/vmware-tools
	@${INSTALL_SCRIPT} -v ${BLDSRC}/scripts/poweroff-vm-default ${FREENAS_ROOTFS}/${PREFIX}/share/vmware-tools
	@${INSTALL_SCRIPT} -v ${BLDSRC}/scripts/poweron-vm-default ${FREENAS_ROOTFS}/${PREFIX}/share/vmware-tools
	@${INSTALL_SCRIPT} -v ${BLDSRC}/scripts/resume-vm-default ${FREENAS_ROOTFS}/${PREFIX}/share/vmware-tools
	@${INSTALL_SCRIPT} -v ${BLDSRC}/scripts/suspend-vm-default ${FREENAS_ROOTFS}/${PREFIX}/share/vmware-tools
	@${INSTALL_SCRIPT} -v ${BLDSRC}/scripts/common/vm-support ${FREENAS_ROOTFS}/${PREFIX}/share/vmware-tools

.include <bsd.port.mk>
.include <../freenas.mk>
