install:
	@for i in `cd files && ls *-options | sed -e 's/-options//`; do \
		mkdir -pv /var/db/ports/$$i; \
		cp -pv files/$$i-options /var/db/ports/$$i/options; \
	done;
