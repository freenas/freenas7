PORTDIR=	/usr/ports/net/nss_ldap

build:	clean
	make -C $(PORTDIR)

install:
	install -vs $(PORTDIR)/work/nss_ldap-*/nss_ldap.so $(FREENAS)/usr/local/lib/nss_ldap.so.1

clean:
	make -C $(PORTDIR) clean
