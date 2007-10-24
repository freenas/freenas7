ARCHIVE=	bsd4a100.zip
SRCDIR=		${FREENAS_WORKINGDIR}/a100

install:	clean
	@mkdir -pv ${SRCDIR}
	@tar -zxvf ${.CURDIR}/files/${ARCHIVE} -C ${SRCDIR}

# Create backup.
	-@if [ ! -e /usr/src/sys/conf/files.orig ]; then \
		cp -pv /usr/src/sys/conf/files /usr/src/sys/conf/files.orig; \
	fi

	@cp -pv ${SRCDIR}/a100.* /usr/src/sys/pci
	@echo "pci/a100.c optional ihb device-driver" >> /usr/src/sys/conf/files

clean:
	@rm -f -r ${SRCDIR}

	-@if [ -e /usr/src/sys/pci ]; then \
		rm -rfv /usr/src/sys/pci; \
	fi

	-@if [ -e /usr/src/sys/conf/files.orig ]; then \
		rm -fv /usr/src/sys/conf/files; \
		mv -fv /usr/src/sys/conf/files.orig /usr/src/sys/conf/files; \
	fi
