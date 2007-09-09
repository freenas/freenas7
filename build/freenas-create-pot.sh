#!/bin/sh
# Extract gettext strings from source.
# Created: 09.09.2007 by Volker Theile (votdev@gmx.de)

# Global variables
FREENAS_ROOTDIR="/usr/local/freenas"
FREENAS_SVNDIR="$FREENAS_ROOTDIR/svn"
FREENAS_PRODUCTNAME=$(cat $FREENAS_SVNDIR/etc/prd.name | tr '[:upper:]' '[:lower:]')

PARAMETERS="--output-dir=${FREENAS_SVNDIR}/locale --output=${FREENAS_PRODUCTNAME}.pot \
--force-po --indent --no-location --no-wrap --sort-output --omit-header"

cd ${FREENAS_SVNDIR}/www
xgettext ${PARAMETERS} *.*

cd ${FREENAS_SVNDIR}/www
xgettext ${PARAMETERS} --join-existing *.*
