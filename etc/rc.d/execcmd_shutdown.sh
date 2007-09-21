#!/bin/sh
# Copyright (c) 2007 Volker Theile (votdev@gmx.de)
# All rights reserved.

# PROVIDE: execcmd_shutdown
# REQUIRE: LOGIN
# KEYWORD: nostart shutdown

. /etc/rc.subr
. /etc/configxml.subr

_index=`configxml_get_count "//rc/shutdown/cmd"`
while [ ${_index} -gt 0 ]
do
	_cmd=`configxml_get "//rc/shutdown/cmd[${_index}]"`
	eval ${_cmd}
	_index=$(( ${_index} - 1 ))
done
