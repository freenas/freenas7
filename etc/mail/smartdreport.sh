#!/bin/sh
# Copyright (c) 2008 Volker Theile (votdev@gmx.de)
# All rights reserved.

. /etc/rc.subr
. /etc/configxml.subr

# Defaults
msmtp_config=${msmtp_config:-"/var/etc/msmtp.conf"}
msmtp_msgfile=${msmtp_msgfile:-"/tmp/message"}

# Get date in RFC 2882 format.
_rfcdate=`date "+%a, %d %b %Y %H:%M:%S %z"`

# Create message
echo ${SMARTD_ADDRESS} | awk '{for ( i = NF ; i > 0 ; --i ) printf("To: %s\n",$i)}' > ${msmtp_msgfile}

/usr/local/bin/xml sel -t \
	-v "concat('From: ',//system/email/from)" -n \
	-o "Subject: ${SMARTD_SUBJECT}" -n \
	-o "Date: ${_rfcdate}" -n \
	-o "." -n \
	${configxml_file} | /usr/local/bin/xml unesc >> ${msmtp_msgfile}

# Save the email message (STDIN) to a file:
cat >> ${msmtp_msgfile}

# Append the output of smartctl -a to the message:
/usr/local/sbin/smartctl -a -d ${SMARTD_DEVICETYPE} ${SMARTD_DEVICE} 1>> ${msmtp_msgfile}

# Now email the message to the user at address ADD:
/usr/local/bin/msmtp --file=${msmtp_config} ${SMARTD_ADDRESS} < ${msmtp_msgfile} 1>/dev/null 2>&1

# Cleanup
/bin/rm ${msmtp_msgfile} 1>/dev/null 2>&1
