build:	clean
	# Copy options file
	mkdir -pv /var/db/ports/fusefs
	cp -pv ${FILESDIR}/options /var/db/ports/fusefs

	make -C /usr/ports/sysutils/fusefs-kmod
	make -C /usr/ports/sysutils/fusefs-ntfs

install:
	cp -pv /usr/ports/sysutils/fusefs-kmod/work/fuse4bsd-*/mount_fusefs/mount_fusefs $(FREENAS)/usr/local/sbin/
	cp -pv /usr/ports/sysutils/fusefs-kmod/work/fuse4bsd-*/fuse_module/fuse.ko $(FREENAS)/boot/kernel
	cp -pv /usr/ports/sysutils/fusefs-ntfs/work/ntfs-3g-*/libntfs-3g/.libs/libntfs-3g.so $(FREENAS)/usr/local/lib/libntfs-3g.so.0

	install -vs /usr/ports/sysutils/fusefs-ntfs/work/ntfs-3g-*/src/.libs/ntfs-3g $(FREENAS)/usr/local/bin/
	install -vs /usr/local/lib/libfuse.so.2 $(FREENAS)/usr/local/lib/

clean:
	make -C /usr/ports/sysutils/fusefs-kmod clean
	make -C /usr/ports/sysutils/fusefs-ntfs clean

.include "bsd.freenas.mk"
