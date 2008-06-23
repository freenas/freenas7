#!/bin/sh
# Copyright (c) 2007 Volker Theile (votdev@gmx.de)
# All rights reserved.

# PROVIDE: rcconf
# REQUIRE: system_init
# BEFORE: CONFIG

. /etc/rc.subr
. /etc/util.subr
. /etc/configxml.subr

name="rcconf"

# Defaults
rc_config="/etc/rc.conf"

sethostname()
{
	local _hostname

	_hostname=`/usr/local/bin/xml sel -t -v "concat(//system/hostname,'.',//system/domain)" ${configxml_file}`

	eval /usr/local/sbin/rconf attribute set hostname "${_hostname}"
}

setifconfig()
{
	local _ipaddress _if _interfaces _ifconf _subnet

	_ipaddress=`/usr/local/bin/xml sel -t -v "//interfaces/lan/ipaddr" ${configxml_file}`
	_if=`/usr/local/bin/xml sel -t -v "//interfaces/lan/if" ${configxml_file}`
	_if=`get_if ${_if}`

	case ${_ipaddress} in
		dhcp)
			_ifconf="DHCP"
			;;
		*)
			_ifconf=`/usr/local/bin/xml sel -t -o "${_ipaddress}/" -v "//interfaces/lan/subnet" ${configxml_file}`
			;;
	esac

	eval /usr/local/sbin/rconf attribute set "ifconfig_${_if}" "${_ifconf}"
}

setmdconfig()
{
	local _disknum _cmd

	# Set new unit start id because md0 is already used on embedded for /.
	/usr/local/sbin/rconf attribute set mdconfig2_unit 1

	# Configure memory disks used by ISO files to be mounted.
	_disknum=`/usr/local/bin/xml sel -t -v "count(//mounts/mount[type = 'iso'])" ${configxml_file}`
	while [ ${_disknum} -gt 0 ]
	do
		_cmd=`/usr/local/bin/xml sel -t -m "//mounts/mount[type = 'iso'][${_disknum}]" \
			-o "/usr/local/sbin/rconf attribute set " \
			-v "concat('mdconfig_md',position(),' &quot;-t vnode -f ',filename,'&quot;')" \
			${configxml_file}`

		eval $_cmd

		_disknum=$(( ${_disknum} - 1 ))
	done
}

updateservices()
{
	# Update rcvar's. Use settings from config.xml.
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
	
				# Enable/disable service depending on query result.
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

load_rc_config ${name}

echo -n "Updating rc.conf:"

# Update services.
updateservices

# Set hostname.
sethostname

# Set interface configuration.
setifconfig

# Finally do a line break.
echo

# Force reloading of rc.conf file.
_rc_conf_loaded=false
load_rc_config ${name}

return 0
