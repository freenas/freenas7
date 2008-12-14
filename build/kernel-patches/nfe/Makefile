# Add nVidia nForce MCP on-board ethernet.
# See http://www.f.csce.kyushu-u.ac.jp/~shigeaki/software/freebsd-nfe.html

install:
	@mkdir -pv /sys/dev/nfe
	@mkdir -pv /sys/modules/nfe
	
	@cp -pv ${.CURDIR}/files/if_nfe.c /sys/dev/nfe
	@cp -pv ${.CURDIR}/files/if_nfereg.h /sys/dev/nfe
	@cp -pv ${.CURDIR}/files/if_nfevar.h /sys/dev/nfe
	@cp -pv ${.CURDIR}/files/Makefile /sys/modules/nfe

	@echo "dev/nfe/if_nfe.c		optional nfe" >> /sys/conf/files
	@patch -p1 --forward --remove-empty-files --directory=/sys/dev/nfe < ${.CURDIR}/files/wol_nfe.patch
	@patch -p1 --forward --remove-empty-files --unified --directory=/sys/dev/mii < ${.CURDIR}/files/rlphy.20070314.fbsd62stable.patch
	@patch -p1 --forward --remove-empty-files --unified --directory=/sys/dev/mii < ${.CURDIR}/files/ciphy.20070402.fbsd62stable.patch

clean:
	@rm -f -r /sys/dev/nfe /sys/modules/nfe
