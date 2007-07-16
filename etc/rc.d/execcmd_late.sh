#!/bin/sh
# Copyright © 2007 Volker Theile (votdev@gmx.de)
# All rights reserved.

# PROVIDE: execcmd_late
# REQUIRE: LOGIN

. /etc/rc.subr
. /etc/configxml.subr

_index=`/usr/local/bin/xml sel -t -v "count(//system/shellcmd)" ${configxml_file}`
while [ ${_index} -gt 0 ]
do
	_cmd=`/usr/local/bin/xml sel -t -v "//system/shellcmd[${_index}]" ${configxml_file}`
	eval ${_cmd}
	_index=$(( ${_index} - 1 ))
done
