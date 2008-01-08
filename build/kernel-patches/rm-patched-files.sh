#!/bin/sh
# Extract gettext strings from source.
# Created: 09.09.2007 by Volker Theile (votdev@gmx.de)

echo "Removing patched files..."

for file in $(find /usr/src -name "*.orig"); do
	rm -rv ${file}
done
