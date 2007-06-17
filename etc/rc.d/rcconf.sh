#!/bin/sh
# Copyright © 2007 Volker Theile (votdev@gmx.de)
# All rights reserved.

# PROVIDE: rcconf

. /etc/rc.subr

name="rcconf"

echo "Updating rc.conf."
# Update /etc/rc.conf file. Use settings from config.xml.
eval /etc/rc.d.php/${name}
# Force reloading of rc.conf file.
_rc_conf_loaded=false
load_rc_config ${name}
