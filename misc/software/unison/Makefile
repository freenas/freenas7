PKGNAME=	unison
COMMENT=	A user-level file synchronization tool
MAINTAINER=	votdev@gmx.de

BUILD_DEPENDS=	unison:${PORTSDIR}/net/unison

pre-depends:
# Copy ocaml options file
	@${MKDIR} -v /var/db/ports/ocaml
	@${CP} -pv ${FILESDIR}/ocaml/options /var/db/ports/ocaml

do-install:
	@${INSTALL_PROGRAM} ${PORTSDIR}/net/unison/work/unison*/unison $(FREENAS)/usr/local/bin

.include "bsd.freenas.mk"
