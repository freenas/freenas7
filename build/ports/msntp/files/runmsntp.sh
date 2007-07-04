#!/bin/sh
# write our PID to file
echo $$ > $1

_pidfile=$2
_timeupdateinterval=$3
shift 3
_timeservers=$@

# execute msntp in endless loop; restart if it
# exits (wait 1 second to avoid restarting too fast in case
# the network is not yet setup)
while true; do
	/usr/local/bin/msntp -r -P no -l ${_pidfile} -x ${_timeupdateinterval} ${_timeservers}
	sleep 1
done
