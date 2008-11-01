# Patch number of kernel page tables to allow booting of larger compressed mfs_root images
# on systems with > 2GB RAM.
# http://markmail.org/message/737aymqrbntjxktc

UNAME?=		/usr/bin/uname

# Get the architecture
.if !defined(ARCH)
ARCH!=	${UNAME} -p
.endif

install:
	@if [ "${ARCH}" != "amd64" ]; then \
		patch --forward --remove-empty-files --unified --directory=/sys/i386/include < ${.CURDIR}/files/pmap.h.diff; \
	fi
