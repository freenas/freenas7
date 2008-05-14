#!/bin/sh
# Copyright (c) 2007-2008 Volker Theile (votdev@gmx.de)
# All rights reserved.

# PROVIDE: rcconf
# BEFORE: CONFIG
# REQUIRE: SYSTEMINIT

. /etc/rc.subr
. /etc/util.subr
. /etc/configxml.subr

name="rcconf"

updateservices()
{
	# Update rcvar's. Use settings from config.xml
	for _rcscript in /etc/rc.d/*; do
		_rcscriptname=${_rcscript#/etc/rc.d/}
		if [ "${name}.sh" != "${_rcscriptname}" ]; then
			_xquery=`grep "XQUERY:" ${_rcscript} | sed 's/.*XQUERY: \(.*\)/\1/'`
			if [ -n "${_xquery}" ]; then
				_rcvar=`grep "RCVAR:" ${_rcscript} | sed 's/.*RCVAR: \(.*\)/\1/'`
				if [ -z "${_rcvar}" ]; then
					_rcvar=${_rcscriptname}
				fi

				# Execute query.
				_queryresult=`configxml_exec_query ${_xquery}`

				# Enable/disable service depending on query result
				if [ "0" = "${_queryresult}" ]; then
					eval /usr/local/sbin/rconf service enable ${_rcvar}
					debug "rcconf.sh: ${_rcscriptname} service enabled"
				else
					eval /usr/local/sbin/rconf service disable ${_rcvar}
					debug "rcconf.sh: ${_rcscriptname} service disabled"
				fi

				echo -n "."
			fi
		fi
	done
}

setoptions()
{
	local _option _name _value

	/usr/local/bin/xml sel -t -m "//system/rcconf/param" \
		-v "concat(name,'=',value)" \
		-i "position() != last()" -n -b \
		${configxml_file} | /usr/local/bin/xml unesc | \
		while read _option; do
			_name=${_option%=*}
			_value=${_option#*=}

			eval /usr/local/sbin/rconf attribute set "${_name}" "${_value}"
		done
}

load_rc_config ${name}

echo -n "Updating rc.conf:"

# Update services
updateservices

# Set additional options.
setoptions

# Finally issue a line break
echo

# Force reloading of rc.conf file
_rc_conf_loaded=false
load_rc_config ${name}

return 0
