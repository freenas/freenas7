ARCNAME=	geom_raid5.tbz
URL=	http://home.tiscali.de/cmdr_faako/$(ARCNAME)

all:
	-@if [ ! -f $(FREENASDISTFILES)/$(ARCNAME) ]; then \
		cd $(FREENASDISTFILES) && fetch $(URL); \
	fi

	tar -zxvf $(FREENASDISTFILES)/$(ARCNAME) -C /usr/src
	
	# Make kernel module.
	make -C /usr/src/sys/modules/geom/geom_raid5 depend
	make -C /usr/src/sys/modules/geom/geom_raid5
	
	# Make application.
	mkdir -pv /usr/include/geom/raid5
	cp -pv /usr/src/sys/geom/raid5/g_raid5.h /usr/include/geom/raid5
	make -C /usr/src/sbin/geom/class/raid5 depend
	make -C /usr/src/sbin/geom/class/raid5

install:
	cp -pv /usr/src/sys/modules/geom/geom_raid5/geom_raid5.ko $(FREENAS)/boot/kernel
	
	# How to create application binary without this command? Any ideas?
	make -C /usr/src/sbin/geom/class/raid5 install
	
	install -vs /sbin/graid5 $(FREENAS)/sbin
	install -vs /usr/src/sbin/geom/class/raid5/geom_raid5.so $(FREENAS)/lib/geom

clean:
