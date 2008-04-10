PORTNAME=			arcconf
DISTVERSION=	v5_20_17414
CATEGORIES=		sysutils
COMMENT=			Adaptec SCSI RAID administration tool
MAINTAINER=		votdev@gmx.de

MASTER_SITES=	http://download.adaptec.com/raid/storage_manager/
DISTNAME=			cli_freebsd${DIST_EXT}_${DISTVERSION}

ONLY_FOR_ARCHS=	i386 amd64
RESTRICTED=			May not be redistributed in binary form
NO_CDROM=				yes
NO_BUILD=				yes
NO_WRKSUBDIR=		yes

.include <bsd.port.pre.mk>

.if ${OSVERSION} >= 700000
LIB_DEPENDS+=	c.6:${PORTSDIR}/misc/compat6x
.endif

.if ${OSVERSION} >= 600000
.if ${ARCH} == amd64
DIST_EXT=	6_x64
.else
DIST_EXT=	6_x86
.endif
.endif

.if ${OSVERSION} < 600000
.if ${ARCH} == "i386"
DIST_EXT=	5_x86
.else
IGNORE=		combination OS/ARCH not supported
.endif
.endif

do-install:
	${INSTALL_PROGRAM} -v ${WRKSRC}/arcconf ${FREENAS_ROOTFS}/usr/local/sbin

.include <bsd.port.post.mk>
