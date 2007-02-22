PORTDIR=	/usr/ports/security/pam_ldap

all:	clean
	make -C $(PORTDIR)

install:
	install -vs $(PORTDIR)/work/pam_ldap-*/pam_ldap.so $(FREENAS)/usr/local/lib

clean:
	make -C $(PORTDIR) clean
