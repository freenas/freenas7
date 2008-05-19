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

# Set hostname
sethostname()
{
	local _hostname

	_hostname=`configxml_get "concat(//system/hostname,'.',//system/domain)"`

	eval /usr/local/sbin/rconf attribute set hostname "${_hostname}"
}

# Set interface configuration
setifconfig()
{
	local _ifn _ifconfig_args _ipaddr _gateway _cloned_interfaces _tag _id

	#########################################################################
	# IPv4

	# LAN interface:
	_ifn=`configxml_get "//interfaces/lan/if"`
	_ifn=`get_if ${_ifn}`
	_ifconfig_args=`/usr/local/bin/xml sel -t -m "//interfaces/lan" \
		-i "ipaddr[. = 'dhcp']" -o "dhcp" -b \
		-i "ipaddr[. != 'dhcp']" -v "concat('inet ',ipaddr,'/',subnet)" -b \
		-i "media[. != 'autoselect'] and count(mediaopt) > 0" -v "concat(' media ',media,' mediaopt ',mediaopt)" -b \
		-i "count(polling) > 0" -o " polling" -b \
		-i "string-length(mtu) > 0" -v "concat(' mtu ',mtu)" -b \
		-i "string-length(extraoptions) > 0" -v "concat(' ',extraoptions)" -b \
		-m "wireless" \
			-v "concat(' ssid ',ssid,' channel ',channel)" \
			-i "string-length(stationname) > 0" -v "concat(' stationname ',stationname)" -b \
			-i "count(wep/enable) > 0 and count(wep/key) > 0" \
				-m "wep/key" \
					-v "concat(' wepkey ',position(),':',value)" \
					-i "count(txkey) > 0" -v "concat(' weptxkey ',position())" -b \
				-b \
			-b \
			-i "count(wep/enable) = 0" -o " wepmode off" -b \
			-i "contains('${_ifn}','ath') and string-length(standard) > 0" -v "concat(' mode ',standard)" -b \
			-i "mode[. = 'hostap']" \
				-i "contains('${_ifn}','ath')" -o " -mediaopt adhoc mediaopt hostap" -b \
				-i "contains('${_ifn}','wi')" -o " -mediaopt ibss mediaopt hostap" -b \
			-b \
			-i "mode[. = 'ibss'] or mode[. = 'IBSS']" \
				-i "contains('${_ifn}','ath')" -o " -mediaopt hostap mediaopt adhoc" -b \
				-i "contains('${_ifn}','wi')" -o " -mediaopt hostap mediaopt ibss" -b \
				-i "contains('${_ifn}','an')" -o " mediaopt adhoc" -b \
			-b \
			-i "mode[. = 'bss'] or mode[. = 'IBSS']" \
				-i "contains('${_ifn}','ath')" -o " -mediaopt hostap -mediaopt adhoc" -b \
				-i "contains('${_ifn}','wi')" -o " -mediaopt hostap -mediaopt ibss" -b \
				-i "contains('${_ifn}','an')" -o " -mediaopt adhoc" -b \
			-b \
			-o " up" \
		-b \
		${configxml_file} | /usr/local/bin/xml unesc`

	if [ -n "${_ifconfig_args}" ]; then
		eval /usr/local/sbin/rconf attribute set "ifconfig_${_ifn}" "${_ifconfig_args}"
	fi

	# Set gateway.
	_ipaddr=`configxml_get "//interfaces/lan/ipaddr"`
	_gateway=`configxml_get "//interfaces/lan/gateway"`
	if [ "${_ipaddr}" != "dhcp" -a -n "${_gateway}" ]; then
		eval /usr/local/sbin/rconf attribute set "defaultrouter" "${_gateway}"
	fi

	# Cloned interfaces:
	_cloned_interfaces=`/usr/local/bin/xml sel -t -m "//vlans/vlan" \
		-v "concat('vlan',id,' ')" \
		${configxml_file} | /usr/local/bin/xml unesc`

	eval /usr/local/sbin/rconf attribute set "cloned_interfaces" "${_cloned_interfaces}"

	if [ -n "${_cloned_interfaces}" ]; then
		/usr/local/bin/xml sel -t -m "//vlans/vlan" \
			-v "concat(id,' ',tag,' ',if)" \
			-i "position() != last()" -n -b \
			${configxml_file} | /usr/local/bin/xml unesc | \
			while read _id _tag _if; do
				eval /usr/local/sbin/rconf attribute set "ifconfig_vlan${_id}" "vlan ${_tag} vlandev ${_if}"
			done
	fi

	# OPT interfaces:
	_id=`configxml_get_count "//interfaces/*[contains(name(),'opt')]"`
	while [ ${_id} -gt 0 ]
	do
		_ifn=`configxml_get "//interfaces/*[name() = 'opt${_id}']/if"`
		if configxml_isset "//interfaces/*[name() = 'opt${_id}']/enable"; then
			_ifconfig_args=`/usr/local/bin/xml sel -t -m "//interfaces/*[name() = 'opt${_id}']" \
				-i "ipaddr[. = 'dhcp']" -o "dhcp" -b \
				-i "ipaddr[. != 'dhcp']" -v "concat('inet ',ipaddr,'/',subnet)" -b \
				-i "media[. != 'autoselect'] and count(mediaopt) > 0" -v "concat(' media ',media,' mediaopt ',mediaopt)" -b \
				-i "count(polling) > 0" -o " polling" -b \
				-i "string-length(mtu) > 0" -v "concat(' mtu ',mtu)" -b \
				-i "string-length(extraoptions) > 0" -v "concat(' ',extraoptions)" -b \
				-m "wireless" \
					-v "concat(' ssid ',ssid,' channel ',channel)" \
					-i "string-length(stationname) > 0" -v "concat(' stationname ',stationname)" -b \
					-i "count(wep/enable) > 0 and count(wep/key) > 0" \
						-m "wep/key" \
							-v "concat(' wepkey ',position(),':',value)" \
							-i "count(txkey) > 0" -v "concat(' weptxkey ',position())" -b \
						-b \
					-b \
					-i "count(wep/enable) = 0" -o " wepmode off" -b \
					-i "contains('${_ifn}','ath') and string-length(standard) > 0" -v "concat(' mode ',standard)" -b \
					-i "mode[. = 'hostap']" \
						-i "contains('${_ifn}','ath')" -o " -mediaopt adhoc mediaopt hostap" -b \
						-i "contains('${_ifn}','wi')" -o " -mediaopt ibss mediaopt hostap" -b \
					-b \
					-i "mode[. = 'ibss'] or mode[. = 'IBSS']" \
						-i "contains('${_ifn}','ath')" -o " -mediaopt hostap mediaopt adhoc" -b \
						-i "contains('${_ifn}','wi')" -o " -mediaopt hostap mediaopt ibss" -b \
						-i "contains('${_ifn}','an')" -o " mediaopt adhoc" -b \
					-b \
					-i "mode[. = 'bss'] or mode[. = 'IBSS']" \
						-i "contains('${_ifn}','ath')" -o " -mediaopt hostap -mediaopt adhoc" -b \
						-i "contains('${_ifn}','wi')" -o " -mediaopt hostap -mediaopt ibss" -b \
						-i "contains('${_ifn}','an')" -o " -mediaopt adhoc" -b \
					-b \
					-o " up" \
				-b \
				${configxml_file} | /usr/local/bin/xml unesc`

			if [ -n "${_ifconfig_args}" ]; then
				eval /usr/local/sbin/rconf attribute set "ifconfig_${_ifn}" "${_ifconfig_args}"
			fi
		else
			eval /usr/local/sbin/rconf attribute remove "ifconfig_${_ifn}"
		fi

		_id=$(( ${_id} - 1 ))
	done

	#########################################################################
	# IPv6

	# LAN interface:
	_ifn=`configxml_get "//interfaces/lan/if"`
	_ifn=`get_if ${_ifn}`
	_ifconfig_args=`/usr/local/bin/xml sel -t -m "//interfaces/lan" \
		-i "count(ipv6addr) > 0 and ipv6addr[. != 'auto']" \
			-v "concat('inet6 alias ',ipv6addr,'/',ipv6subnet)" \
		-b \
		${configxml_file} | /usr/local/bin/xml unesc`

	# Create ipv6_ifconfig_xxx variable only if interface is not defined as 'auto'.
	if [ -n "${_ifconfig_args}" ]; then
		eval /usr/local/sbin/rconf attribute set "ipv6_ifconfig_${_ifn}" "${_ifconfig_args}"
	fi

	# Set gateway.
	_ipaddr=`configxml_get "//interfaces/lan/ipv6addr"`
	_gateway=`configxml_get "//interfaces/lan/ipv6gateway"`
	if [ "${_ipaddr}" != "auto" -a -n "${_gateway}" ]; then
		eval /usr/local/sbin/rconf attribute set "ipv6_defaultrouter" "${_gateway}"
	fi

	# OPT interfaces:
	_id=`configxml_get_count "//interfaces/*[contains(name(),'opt')]"`
	while [ ${_id} -gt 0 ]
	do
		_ifn=`configxml_get "//interfaces/*[name() = 'opt${_id}']/if"`
		if configxml_isset "//interfaces/*[name() = 'opt${_id}']/enable"; then
			_ifconfig_args=`/usr/local/bin/xml sel -t -m "//interfaces/*[name() = 'opt${_id}']" \
				-i "count(ipv6addr) > 0 and ipv6addr[. != 'auto']" \
					-v "concat('inet6 alias ',ipv6addr,'/',ipv6subnet)" \
				-b \
				${configxml_file} | /usr/local/bin/xml unesc`

			# Create ipv6_ifconfig_xxx variable only if interface is not defined as 'auto'.
			if [ -n "${_ifconfig_args}" ]; then
				eval /usr/local/sbin/rconf attribute set "ipv6_ifconfig_${_ifn}" "${_ifconfig_args}"
			fi
		else
			eval /usr/local/sbin/rconf attribute remove "ifconfig_${_ifn}"
		fi

		_id=$(( ${_id} - 1 ))
	done
}

# Update services
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

# Set additional options.
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

updateservices
sethostname
setifconfig
setoptions

# Finally issue a line break
echo

# Force reloading of rc.conf file
_rc_conf_loaded=false
load_rc_config ${name}

return 0
