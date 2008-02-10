<?php
/*
	tui.inc
	Copyright (c) 2006-2008 Volker Theile (votdev@gmx.de)
	All rights reserved.

	part of FreeNAS (http://www.freenas.org)
	Copyright (C) 2005-2008 Olivier Cochard-Labbé <olivier@freenas.org>.
	All rights reserved.

	Redistribution and use in source and binary forms, with or without
	modification, are permitted provided that the following conditions are met:

	1. Redistributions of source code must retain the above copyright notice,
	   this list of conditions and the following disclaimer.

	2. Redistributions in binary form must reproduce the above copyright
	   notice, this list of conditions and the following disclaimer in the
	   documentation and/or other materials provided with the distribution.

	THIS SOFTWARE IS PROVIDED ``AS IS'' AND ANY EXPRESS OR IMPLIED WARRANTIES,
	INCLUDING, BUT NOT LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY
	AND FITNESS FOR A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE
	AUTHOR BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY,
	OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF
	SUBSTITUTE GOODS OR SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS
	INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN
	CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE)
	ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE
	POSSIBILITY OF SUCH DAMAGE.
*/
require_once("util.inc");

// Display a yes|no dialog.
// $text - The dialog text
// $width - Width of the dialog
// $height - Height of the dialog
// Returns 0 if yes, 1 if no or 255 if aborted via ESC.
function tui_display_yesno($text, $width, $height) {
	passthru("/usr/local/bin/cdialog --clear --yesno \"{$text}\" {$height} {$width}", $retval);
	return $retval;
}

// Display a message box.
// $text - The dialog text
// $width - Width of the dialog
// $height - Height of the dialog
// Returns 0 if ok has been pressed or 255 if aborted via ESC.
function tui_display_message($text, $width, $height) {
	passthru("/usr/local/bin/cdialog --clear --msgbox \"{$text}\" {$height} {$width}", $retval);
	return $retval;
}

// Display a input box.
// $text - The dialog text
// $width - Width of the dialog
// $height - Height of the dialog
// $value [in|out]- Initial value. Also contains result.
// Returns 0 if ok has been pressed or 255 if aborted via ESC.
function tui_display_input($text, $width, $height, &$value) {
	$tmpfile = "/tmp/tui" . rand();
	passthru("/usr/local/bin/cdialog --clear --inputbox \"{$text}\" {$height} {$width} \"{$value}\" &1</dev/console 2>{$tmpfile}", $retval);
	$value = @file_get_contents($tmpfile);
	@unlink($tmpfile);
	return $retval;
}

// Display a menu.
// $title - The menu title
// $width - Width of the menu
// $height - Height of the menu
// $amenuitem - The menu item array => Array
// (
// 	[tag]  => "x"
// 	[item] => "xy zxy yxz"
// )
function tui_display_menu($title, $width, $height, $amenuitem) {
	$menuheight = sizeof($amenuitem);
	$cmd = "/usr/local/bin/cdialog --menu {$title} {$height} {$width} {$menuheight}";
	foreach($amenuitem as $menuitemv) {
		$cmd .= " \"{$menuitemv['tag']}\" \"{$menuitemv['item']}\"";
	}
	passthru($cmd, $retval);
	return $retval;
}
?>
