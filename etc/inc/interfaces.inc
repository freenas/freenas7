<?php
/*
	interfaces.inc
	Copyright (c) 2008 Volker Theile (votdev@gmx.de)
	All rights reserved.

	part of FreeNAS (http://www.freenas.org)
	Copyright (C) 2005-2008 Olivier Cochard-Labbe <olivier@freenas.org>.
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
require_once("config.inc");

function interfaces_wireless_get_channellist($if) {
	$a_chanlist = array();

	exec("/sbin/ifconfig {$if} list chan", $rawdata);
	foreach ($rawdata as $line) {
		$line = trim($line);

		foreach (explode("Channel", $line) as $channelv) {
			if (preg_match("/(\d+)\s+:\s+(\d+)\s+Mhz\s+(.+)/", $channelv, $matches)) {
				$info = array();
				$info['channel'] = $matches[1];
				$info['freq'] = $matches[2];
				$info['mode'] = trim($matches[3]);

				$a_chanlist[$info['channel']] = $info;
			}
		}
	}

	ksort($a_chanlist, SORT_NUMERIC);

	return $a_chanlist;
}

function interfaces_wireless_get_standards($if) {
	$a_standards = array();

	exec("/sbin/ifconfig -m {$if}", $rawdata);
	foreach ($rawdata as $line) {
		$line = trim($line);

		if (preg_match("/media \S+ mode (11\S+)/", $line, $matches)) {
			$a_standards[] = $matches[1];
		}
	}

	return array_unique($a_standards);
}
?>
