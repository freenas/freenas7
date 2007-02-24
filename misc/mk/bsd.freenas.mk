# Start of options section
UNAME?=	/usr/bin/uname

# FILESDIR - A directory containing any miscellaneous additional files.
#            Default: ${.CURDIR}/files
FILESDIR?=	${.CURDIR}/files

# Get the architecture
.if !defined(ARCH)
ARCH!=	${UNAME} -p
.endif
