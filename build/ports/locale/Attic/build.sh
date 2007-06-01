#!/usr/bin/env bash

# Description displayed in dialog menu (max. 60 characters)
MENUDESC="locale - Language packages for FreeNAS"
# Is dialog menu selected [ ON | OFF ]
MENUSTATUS="ON"

build_locale() {
	cd $WORKINGDIR

	# Translate *po files to *.mo.
	for i in $(ls $SVNDIR/locale/*.po); do
		filename=$(basename $i)
		language=${filename%*.po}
		filename=$(echo $PRODUCTNAME | tr '[A-Z]' '[a-z]') # make filename lower case.
		mkdir -v -p $WORKINGDIR/locale/$language/LC_MESSAGES
		msgfmt -v --output-file="$WORKINGDIR/locale/$language/LC_MESSAGES/$filename.mo" $i
		[ 0 != $? ] && return 1 # successful?
	done

  return 0
}

install_locale() {
	cp -v -r $WORKINGDIR/locale/* $FREENAS/usr/local/share/locale

  return 0
}
