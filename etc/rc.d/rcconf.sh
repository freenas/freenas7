#!/bin/sh
# Copyright © 2007 Volker Theile (votdev@gmx.de)
# All rights reserved.

# PROVIDE: rcconf
# REQUIRE: system_preinit
# BEFORE: CONFIG

. /etc/rc.subr
. /etc/util.subr
. /etc/configxml.subr

name="rcconf"

sethostname()
{
	local _hostname

	_hostname=`/usr/local/bin/xml sel -t -m "//system" -v hostname -o "." -v domain ${configxml_file}`

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
			_subnet=`/usr/local/bin/xml sel -t -o "${_ipaddress}/" -v "//interfaces/lan/subnet" ${configxml_file}`
			;;
	esac

	eval /usr/local/sbin/rconf attribute set "ifconfig_${_if}" "${_ifconf}"
}

echo "Updating rc.conf."

# Update rcvar's. Use settings from config.xml.
for _rcscript in /etc/rc.d/*; do
	_rcscriptname=${_rcscript#/etc/rc.d/}
	if [ "${name}.sh" != "${_rcscriptname}" ]; then
		_xpath=`cat ${_rcscript} | grep XPATH:`
		_xpath=${_xpath#*XPATH: }
		if [ -n "${_xpath}" ]; then
			_rcvar=`cat ${_rcscript} | grep RCVAR:`
			_rcvar=${_rcvar#*RCVAR: }
			if [ -z "${_rcvar}" ]; then
				_rcvar=${_rcscriptname}
			fi
			if configxml_isset ${_xpath}; then
				eval /usr/local/sbin/rconf service enable ${_rcvar}
				debug "rcconf.sh: Enable service ${_rcscriptname}"
			else
				eval /usr/local/sbin/rconf service disable ${_rcvar}
				debug "rcconf.sh: Disable service ${_rcscriptname}"
			fi
		fi
	fi
done

# Set hostname.
sethostname

# Set interface configuration.
setifconfig

# Force reloading of rc.conf file.
_rc_conf_loaded=false
load_rc_config ${name}

return 0
