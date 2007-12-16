PORTNAME=			aaccli
PORTVERSION=	1.0
CATEGORIES=		sysutils
COMMENT=			Adaptec SCSI RAID administration tool
MAINTAINER=		votdev@gmx.de

MASTER_SITES=	http://download.adaptec.com/raid/ccu/freebsd/
DISTNAME=			5400s_fbsd_cli_v10

USE_ZIP=					yes
RESTRICTED=				May not be redistributed in binary form
NO_CDROM=					yes
IA32_BINARY_PORT=	yes
NO_BUILD=					yes
NO_WRKSUBDIR=			yes

do-install:
	@${TAR} -zxf ${WRKSRC}/${PKGNAME}_${PORTREVISION}.tgz -C ${WRKSRC}
	@${INSTALL_PROGRAM} -v ${WRKSRC}/bin/aaccli ${FREENAS_ROOTFS}/usr/local/bin

.include <bsd.port.mk>
