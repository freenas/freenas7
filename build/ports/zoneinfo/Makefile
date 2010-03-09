PORTNAME=			zoneinfo
DISTVERSION=	2010e
CATEGORIES=		misc
COMMENT=			Updated timezone definitions
MAINTAINER=		votdev@gmx.de

MASTER_SITES=	ftp://elsie.nci.nih.gov/pub/
DISTNAME=			tzdata${DISTVERSION}

WRKSRC=				${WRKDIR}
PREFIX=				/usr

TZFILES=			africa antarctica asia australasia etcetera europe \
							factory northamerica southamerica systemv
POSIXRULES=		America/New_York

.include <bsd.port.pre.mk>

# The following country codes can be missing from /usr/share/misc/iso3166 and
# shouldn't appear in the zoneinfo file.
MISSING=	ax bl gg im je me mf rs

.ifndef NOMISSING
.for M in ${MISSING}
HAS_${M:U}!=	${GREP} -c ^${M:U} /usr/share/misc/iso3166 || ${TRUE}
.if ${HAS_${M:U}} == "0"
EXTRA_PATCHES+=	${FILESDIR}/patchremove-${M}
.endif
.endfor
.endif

do-build:
	umask 022; \
	cd ${WRKSRC}; \
	zic -d ${WRKSRC}/zoneinfo -p ${POSIXRULES} -m ${NOBINMODE} \
		${LEAPFILE} -y ${.OBJDIR}/yearistype ${TZFILES}

test-patches:
.for M in ${MISSING}
	make clean
	NOMISSING=1 EXTRA_PATCHES=${FILESDIR}/patchremove-${M} make patch
.endfor

do-install:
# Compress zoneinfo data, exclude some useless files.
	@${ECHO_CMD} "Factory" > ${WRKSRC}/zoneinfo.exlude
	@${ECHO_CMD} "posixrules" >> ${WRKSRC}/zoneinfo.exlude
	@${ECHO_CMD} "zone.tab" >> ${WRKSRC}/zoneinfo.exlude

	@${MKDIR} -p ${FREENAS_ROOTFS}/usr/share
	${TAR} -c -v -f - -X ${WRKSRC}/zoneinfo.exlude -C ${WRKSRC}/zoneinfo/ . | ${GZIP_CMD} -cv > ${FREENAS_ROOTFS}/usr/share/zoneinfo.tgz

.include <bsd.port.post.mk>
