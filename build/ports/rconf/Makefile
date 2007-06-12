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

.include <bsd.port.pre.mk>

do-install:
	${INSTALL_SCRIPT} ${FILESDIR}/${PORTNAME} ${PREFIX}/sbin/

.include <bsd.port.post.mk>
