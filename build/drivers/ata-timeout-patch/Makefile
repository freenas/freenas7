PATCH=	patch-ata.diff

install:	clean
	@patch --remove-empty-files --unified --directory=/usr/src/sys/dev < ${.CURDIR}/files/${PATCH}

clean:
# Check if patch is already applied, then reverse patch.
	-@patch --silent --check --reverse --forward --unified --directory=/usr/src/sys/dev < ${.CURDIR}/files/${PATCH}; \
	if [ $$? = 0 ]; then \
		patch --reverse --unified --directory=/usr/src/sys/dev < ${.CURDIR}/files/${PATCH}; \
	fi
