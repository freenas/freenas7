PORTNAME=			unison
PORTVERSION=	1.0
PORTREVISION=	0
PORTEPOCH=		2
CATEGORIES=		net
COMMENT=			A user-level file synchronization tool
MAINTAINER=		votdev@gmx.de

BUILD_DEPENDS=	unison:${PORTSDIR}/net/unison
# No build is required for this port.
NO_BUILD?=			1

post-patch:
# Copy ocaml options file
	@${MKDIR} -v /var/db/ports/ocaml
	@${CP} -pv ${FILESDIR}/ocaml-options /var/db/ports/ocaml/options

do-install:
	@${INSTALL_PROGRAM} -v ${PORTSDIR}/net/unison/work/unison*/unison ${FREENAS_ROOTFS}/usr/local/bin

.include "bsd.freenas.mk"
