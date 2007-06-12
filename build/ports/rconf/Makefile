PORTNAME=				rconf
PORTVERSION=		1.0
PORTREVISION=		0
CATEGORIES=			sysutils

MAINTAINER=			votdev@gmx.de
COMMENT=				Command-line tool to manage content of rc.conf

NO_BUILD=		yes
NO_WRKSUBDIR=		yes

DISTFILES=
PLIST_FILES=	sbin/${PORTNAME}

do-install:
	${INSTALL_SCRIPT} ${FILESDIR}/${PORTNAME} ${FREENAS_ROOTFS}/usr/local/sbin/

.include "bsd.freenas.mk"
