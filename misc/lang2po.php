#!/usr/local/bin/php -f
# Automatic language file translator
# Convert a CONSTANT file to a .po file
# Created for FreeNAS, use the same BSD Licence as FreeNAS
# olivier@freenas.org
# Need to be started from the svn/misc folder

<?php

function system_language_getall()
{
	/* Get all the languages */
 	$handle=opendir('../www/');
   	while ($file = readdir($handle)) {
        	if (preg_match("/^lang\-(.+)\.inc/", $file, $matches)) {
           		$langFound = $matches[1];
            		$files[] = $langFound;
        	}
	}
	closedir($handle);
	sort($files);
	return $files;

}

// Get the language file:
$language_liste = system_language_getall();

$reference_filename = '../www/lang-English.inc';

// Load the english file on a table

$no_error = 1;

echo "Starting the conversion\n";

echo "Checking Reference file: ";

if (file_exists($reference_filename)) {
		$reference_text = file_get_contents("$reference_filename");
		echo "English file found\n";
} else {

	$no_error = 0;
	echo "English file NOT found\n";
}

$fp = fopen('php://stdin', 'r');

echo "Here is the list of detected language file\n";
print_r($language_liste);
$language_number = count($language_liste);

do {

	echo "\nEnter the number of file that you want to generate the .po file\n: ";
	$number = chop(fgets($fp));
	if ($number === "") {
		exit(0);
	}
} while ($number > $language_number) ;

$language = $language_liste[$number] ;

echo "Language choosed: $language\n";
	
$tobeconverted_filename = "../www/lang-$language.inc";

// Load the language file to be converted

if (file_exists($tobeconverted_filename)){
	require($tobeconverted_filename);
	echo "To be converted file : $language found\n";
} else {

	echo "File: $tobeconverted_filename to be converted NOT found\n";
	exit ;

}

// Generate the destination .po file

$po_file_name = "../www/$language.po";

$fd = fopen("$po_file_name", "w");
if (!$fd) {
	printf("Error: cannot open $po_file_name;\n");
	exit;
}

		
// generate a table with all line from the string variable	
$reference_table = explode("\n", $reference_text);

$po_text = <<<EOD
#	$language Language file
#	Generated with the automatic lang2po.php generator
#	part of FreeNAS (http://www.freenas.org)
#	Copyright (C) 2005-2007 Olivier Cochard-Labbe <olivier@freenas.org>.
#	All rights reserved.
#	
#	Based on m0n0wall (http://m0n0.ch/wall)
#	Copyright (C) 2003-2006 Manuel Kasper <mk@neon1.net>.
#	All rights reserved.
#	
#	Redistribution and use in source and binary forms, with or without
#	modification, are permitted provided that the following conditions are met:
#	
#	1. Redistributions of source code must retain the above copyright notice,
#	   this list of conditions and the following disclaimer.
#	
#	2. Redistributions in binary form must reproduce the above copyright
#	   notice, this list of conditions and the following disclaimer in the
#	   documentation and/or other materials provided with the distribution.
#	
#	THIS SOFTWARE IS PROVIDED ``AS IS'' AND ANY EXPRESS OR IMPLIED WARRANTIES,
#	INCLUDING, BUT NOT LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY
#	AND FITNESS FOR A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE
#	AUTHOR BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY,
#	OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF
#	SUBSTITUTE GOODS OR SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS
#	INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN
#	CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE)
#	ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE
#	POSSIBILITY OF SUCH DAMAGE.

EOD;

		
// Parse this reference table
foreach ($reference_table as $line) {

	/* $line have this string:
	define('_HOURS', 'don'\t');
	*/		
			
	// Use only line that contain "define("		
	$to_be_checked  = 'define(';
	$is_good_line = strripos($line, $to_be_checked );

	if ($is_good_line !== false) {
		/* example of good line:
		define('_MSGVALIDPASSWORD','Password don\'t match.');
		define('_MSGVALIDPASSWORD', 'Password don\'t match.');
		define('_SRVRYNCC_MSGVALIDIP',_MSGVALIDIP);
		*/
			
		//Extract the CONSTANT : _MSGVALIDPASSWORD
		$line_tab = explode("'", $line);
		$const_name = $line_tab[1] ;
		//Now replace constant with the value
		$const = constant("$const_name");
		//Extract the text : Password don\'t match.

		$text = strstr($line, ',');

		/* if the text is a CONSTANT (begin with '_')
		skip the text (allready translated */
		if (strstr($text, '_') !== false) {
				continue;
		}

		
		// remove the first characters ","
		$text = substr($text, 1);
		// remove space at begining
		$text = ltrim($text);
		// remove the second characters "'"
		$text = substr($text, 1);
		// Remove the 3 last characters "');";
		$text = substr($text, 0,-3);
		
		// If it's empty, go to the next
		if (($text == "") || ($const == ""))
			continue;
				
$po_text .= <<<EOD
msgid "$text"
msgstr "$const"

EOD;

	}
}
	
fwrite($fd, $po_text);
fclose($fd);

echo "Done!\n";

?>
