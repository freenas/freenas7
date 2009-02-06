#!/bin/sh
# Copyright (c) 2007-2009 Volker Theile (votdev@gmx.de)
# All rights reserved.

# PROVIDE: execcmd_preinit
# BEFORE: CONFIG
# REQUIRE: SYSTEMINIT rcconf

. /etc/rc.subr
. /etc/configxml.subr

# Execute all commands.
/usr/local/bin/xml sel -t -m "//rc/preinit/cmd" \
	-v "." \
	-i "position() != last()" -n -b \
	${configxml_file} | /usr/local/bin/xml unesc | \
	while read _cmd; do
		eval ${_cmd}
	done
