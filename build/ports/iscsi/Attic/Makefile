PKGNAME=	iscsi
COMMENT=	iSCSI tools
MAINTAINER=	votdev@gmx.de

ARCHIVE=	iscsi-2.0.1.tar.bz2
URL=	ftp://ftp.cs.huji.ac.il/users/danny/freebsd/$(ARCHIVE)
SRCDIR=	${FREENAS_WORKINGDIR}/iscsi

do-build:
	-@if [ ! -f ${FREENAS_DISTFILES}/$(ARCHIVE) ]; then \
		cd ${FREENAS_DISTFILES} && fetch $(URL); \
	fi

	@${MKDIR} -v $(SRCDIR)
	@${TAR} -zxvf ${FREENAS_DISTFILES}/$(ARCHIVE) -C $(SRCDIR)

	@cd $(SRCDIR)/sys && ln -sv /sys/kern . && ln -sv /sys/tools .
	@cd $(SRCDIR)/sys/modules/iscsi_initiator && ln -sv ../.. @

	@${MAKE} -C $(SRCDIR)/sys/modules/iscsi_initiator
	@${MAKE} -C $(SRCDIR)/iscontrol

do-install:
	@${INSTALL_PROGRAM} $(SRCDIR)/iscontrol/iscontrol ${FREENAS_ROOTFS}/usr/local/sbin
	@${CP} -pv $(SRCDIR)/sys/modules/iscsi_initiator/iscsi_initiator.ko ${FREENAS_ROOTFS}/boot/kernel

do-clean:
	@${RM} -f -r ${FREENAS_WORKINGDIR}/iscsi

.include "bsd.freenas.mk"
