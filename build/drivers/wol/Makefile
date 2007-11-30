# Get WOL patch here: http://stsp.name/wol
PATCH=	FreeBSD-6.2-wol-2007-11-25.diff

install:
	@patch --strip=0 --directory=/usr/src < ${.CURDIR}/files/${PATCH}

	@cd /usr/src; make includes
	@cd /usr/src/sbin/ifconfig; make clean obj; make && make install
