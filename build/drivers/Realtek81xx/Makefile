ARCHIVE=	rtl_bsd_drv_v172.tgz
SRCDIR=	${FREENAS_WORKINGDIR}/rtl_bsd_drv_v173

install:	clean
	@tar -zxvf ${.CURDIR}/files/$(ARCHIVE) -C ${FREENAS_WORKINGDIR}

	@if [ ! -e /sys/modules/rl.orig ]; then \
		cp -pvR /sys/modules/rl /sys/modules/rl.orig; \
	fi
	@cp -pv $(SRCDIR)/Makefile /sys/modules/rl

	@if [ ! -e /sys/pci/if_rl.c.orig ]; then \
		cp -pv /sys/pci/if_rl.c /sys/pci/if_rl.c.orig; \
	fi
	@if [ ! -e /sys/pci/if_rlreg.h.orig ]; then \
		cp -pv /sys/pci/if_rlreg.h /sys/pci/if_rlreg.h.orig; \
	fi
	@cp -pv $(SRCDIR)/if_rl.c /sys/pci
	@cp -pv $(SRCDIR)/if_rlreg.h /sys/pci

clean:
	@rm -f -r $(SRCDIR)
