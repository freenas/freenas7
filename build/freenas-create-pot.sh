#!/bin/sh
# Extract gettext strings from source.
# Created: 09.09.2007 by Volker Theile (votdev@gmx.de)

# Global variables
FREENAS_ROOTDIR="/usr/local/freenas"
FREENAS_SVNDIR="$FREENAS_ROOTDIR/svn"
FREENAS_PRODUCTNAME=$(cat ${FREENAS_SVNDIR}/etc/prd.name)

OUTPUT="$(echo ${FREENAS_PRODUCTNAME} | tr '[:upper:]' '[:lower:]').pot"
OUTPUTDIR="${FREENAS_SVNDIR}/locale"
PARAMETERS="--output-dir=${OUTPUTDIR} --output=${OUTPUT} \
--force-po --no-location --no-wrap --sort-output --omit-header"

cd ${FREENAS_SVNDIR}/www
xgettext ${PARAMETERS} *.*

cd ${FREENAS_SVNDIR}/www
xgettext ${PARAMETERS} --join-existing *.*

echo "msgid \"\"
msgstr \"\"
\"Project-Id-Version: ${FREENAS_PRODUCTNAME}\\n\"
\"POT-Creation-Date: \\n\"
\"PO-Revision-Date: $(date "+%Y-%m-%d %H:%M")+0000\\n\"
\"Last-Translator: \\n\"
\"Language-Team: \\n\"
\"MIME-Version: 1.0\\n\"
\"Content-Type: text/plain; charset=iso-8859-1\\n\"
\"Content-Transfer-Encoding: 8bit\\n\"
" >${OUTPUTDIR}/${OUTPUT}.tmp

cat ${OUTPUTDIR}/${OUTPUT} >>${OUTPUTDIR}/${OUTPUT}.tmp
mv -f ${OUTPUTDIR}/${OUTPUT}.tmp ${OUTPUTDIR}/${OUTPUT}
