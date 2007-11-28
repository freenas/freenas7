# http://stsp.name/wol
PATCH=	FreeBSD-6.2-wol-2007-11-25.diff

install:	clean
	@patch --strip=0 --directory=/usr/src < ${.CURDIR}/files/${PATCH}

	@cd /usr/src; make includes
	@cd /usr/src/sbin/ifconfig; make clean obj; make && make install

clean:
# Check if patch is already applied, then reverse patch.
	-@patch --silent --check --reverse --forward --directory=/usr/src < ${.CURDIR}/files/${PATCH}; \
	if [ $$? = 0 ]; then \
		patch --reverse --strip=0 --directory=/usr/src < ${.CURDIR}/files/${PATCH}; \
	fi
