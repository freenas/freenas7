install:
	# Add support for nForce MCP61 chipset. http://www.freebsd.org/cgi/query-pr.cgi?q=108830
	@patch --forward --remove-empty-files --unified --directory=/usr/src/sys/dev < ${.CURDIR}/files/patch-nve.diff
