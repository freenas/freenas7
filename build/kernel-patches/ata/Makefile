install:
	# Support for ATi IXP600/700 PATA/SATA http://www.freebsd.org/cgi/query-pr.cgi?pr=116125
	@patch --forward --remove-empty-files --unified --directory=/usr/src/sys/dev < ${.CURDIR}/files/patch-ata_ati_ixp600700.diff
 