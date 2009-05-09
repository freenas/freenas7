<?php
/*
	diag_log.inc
	part of FreeNAS (http://www.freenas.org)
	Copyright (C) 2005-2009 Olivier Cochard-Labbe <olivier@freenas.org>.
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
$loginfo = array(
	array(
		"desc" => gettext("System"),
		"logfile" => "/var/log/system.log",
		"filename" => "system.log",
		"type" => "clog",
		"size" => "262144",
		"pattern" => "/^(\S+\s+\d+\s+\S+)\s+(\S+)\s+(.*)$/",
		"columns" => array(
			array("title" => gettext("Date & Time"), "class" => "listlr", "param" => "nowrap", "pmid" => 1),
			array("title" => gettext("User"), "class" => "listr", "param" => "nowrap", "pmid" => 2),
			array("title" => gettext("Event"), "class" => "listr", "param" => "", "pmid" => 3)
		)),
	array(
		"desc" => gettext("SSH"),
		"logfile" => "/var/log/sshd.log",
		"filename" => "sshd.log",
		"type" => "clog",
		"size" => "32768",
		"pattern" => "/^(\S+\s+\d+\s+\S+)\s+(\S+)\s+(.*)$/",
		"columns" => array(
			array("title" => gettext("Date & Time"), "class" => "listlr", "param" => "nowrap", "pmid" => 1),
			array("title" => gettext("User"), "class" => "listr", "param" => "nowrap", "pmid" => 2),
			array("title" => gettext("Event"), "class" => "listr", "param" => "", "pmid" => 3)
		)),
	array(
		"desc" => gettext("Daemon"),
		"logfile" => "/var/log/daemon.log",
		"filename" => "daemon.log",
		"type" => "clog",
		"size" => "32768",
		"pattern" => "/^(\S+\s+\d+\s+\S+)\s+(\S+)\s+(.*)$/",
		"columns" => array(
			array("title" => gettext("Date & Time"), "class" => "listlr", "param" => "nowrap", "pmid" => 1),
			array("title" => gettext("User"), "class" => "listr", "param" => "nowrap", "pmid" => 2),
			array("title" => gettext("Event"), "class" => "listr", "param" => "", "pmid" => 3)
		)),
	array(
		"desc" => gettext("FTP"),
		"logfile" => "/var/log/ftp.log",
		"filename" => "ftp.log",
		"type" => "clog",
		"size" => "32768",
		"pattern" => "/^(\S+\s+\d+\s+\S+)\s+(\S+)\s+(.*)$/",
		"columns" => array(
			array("title" => gettext("Date & Time"), "class" => "listlr", "param" => "nowrap", "pmid" => 1),
			array("title" => gettext("User"), "class" => "listr", "param" => "nowrap", "pmid" => 2),
			array("title" => gettext("Event"), "class" => "listr", "param" => "", "pmid" => 3)
		)),
	array(
		"desc" => gettext("FTP - File transfer"),
		"logfile" => "/var/log/xferlog",
		"filename" => "xferlog",
		"type" => "plain",
		"pattern" => "/^(\S+\s+\S+\s+\d+\s+\S+\s+\d+)\s+(.*)$/",
		"columns" => array(
			array("title" => gettext("Date & Time"), "class" => "listlr", "param" => "nowrap", "pmid" => 1),
			array("title" => gettext("Event"), "class" => "listr", "param" => "", "pmid" => 2)
		)),
	array(
		"desc" => gettext("S.M.A.R.T."),
		"logfile" => "/var/log/smartd.log",
		"filename" => "smartd.log",
		"type" => "clog",
		"size" => "32768",
		"pattern" => "/^(\S+\s+\d+\s+\S+)\s+(\S+)\s+(.*)$/",
		"columns" => array(
			array("title" => gettext("Date & Time"), "class" => "listlr", "param" => "nowrap", "pmid" => 1),
			array("title" => gettext("User"), "class" => "listr", "param" => "nowrap", "pmid" => 2),
			array("title" => gettext("Event"), "class" => "listr", "param" => "", "pmid" => 3)
		)),
	array(
		"desc" => gettext("RSYNC - Server"),
		"logfile" => "/var/log/rsyncd.log",
		"filename" => "rsyncd.log",
		"type" => "clog",
		"size" => "32768",
		"pattern" => "/^(\S+\s+\d+\s+\S+)\s+(\S+)\s+(.*)$/",
		"columns" => array(
			array("title" => gettext("Date & Time"), "class" => "listlr", "param" => "nowrap", "pmid" => 1),
			array("title" => gettext("User"), "class" => "listr", "param" => "nowrap", "pmid" => 2),
			array("title" => gettext("Event"), "class" => "listr", "param" => "", "pmid" => 3)
		)),
	array(
		"desc" => gettext("RSYNC - Client"),
		"logfile" => "/var/log/rsync_client.log",
		"filename" => "rsync_client.log",
		"type" => "plain",
		"pattern" => "/^(.*)$/",
		"columns" => array(
			array("title" => gettext("Event"), "class" => "listlr", "param" => "", "pmid" => 1)
		)),
	array(
		"desc" => gettext("RSYNC - Local"),
		"logfile" => "/var/log/rsync_local.log",
		"filename" => "rsync_local.log",
		"type" => "plain",
		"pattern" => "/^(.*)$/",
		"columns" => array(
			array("title" => gettext("Event"), "class" => "listlr", "param" => "", "pmid" => 1)
		)),
	array(
		"desc" => gettext("UPnP"),
		"logfile" => "/var/log/fuppes.log",
		"filename" => "upnp.log",
		"type" => "plain",
		"pattern" => "/^(.*)$/",
		"columns" => array(
			array("title" => gettext("Event"), "class" => "listlr", "param" => "", "pmid" => 1)
		)),
	array(
		"desc" => gettext("DAAP"),
		"logfile" => "/var/log/mt-daapd.log",
		"filename" => "daap.log",
		"type" => "plain",
		"pattern" => "/^(\S+\s+\S+)\s+\S+:\s+(.*)$/",
		"columns" => array(
			array("title" => gettext("Date & Time"), "class" => "listlr", "param" => "nowrap", "pmid" => 1),
			array("title" => gettext("Event"), "class" => "listr", "param" => "", "pmid" => 2)
		))
);

function log_get_contents($logfile, $type) {
	global $config;

	$content = array();

	$param = (isset($config['syslogd']['reverse']) ? "-r " : "");
	$param .= "-n " . $config['syslogd']['nentries'];

	switch ($type) {
		case "clog":
			exec("/usr/sbin/clog {$logfile} | /usr/bin/tail {$param}", $content);
			break;

		case "plain":
			exec("/bin/cat {$logfile} | /usr/bin/tail {$param}", $content);
	}

	return $content;
}

function log_display($loginfo) {
	if (!is_array($loginfo))
		return;

	// Create table header
	echo "<tr>";
	foreach ($loginfo['columns'] as $columnk => $columnv) {
		echo "<td {$columnv['param']} class='listhdrr'>{$columnv['title']}</td>\n";
	}
	echo "</tr>";

	// Get log file content
	$content = log_get_contents($loginfo['logfile'], $loginfo['type']);
	if (empty($content))
		return;

	// Create table data
	foreach ($content as $contentv) {
		// Skip invalid pattern matches
		$result = preg_match($loginfo['pattern'], $contentv, $matches);
		if ((FALSE === $result) || (0 == $result))
			continue;

		// Skip empty lines
		if (count($loginfo['columns']) == 1 && empty($matches[1]))
			continue;

		echo "<tr valign=\"top\">\n";
		foreach ($loginfo['columns'] as $columnk => $columnv) {
			echo "<td class='{$columnv['class']}'>" . htmlspecialchars($matches[$columnv['pmid']]) . "</td>\n";
		}
		echo "</tr>\n";
	}
}

function log_clear($loginfo) {
	if (!is_array($loginfo))
		return;

	switch ($loginfo['type']) {
		case "clog":
			exec("/usr/sbin/clog -i -s {$loginfo['size']} {$loginfo['logfile']}");
			break;

		case "plain":
			exec("/bin/cat /dev/null > {$loginfo['logfile']}");
	}
}

function log_download($loginfo) {
	if (!is_array($loginfo))
		return;

	$fs = get_filesize($loginfo['logfile']);

	header("Content-Type: application/octet-stream");
	header("Content-Disposition: attachment; filename={$loginfo['filename']}");
	header("Content-Length: {$fs}");
	header("Pragma: hack");

	switch ($loginfo['type']) {
		case "clog":
			exec("/usr/sbin/clog {$loginfo['logfile']}", $content);
			echo implode("\n", $content);
			break;

		case "plain":
			readfile($loginfo['logfile']);
	}
}
?>
