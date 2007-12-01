PATCH=	if_re.diff

install:
	@patch --forward --remove-empty-files --unified --directory=/usr/src/sys/dev/re < ${.CURDIR}/files/${PATCH}
