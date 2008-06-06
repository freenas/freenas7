install:
	# Bug in fsread in sys/boot/common/ufsread.c causes reboots on machines
	# with > 2GB RAM. http://www.freebsd.org/cgi/query-pr.cgi?pr=108215
	@patch --forward --remove-empty-files --unified --directory=/usr/src/sys/boot/common < ${.CURDIR}/files/patch-ufsread.c.diff
