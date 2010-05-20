PORTNAME=			arcconf
DISTVERSION=	v6_40_18530
CATEGORIES=		sysutils
COMMENT=			Adaptec SCSI RAID administration tool
MAINTAINER=		aoyama@peach.ne.jp

MASTER_SITES=	http://download.adaptec.com/raid/storage_manager/
DISTNAME=			asm_freebsd${DIST_TYPE}_${DIST_EXT}_${DISTVERSION}

ONLY_FOR_ARCHS=	i386 amd64
RESTRICTED=			May not be redistributed in binary form
NO_CDROM=				yes
NO_BUILD=				yes
NO_WRKSUBDIR=		yes

.include <bsd.port.pre.mk>

.if ${OSVERSION} >= 700000
DIST_TYPE=	7
EXTRACT_SUFX=	.pkg.tbz
.else
DIST_TYPE=	6
EXTRACT_SUFX=	.pkg.tgz
.endif

.if ${ARCH} == amd64
DIST_EXT=	x64
.else
DIST_EXT=	x86
.endif

do-install:
	${INSTALL_PROGRAM} -v ${WRKSRC}/opt/StorMan/arcconf ${FREENAS_ROOTFS}/${PREFIX}/sbin

.include <bsd.port.post.mk>
