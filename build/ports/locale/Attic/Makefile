PKGNAME=	locale
COMMENT=	Language packages for FreeNAS
MAINTAINER=	votdev@gmx.de

do-build:
	# Translate *po files to *.mo.
	@for i in $$(ls ${FREENAS_SVNDIR}/locale/*.po); do \
		filename=$$(basename $$i); \
		language=$${filename%*.po}; \
		filename=$$(echo ${FREENAS_PRODUCTNAME} | tr '[A-Z]' '[a-z]'); \
		${MKDIR} -v ${FREENAS_WORKINGDIR}/locale/$$language/LC_MESSAGES; \
		msgfmt -v --output-file="${FREENAS_WORKINGDIR}/locale/$$language/LC_MESSAGES/$$filename.mo" $$i; \
	done

do-install:
	@${CP} -vp -r ${FREENAS_WORKINGDIR}/locale/* ${FREENAS_ROOTFS}/usr/local/share/locale

do-clean:
	@${RM} -f -r ${FREENAS_WORKINGDIR}/locale

.include "bsd.freenas.mk"
