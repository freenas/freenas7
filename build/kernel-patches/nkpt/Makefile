# Patch number of kernel page tables to allow booting of larger compressed mfs_root images
# on systems with > 2GB RAM.

install:
	@patch --forward --remove-empty-files --unified --directory=/sys/i386/include < ${.CURDIR}/files/pmap.h.diff
