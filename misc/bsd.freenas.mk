# Start of options section
UNAME?=	/usr/bin/uname

# Get the architecture
.if !defined(ARCH)
ARCH!=	${UNAME} -p
.endif
