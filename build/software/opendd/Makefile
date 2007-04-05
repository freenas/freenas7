PKGNAME=	opendd
COMMENT=	A small DynDNS client
MAINTAINER=	votdev@gmx.de

BUILD_DEPENDS=	opendd:${PORTSDIR}/dns/opendd

pre-depends:
# Copy opendd options file
	@${MKDIR} -v /var/db/ports/opendd
	@${CP} -pv ${FILESDIR}/options /var/db/ports/opendd

do-install:
	@${INSTALL_PROGRAM} ${PORTSDIR}/dns/opendd/work/opendd*/opendd $(FREENAS)/usr/local/sbin

.include "bsd.freenas.mk"
