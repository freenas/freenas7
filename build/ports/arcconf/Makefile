PORTNAME=			arcconf
DISTVERSION=	v6_50_18579
CATEGORIES=		sysutils
COMMENT=			Adaptec SCSI RAID administration tool
MAINTAINER=		michael.zoon@freenas.org

MASTER_SITES=	http://download.adaptec.com/raid/storage_manager/

DISTNAME=			asm_freebsd_${DIST_EXT}_${DISTVERSION}
EXTRACT_SUFX=	.tgz

ONLY_FOR_ARCHS=	i386 amd64
RESTRICTED=			May not be redistributed in binary form
NO_CDROM=				yes
NO_BUILD=				yes
NO_WRKSUBDIR=		yes

.include <bsd.port.pre.mk>

.if ${OSVERSION} >= 800000
DIST_TYPE=	8
.elif ${OSVERSION} >= 700000
DIST_TYPE=	7
.else
DIST_TYPE=	6
.endif

.if ${ARCH} == amd64
DIST_EXT=	x64
.else
DIST_EXT=	x86

.endif

do-install:
	${INSTALL_PROGRAM} -v ${WRKSRC}/freebsd${DIST_TYPE}${WORK_EXT}/cmdline/arcconf ${FREENAS_ROOTFS}/${PREFIX}/sbin

.include <bsd.port.post.mk>
