PORTNAME=				rconf
PORTVERSION=		1.0
PORTREVISION=		0
CATEGORIES=			sysutils
COMMENT=				Command-line tool to manage content of rc.conf
MAINTAINER=			votdev@gmx.de

# No files are needed to be fetched.
DISTFILES?=
# No build is required for this port.
NO_BUILD?=	yes

NO_WRKSUBDIR=	yes

do-install:
	${INSTALL_SCRIPT} ${FILESDIR}/${PORTNAME} ${FREENAS_ROOTFS}/usr/local/sbin/

.include <bsd.port.mk>
