PORTNAME=			gpt
PORTVERSION=	1.0
PORTREVISION=	0
PORTEPOCH=		1
CATEGORIES=		sysutils
COMMENT=			gpt patch. !!! EXPERIMENTAL !!!
MAINTAINER=		kent@khauser.net & votdev@gmx.de

# See:
# http://www.freebsd.org/cgi/query-pr.cgi?pr=115406&cat=
# http://sourceforge.net/tracker/index.php?func=detail&aid=1772053&group_id=151951&atid=782616

WRKSRC=	/usr/src/sbin/gpt

do-build:
	-@if [ -e ${WRKSRC}/create.c.orig ]; then \
		${MV} ${WRKSRC}/create.c.orig ${WRKSRC}/create.c; \
	fi

	# Patch gpt sources.
	${PATCH} -f ${WRKSRC}/create.c ${FILESDIR}/create.c.diff

	@${MAKE} -C ${WRKSRC}

do-install:
	@${INSTALL_PROGRAM} -v ${WRKSRC}/gpt ${FREENAS_ROOTFS}/sbin

.include "bsd.freenas.mk"
