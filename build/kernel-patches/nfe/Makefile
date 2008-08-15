# Add nVidia nForce MCP on-board ethernet.
# See http://www.f.csce.kyushu-u.ac.jp/~shigeaki/software/freebsd-nfe.html

install:
	@mkdir -pv /sys/dev/nfe
	@mkdir -pv /sys/modules/nfe
	
	@cp -pv ${.CURDIR}/files/if_nfe.c /sys/dev/nfe
	@cp -pv ${.CURDIR}/files/if_nfereg.h /sys/dev/nfe
	@cp -pv ${.CURDIR}/files/if_nfevar.h /sys/dev/nfe
	@cp -pv ${.CURDIR}/files/Makefile /sys/modules/nfe

	@patch --forward --remove-empty-files --unified --directory=/sys/conf < ${.CURDIR}/files/patch-files.diff
	@patch --forward --remove-empty-files --unified --directory=/sys/modules < ${.CURDIR}/files/patch-Makefile.diff
	@patch --forward --remove-empty-files --unified --directory=/sys/dev/mii < ${.CURDIR}/files/patch-phy.diff

clean:
	@rm -f -r /sys/dev/nfe /sys/modules/nfe
