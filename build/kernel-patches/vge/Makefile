install:
	# VIA VX800 GigE patch
	# See http://www.freebsd.org/cgi/query-pr.cgi?pr=130846
	@patch --forward --remove-empty-files --unified --directory=/usr/src/sys/dev < ${.CURDIR}/files/patch-mii.diff
 
