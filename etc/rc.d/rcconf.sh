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

setvar()
{
	local _platform

	# Get operating platform
	_platform=`cat /etc/platform`

	case ${_platform} in
		i386-full)
			;;
		*)
			# If running from liveCD or embedded use a memory filesystem for /var.
			eval /usr/local/sbin/rconf attribute set varmfs "YES";
			eval /usr/local/sbin/rconf attribute set varmfs_flags "-S -o union";
			eval /usr/local/sbin/rconf attribute set populate_var "NO";
			;;
	esac
}

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
	local _value _ifn _ifconfig_args _ipaddr _gateway _cloned_interfaces _id

	# Cleanup
	set | grep ^ifconfig_ | while read _value; do
		_value=${_value%=*}
		eval /usr/local/sbin/rconf attribute remove "${_value}"
	done

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
			-i "string-length(standard) > 0" -v "concat(' mode ',standard)" -b \
			-i "count(wep/enable) > 0" \
				-v "concat(' wepmode on wepkey ',wep/key,' weptxkey 1')" \
			-b \
			-i "count(wep/enable) = 0" -o " wepmode off" -b \
			-i "count(wpa/enable) > 0" -o " WPA" -b \
			-o " up" \
		-b \
		-i "starts-with(if,'vlan')" \
			-m "//vinterfaces/vlan[if = '${_ifn}']" \
				-v "concat(' vlan ',tag,' vlandev ',vlandev)" \
			-b \
		-b \
		-i "starts-with(if,'lagg')" \
			-m "//vinterfaces/lagg[if = '${_ifn}']" \
				-v "concat(' laggproto ',laggproto)" \
				-m "laggport" \
					-v "concat(' laggport ',.)" \
				-b \
			-b \
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
					-i "string-length(standard) > 0" -v "concat(' mode ',standard)" -b \
					-i "count(wep/enable) > 0" \
						-v "concat(' wepmode on wepkey ',wep/key,' weptxkey 1')" \
					-b \
					-i "count(wep/enable) = 0" -o " wepmode off" -b \
					-i "count(wpa/enable) > 0" -o " WPA" -b \
					-o " up" \
				-b \
				-i "starts-with(if,'vlan')" \
					-m "//vinterfaces/vlan[if = '${_ifn}']" \
						-v "concat(' vlan ',tag,' vlandev ',vlandev)" \
					-b \
				-b \
				-i "starts-with(if,'lagg')" \
					-m "//vinterfaces/lagg[if = '${_ifn}']" \
						-v "concat(' laggproto ',laggproto)" \
						-m "laggport" \
							-v "concat(' laggport ',.)" \
						-b \
					-b \
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

	# Cloned interfaces:
	_cloned_interfaces=`/usr/local/bin/xml sel -t -m "//vinterfaces/*" \
		-v "concat(if,' ')" \
		${configxml_file} | /usr/local/bin/xml unesc`

	eval /usr/local/sbin/rconf attribute set "cloned_interfaces" "${_cloned_interfaces}"

	# Prepare interfaces used by lagg. Bring interfaces up only if a lagg interface
	# is used as LAN or OPT interface.
	/usr/local/bin/xml sel -t \
		-i "//interfaces/*/if[contains(.,'lagg')]" \
			-m "//vinterfaces/lagg/laggport" \
				-v . -n \
			-b \
		-b \
		${configxml_file} | /usr/local/bin/xml unesc | \
		while read _laggport; do
			[ -n "${_laggport}" ] && eval /usr/local/sbin/rconf attribute set "ifconfig_${_laggport}" "up"
		done

	#########################################################################
	# IPv6

	# Enable/Disable IPv6
	_value="NO"
	if configxml_isset "//interfaces/*[enable]/ipv6_enable"; then
		_value="YES"
	fi
	eval /usr/local/sbin/rconf attribute set "ipv6_enable" "${_value}"

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
			eval /usr/local/sbin/rconf attribute remove "ipv6_ifconfig_${_ifn}"
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
setvar
sethostname
setifconfig
setoptions

# Finally issue a line break
echo

# Force reloading of rc.conf file
_rc_conf_loaded=false
load_rc_config ${name}

return 0
