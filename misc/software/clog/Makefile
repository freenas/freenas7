all:
	tar -zxvf ${.CURDIR}/files/syslogd_clog-current.tgz -C /usr/src/usr.sbin 
	tar -zxvf ${.CURDIR}/files/clog-1.0.1.tar.gz -C /usr/src/usr.sbin

	make -C /usr/src/usr.sbin/syslogd

	gcc /usr/src/usr.sbin/clog/clog.c -o /usr/src/usr.sbin/clog/clog

install:
	install -vs /usr/src/usr.sbin/syslogd/syslogd $(FREENAS)/usr/sbin
	install -vs /usr/src/usr.sbin/clog/clog $(FREENAS)/usr/sbin

clean:
