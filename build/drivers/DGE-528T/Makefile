PATCH=	if_re.diff

install:	clean
	@patch -E -u -d /usr/src/sys/dev/re < ${.CURDIR}/files/${PATCH}

clean:
# Check if patch is already applied, then reverse patch.
	-@patch --silent --check --reverse --forward --directory=/usr/src/sys/dev/re < ${.CURDIR}/files/${PATCH}; \
	if [ $$? = 0 ]; then \
		patch --reverse --directory=/usr/src/sys/dev/re < ${.CURDIR}/files/${PATCH}; \
	fi
