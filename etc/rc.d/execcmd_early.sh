#!/bin/sh
# Copyright (c) 2007 Volker Theile (votdev@gmx.de)
# All rights reserved.

# PROVIDE: execcmd_early
# REQUIRE: system_init rcconf
# BEFORE: CONFIG

. /etc/rc.subr
. /etc/configxml.subr

_index=`/usr/local/bin/xml sel -t -v "count(//system/earlyshellcmd)" ${configxml_file}`
while [ ${_index} -gt 0 ]
do
	_cmd=`configxml_get "//system/earlyshellcmd[${_index}]"`
	eval ${_cmd}
	_index=$(( ${_index} - 1 ))
done
