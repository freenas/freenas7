<?php
// ensure this file is being included by a parent file
if( !defined( '_JEXEC' ) && !defined( '_VALID_MOS' ) ) die( 'Restricted access' );

//-------------Configuration Variables---------------------------------------------
// Send gzipped content if accepted by the browser?
$GLOBALS['use_gzip'] = 1;

$GLOBALS["zip"] = false;	//function_exists("gzcompress");
$GLOBALS["tar"] = false;
$GLOBALS["tgz"] = false;

$GLOBALS['allow_webdav'] = 0;
// The following database settings are only necessary if you want to use WebDAV
// If you actually decide to enable the WebDAV interface, you must first of all create a new database
// by using the instructions from the file "webdav_table.sql.php", which can be found in the same directory as this file
$GLOBALS['DB_HOST'] = 'localhost';
$GLOBALS['DB_NAME'] = 'webdav';
$GLOBALS['DB_USER'] = 'root';
$GLOBALS['DB_PASSWORD'] = 'root';
$GLOBALS['DB_TYPE'] = 'mysql'; // Name of the Database Server Type (see http://en.php.net/manual/en/pdo.drivers.php for more)

//------------------------------------------------------------------------------
// Global User Variables (used when $require_login==false)

// show hidden files in eXtplorer: (hide files starting with '.', as in Linux/UNIX)
$GLOBALS["show_hidden"] = true;

// filenames not allowed to access: (uses PCRE regex syntax)
// Example: Hide files starting with ".ht" (like .htaccess):  ^\.ht
$GLOBALS["no_access"] = ''; // "^\.ht";

// user permissions bitfield: (1=modify, 2=password, 4=admin, add the numbers)
$GLOBALS["permissions"] = 7;
//------------------------------------------------------------------------------
/* NOTE:
	Users can be defined by using the Admin-section,
	or in the file "config/.htusers.php".
	For more information about PCRE Regex Syntax,
	go to http://www.php.net/pcre.pattern.syntax
*/
//------------------------------------------------------------------------------
?>
