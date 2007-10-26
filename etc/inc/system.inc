<?php
/*
	system.inc
	part of FreeNAS (http://www.freenas.org)
	Copyright (C) 2005-2007 Olivier Cochard-Labbe <olivier@freenas.org>.
	All rights reserved.

	Based on m0n0wall (http://m0n0.ch/wall)
	Copyright (C) 2003-2006 Manuel Kasper <mk@neon1.net>.
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
require_once("functions.inc");
require_once("util.inc");
require_once("rc.inc");

function system_stop_common() {
	/* Umount all disk s*/
	disks_umount_all();

	/* Detach all encrypted disks */
	disks_geli_detach_all();

	/* Stop all raid disks */
	disks_raid_stop();
}

function system_reboot() {
	// Initiate halt. Everything will be done automatically
	// in /etc/rc.shutdown by executing rc-init scripts in
	// reverse order (the 'KEYWORD: shutdown' must be defined).
	mwexec("/sbin/shutdown -r now");
}

function system_halt() {
	// Initiate halt. Everything will be done automatically
	// in /etc/rc.shutdown by executing rc-init scripts in
	// reverse order (the 'KEYWORD: shutdown' must be defined).
	mwexec("/sbin/shutdown -p now");
}

/* Init language environment */
function system_language_load()
{
	global $config, $g_languages;

	/* Get the language configured. */
	$language = $config['system']['language'];
	$domain = strtolower(get_product_name());
	$codeset = $g_languages[$language]['codeset'];

	putenv("LANG=$language");
	setlocale(LC_MESSAGES, $language);
	bindtextdomain($domain, "/usr/local/share/locale");
	bind_textdomain_codeset($domain, $codeset);
	textdomain($domain);
}

/* Get the codeset of the current configured language. */
/* Return: String containing codeset of current laguage. */
function system_get_language_codeset() {
	global $config, $g_languages;

	$language = $config['system']['language'];
	$codeset = $g_languages[$language]['codeset'];

	if (empty($codeset))
		$codeset = "ISO-8859-1"; // Set default codeset.

	return $codeset;
}
?>
