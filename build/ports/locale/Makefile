PORTNAME=			locale
PORTVERSION=	1.0
CATEGORIES=		lang
COMMENT=			Language packages for FreeNAS
MAINTAINER=		votdev@gmx.de

DISTFILES?=

.include <../freenas.mk>

do-build:
# Translate *po files to *.mo.
	@for i in $$(ls ${FREENAS_SVNDIR}/locale/*.po); do \
		filename=$$(basename $$i); \
		language=$${filename%*.po}; \
		filename=$$(echo ${FREENAS_PRODUCTNAME} | tr '[A-Z]' '[a-z]'); \
		${MKDIR} -v ${WRKSRC}/$$language/LC_MESSAGES; \
		msgfmt -v --output-file="${WRKSRC}/$$language/LC_MESSAGES/$$filename.mo" $$i; \
	done

do-install:
	@${CP} -vp -r ${WRKSRC}/* ${FREENAS_ROOTFS}/usr/local/share/locale

.include <bsd.port.mk>
