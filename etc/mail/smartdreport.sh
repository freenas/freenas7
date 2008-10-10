#!/bin/sh
# Copyright (c) 2008 Volker Theile (votdev@gmx.de)
# All rights reserved.

. /etc/rc.subr
. /etc/configxml.subr

# Defaults
msmtp_config=${msmtp_config:-"/var/etc/msmtp.conf"}
smartdreport_msgfile=${smartdreport_msgfile:-"/tmp/smartdreport"}

# Create message
/usr/local/bin/xml sel -t \
	-v "concat('From: ',//system/email/from)" -n \
	-o "To: ${SMARTD_ADDRESS}" -n \
	-o "Subject: ${SMARTD_SUBJECT}" -n \
	-o "." \
	${configxml_file} | /usr/local/bin/xml unesc > ${smartdreport_msgfile}

# Save the email message (STDIN) to a file:
cat >> ${smartdreport_msgfile}

# Now email the message to the user at address ADD:
/usr/local/bin/msmtp --file=${msmtp_config} ${SMARTD_ADDRESS} < ${smartdreport_msgfile} 1>/dev/null 2>&1

# Cleanup
/bin/rm ${smartdreport_msgfile} 1>/dev/null 2>&1
