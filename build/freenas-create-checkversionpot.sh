#!/bin/sh
# Extract gettext strings from checkfirmware.php to make pot.
# Created: 11-06-2011 by Michael Zoon (michael.zoon@freenas.org) 

# Global variables
FREENAS_ROOTDIR="/usr/local/freenas"
FREENAS_SVNDIR="$FREENAS_ROOTDIR/svn"
FREENAS_PRODUCTNAME=$(cat ${FREENAS_SVNDIR}/etc/prd.name)

OUTPUT="$(echo ${FREENAS_PRODUCTNAME} | tr '[:upper:]' '[:lower:]')checkversion.pot"
OUTPUTDIR="${FREENAS_SVNDIR}/build/checkversion"
PARAMETERS="--output-dir=${OUTPUTDIR} --output=${OUTPUT} \
--force-po --no-location --no-wrap --sort-output --omit-header"

cd ${FREENAS_SVNDIR}/build/checkversion
xgettext ${PARAMETERS} *.*

cd ${FREENAS_SVNDIR}/build/checkversion
xgettext ${PARAMETERS} --join-existing *.*

DATE="$(date "+%Y-%m-%d %H:%M")+0000"
echo "msgid \"\"
msgstr \"\"
\"Project-Id-Version: ${FREENAS_PRODUCTNAME}-checkversion\\n\"
\"POT-Creation-Date: ${DATE}\\n\"
\"PO-Revision-Date: \\n\"
\"Last-Translator: \\n\"
\"Language-Team: \\n\"
\"MIME-Version: 1.0\\n\"
\"Content-Type: text/plain; charset=iso-8859-1\\n\"
\"Content-Transfer-Encoding: 8bit\\n\"
" >${OUTPUTDIR}/${OUTPUT}.tmp

cat ${OUTPUTDIR}/${OUTPUT} >>${OUTPUTDIR}/${OUTPUT}.tmp
mv -f ${OUTPUTDIR}/${OUTPUT}.tmp ${OUTPUTDIR}/${OUTPUT}

echo "==> Translation file created: ${OUTPUTDIR}/${OUTPUT}"
