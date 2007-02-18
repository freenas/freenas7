#!/usr/bin/env bash

# Description displayed in dialog menu (max. 60 characters)
MENUDESC="php5 - PHP Scripting Language"
# Is dialog menu selected [ ON | OFF ]
MENUSTATUS="ON"

URL_PHP="http://www.php.net/distributions/php-5.2.1.tar.gz"

build_php() {
	cd $WORKINGDIR

  php_tarball=$(urlbasename $URL_PHP)

  if [ ! -f "$php_tarball" ]; then
		fetch $URL_PHP
		if [ 1 == $? ]; then
      echo "==> Failed to fetch $php_tarball."
      return 1
    fi
	fi

	tar -zxf $php_tarball
	cd $(basename $php_tarball .tar.gz)

	./configure --enable-fastcgi --enable-discard-path --enable-force-cgi-redirect --without-mysql --without-pear --with-openssl --without-sqlite --with-pcre-regex --with-gettext
	[ 0 != $? ] && return 1 # successful?

	make

	return $?
}

install_php() {
	cd $WORKINGDIR

 	php_tarball=$(urlbasename $URL_PHP)
	cd $(basename $php_tarball .tar.gz)

	install -vs sapi/cgi/php $FREENAS/usr/local/bin

	echo 'magic_quotes_gpc = off
magic_quotes_runtime = off
max_execution_time = 0
max_input_time = 180
register_argc_argv = off
file_uploads = on
upload_tmp_dir = /ftmp
upload_max_filesize = 256M
post_max_size = 256M
html_errors = off
include_path = ".:/etc/inc:/usr/local/www"' > $FREENAS/usr/local/lib/php.ini

	return 0
}
