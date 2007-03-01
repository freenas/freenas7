do-build:	clean
	# Translate *po files to *.mo.
	@for i in $$(ls $(SVNDIR)/locale/*.po); do \
		filename=$$(basename $$i); \
		language=$${filename%*.po}; \
		filename=$$(echo $(PRODUCTNAME) | tr '[A-Z]' '[a-z]'); \
		mkdir -vp $(WORKINGDIR)/locale/$$language/LC_MESSAGES; \
		msgfmt -v --output-file="$(WORKINGDIR)/locale/$$language/LC_MESSAGES/$$filename.mo" $$i; \
	done

do-install:
	cp -vp -r $(WORKINGDIR)/locale/* $(FREENAS)/usr/local/share/locale

do-clean:
	@rm -f -r $(WORKINGDIR)/locale

.include "bsd.freenas.mk"
