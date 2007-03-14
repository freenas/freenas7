ARCHIVE=	geom_raid5.tbz
URL=	http://home.tiscali.de/cmdr_faako/$(ARCHIVE)

do-build:
	-@if [ ! -f $(FREENASDISTFILES)/$(ARCHIVE) ]; then \
		cd $(FREENASDISTFILES) && fetch $(URL); \
	fi

	@${TAR} -zxvf $(FREENASDISTFILES)/$(ARCHIVE) -C /usr/src

	# Make kernel module.
	@${MAKE} -C /usr/src/sys/modules/geom/geom_raid5 depend
	@${MAKE} -C /usr/src/sys/modules/geom/geom_raid5

	# Make application.
	@${MKDIR} -v /usr/include/geom/raid5
	@${CP} -pv /usr/src/sys/geom/raid5/g_raid5.h /usr/include/geom/raid5
	@${MAKE} -C /usr/src/sbin/geom/class/raid5 depend
	@${MAKE} -C /usr/src/sbin/geom/class/raid5

do-install:
	@${CP} -pv /usr/src/sys/modules/geom/geom_raid5/geom_raid5.ko $(FREENAS)/boot/kernel
	
	# How to create application binary without this command? Any ideas?
	@${MAKE} -C /usr/src/sbin/geom/class/raid5 install
	
	@${INSTALL_PROGRAM} /sbin/graid5 $(FREENAS)/sbin
	@${CP} -pv /usr/src/sbin/geom/class/raid5/geom_raid5.so $(FREENAS)/lib/geom

.include "bsd.freenas.mk"
