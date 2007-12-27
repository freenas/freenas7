#!/bin/sh
# Copyright (c) 2007 Volker Theile (votdev@gmx.de)
# All rights reserved.

# PROVIDE: execcmd_preinit
# BEFORE: CONFIG
# REQUIRE: SYSTEMINIT rcconf

. /etc/rc.subr
. /etc/configxml.subr

_index=`configxml_get_count "//rc/preinit/cmd"`
while [ ${_index} -gt 0 ]
do
	_cmd=`configxml_get "//rc/preinit/cmd[${_index}]"`
	eval ${_cmd}
	_index=$(( ${_index} - 1 ))
done
