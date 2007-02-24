build:	clean
	tar -zxvf ${.CURDIR}/files/syslogd_clog-current.tgz -C /usr/src/usr.bin 
	tar -zxvf ${.CURDIR}/files/clog-1.0.1.tar.gz -C /usr/src/usr.bin

	make -C /usr/src/usr.bin/syslogd

	gcc /usr/src/usr.bin/clog/clog.c -o /usr/src/usr.bin/clog/clog

install:
	install -vs /usr/src/usr.bin/syslogd/syslogd $(FREENAS)/usr/sbin
	install -vs /usr/src/usr.bin/clog/clog $(FREENAS)/usr/sbin

clean:
