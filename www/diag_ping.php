#!/usr/local/bin/php
<?php
/*
	diag_ping.php
	part of FreeNAS (http://www.freenas.org)
	Copyright (C) 2005-2008 Olivier Cochard <olivier@freenas.org>.
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
require("guiconfig.inc");

$pgtitle = array(gettext("Diagnostics"), gettext("Ping"));

if ($_POST) {
	unset($input_errors);
	unset($do_ping);

	// Input validation.
	$reqdfields = explode(" ", "host count");
	$reqdfieldsn = array(gettext("Host"), gettext("Count"));

	do_input_validation($_POST, $reqdfields, $reqdfieldsn, &$input_errors);

	if (!$input_errors) {
		$do_ping = true;
		$host = $_POST['host'];
		$interface = $_POST['interface'];
		$count = $_POST['count'];
	}
}

if (!isset($do_ping)) {
	$do_ping = false;
	$host = "";
	$count = 3;
}

function get_interface_addr($ifdescr) {
	global $config;

	// Find out interface name.
	$if = $config['interfaces'][$ifdescr]['if'];

	return get_ipaddr($if);
}
?>
<?php include("fbegin.inc");?>
<table width="100%" border="0" cellpadding="0" cellspacing="0">
	<tr>
		<td class="tabnavtbl">
			<ul id="tabnav">
				<li class="tabact"><a href="diag_ping.php" title="<?=gettext("Reload page");?>"><span><?=gettext("Ping");?></span></a></li>
				<li class="tabinact"><a href="diag_traceroute.php"><span><?=gettext("Traceroute");?></span></a></li>
			</ul>
		</td>
	</tr>
	<tr>
		<td class="tabcont">
			<form action="diag_ping.php" method="post" name="iform" id="iform">
				<?php if ($input_errors) print_input_errors($input_errors);?>
				<table width="100%" border="0" cellpadding="6" cellspacing="0">
					<?php html_inputbox("host", gettext("Host"), $host, gettext("Destination host name or IP number."), true, 20);?>
					<?php html_interfacecombobox("interface", gettext("Interface"), $interface, gettext("Use the following IP address as the source address in outgoing packets."), true);?>
					<?php $a_count = array(); for ($i = 1; $i <= 10; $i++) { $a_count[$i] = $i; }?>
					<?php html_combobox("count", gettext("Count"), $count, $a_count, gettext("Stop after sending (and receiving) N packets."), true);?>
				</table>
				<div id="submit">
					<input name="Submit" type="submit" class="formbtn" value="<?=gettext("Ping");?>">
				</div>
				<?php if ($do_ping) {
				echo("<strong>" . gettext("Command output:") . "</strong>");
				echo('<pre>');
				ob_end_flush();
				$ifaddr = get_interface_addr($interface);
				if ($ifaddr) {
					system("/sbin/ping -S {$ifaddr} -c {$count} " . escapeshellarg($host));
				} else {
					system("/sbin/ping -c {$count} " . escapeshellarg($host));
				}
				echo('</pre>');
				}
				?>
			</form>
		</td>
	</tr>
</table>
<?php include("fend.inc");?>
