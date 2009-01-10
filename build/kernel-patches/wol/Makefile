install:
	# Wake on LAN patch
	@patch -p2 --forward --remove-empty-files --unified --directory=/usr/src < ${.CURDIR}/files/FreeBSD-6.3-wol.diff

	# Rebuild 'ifconfig'
	@cd /usr/src; make includes
	@cd /usr/src/sbin/ifconfig; make clean obj; make && make install
