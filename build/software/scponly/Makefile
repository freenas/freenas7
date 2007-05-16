PORTNAME=			scponly
PORTVERSION=	1.0
CATEGORIES=		shells
COMMENT=			A tiny shell that only permits scp and sftp
MAINTAINER=		votdev@gmx.de

BUILD_DEPENDS=	scponly:${PORTSDIR}/shells/scponly
# No build is required for this port.
NO_BUILD?=			1

pre-depends:
# Copy required options files
	@${MKDIR} -v /var/db/ports/ocaml
	@${MKDIR} -v /var/db/ports/rsync
	@${CP} -pvr ${FILESDIR}/* /var/db/ports

	@export WITH_SCPONLY_RSYNC=YES
	@export WITH_SCPONLY_SCP=YES
	@export WITH_SCPONLY_WINSCP=YES
	@export WITH_SCPONLY_UNISON=YES

do-install:
	@${INSTALL_PROGRAM} ${PORTSDIR}/shells/scponly/work/scponly*/scponly ${FREENAS_ROOTFS}/usr/local/bin

.include "bsd.freenas.mk"
