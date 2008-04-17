<?php
/*
	diag_logs.inc
	part of FreeNAS (http://www.freenas.org)
	Copyright (C) 2005-2008 Olivier Cochard-Labbé <olivier@freenas.org>.
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
function logs_get_contents($logfile, $entries, $mode) {
	global $config;

	$content = array();

	$param = (isset($config['syslogd']['reverse']) ? "-r " : "") . "-n";

	switch ($mode) {
		case "clog":
			exec("/usr/sbin/clog {$logfile} | /usr/bin/tail {$param} {$entries}", $content);
			break;
		case "cat":
			exec("/bin/cat {$logfile} | /usr/bin/tail {$param} {$entries}", $content);
	}

	return $content;
}

function logs_dump($logfile, $entries, $withorig = true) {
	foreach (logs_get_contents($logfile, $entries, "clog") as $logent) {
		echo "<tr valign=\"top\">\n";
		if ($withorig) {
			$logent = preg_split("/\s+/", $logent, 6);
			echo "<td class=\"listlr\" nowrap>" . htmlspecialchars(join(" ", array_slice($logent, 0, 3))) . "</td>\n";
			echo "<td class=\"listr\">" . htmlspecialchars($logent[4] . " " . $logent[5]) . "</td>\n";
		} else {
			echo "<td class=\"listlr\" colspan=\"2\">" . htmlspecialchars($logent) . "</td>\n";
		}
		echo "</tr>\n";
	}
}

function logs_dump_ex($logfile, $entries, $columns, $withorig = true) {
	foreach (logs_get_contents($logfile, $entries, "cat") as $logent) {
		if (empty($logent)) // Skip empty lines.
			continue;
		echo "<tr valign=\"top\">\n";
		if ($withorig) {
			$logent = preg_split("/\s+/", $logent, $columns);
			echo "<td class=\"listlr\" nowrap>" . htmlspecialchars(join(" ", array_slice($logent, 0, 2))) . "</td>\n";
			echo "<td class=\"listr\">" . htmlspecialchars($logent[2] . " " . $logent[3]) . "</td>\n";
		} else {
			echo "<td class=\"listlr\" colspan=\"2\">" . htmlspecialchars($logent) . "</td>\n";
		}
		echo "</tr>\n";
	}
}
