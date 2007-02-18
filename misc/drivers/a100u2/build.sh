#!/usr/bin/env bash

# Description displayed in dialog menu
MENUDESC="A100U2 U2W-SCSI-Controller"
# Is dialog menu selected [ ON | OFF ]
MENUSTATUS="ON"

add_a100u2() {
	# Extract sources
	mkdir -p $TMPDIR/a100
	cd $TMPDIR/a100
	tar -zxvf $SVNDIR/misc/drivers/a100u2/files/bsd4a100.zip

	# Copy sources
	cp a100.* /usr/src/sys/pci
	echo "pci/a100.c optional ihb device-driver" >> /usr/src/sys/conf/files

	# Cleanup
	rm -r $TMPDIR/a100

	return 0
}
