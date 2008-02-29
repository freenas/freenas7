PORTNAME=			msntp
PORTVERSION=	1.6
PORTREVISION=	1
CATEGORIES=		net
COMMENT=			A straightforward SNTP daemon/utility
MAINTAINER=		votdev@gmx.de

MASTER_SITES=	ftp://ftp.openpkg.org/sources/DST/${PORTNAME}

MAKE_ARGS=		CC="${CC}" CFLAGS="${CFLAGS}" LIBS="-lm"
CFLAGS+=			-DLOCKNAME='\"/var/run/msntp.pid\"'
CFLAGS+=			-DSAVENAME='\"/var/db/msntp.state\"'

do-install:
	@${INSTALL_PROGRAM} -v ${PORTSDIR}/net/msntp/work/msntp*/msntp ${FREENAS_ROOTFS}/usr/local/bin

.include <bsd.port.mk>
