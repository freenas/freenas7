PATCH=	patch-ata.diff

install:
	@patch --forward --remove-empty-files --unified --directory=/usr/src/sys/dev < ${.CURDIR}/files/${PATCH}
