PORTNAME=			scponly
PORTVERSION=	1.0
PORTREVISION=	0
PORTEPOCH=		3
CATEGORIES=		shells
COMMENT=			A tiny shell that only permits scp and sftp
MAINTAINER=		votdev@gmx.de

BUILD_DEPENDS=	scponly:${PORTSDIR}/shells/scponly
# No build is required for this port.
NO_BUILD?=			1

post-patch:
# Copy ocaml options file
	@${MKDIR} -pv /var/db/ports/ocaml
	@${CP} -pv ${FILESDIR}/ocaml-options /var/db/ports/ocaml/options

# Copy rsync options file
	@${MKDIR} -pv /var/db/ports/rsync
	@${CP} -pv ${FILESDIR}/rsync-options /var/db/ports/rsync/options

# Copy scponly options file
	@${MKDIR} -pv /var/db/ports/scponly
	@${CP} -pv ${FILESDIR}/scponly-options /var/db/ports/scponly/options

do-install:
	@${INSTALL_PROGRAM} -v ${PORTSDIR}/shells/scponly/work/scponly*/scponly ${FREENAS_ROOTFS}/usr/local/bin

.include "bsd.freenas.mk"
