#!/bin/sh
# Copyright (c) 2008 Volker Theile (votdev@gmx.de)
# All rights reserved.

. /etc/rc.subr
. /etc/configxml.subr

# Defaults
msmtp_config=${msmtp_config:-"/var/etc/msmtp.conf"}
msmtp_msgfile=${msmtp_msgfile:-"/tmp/message"}

# Create message
/usr/local/bin/xml sel -t \
	-v "concat('From: ',//system/email/from)" -n \
	-o "To: ${SMARTD_ADDRESS}" -n \
	-o "Subject: ${SMARTD_SUBJECT}" -n \
	-o "." -n \
	${configxml_file} | /usr/local/bin/xml unesc > ${msmtp_msgfile}

# Save the email message (STDIN) to a file:
cat >> ${msmtp_msgfile}

# Now email the message to the user at address ADD:
/usr/local/bin/msmtp --file=${msmtp_config} ${SMARTD_ADDRESS} < ${msmtp_msgfile} 1>/dev/null 2>&1

# Cleanup
/bin/rm ${msmtp_msgfile} 1>/dev/null 2>&1
