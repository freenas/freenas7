<?php
/*
	diag_routes.php
	Modified for XHTML by Daisuke Aoyama (aoyama@peach.ne.jp)
	Copyright (C) 2010 Daisuke Aoyama <aoyama@peach.ne.jp>.
	All rights reserved.

	part of FreeNAS (http://www.freenas.org)
	Copyright (C) 2005-2010 Olivier Cochard <olivier@freenas.org>.
	All rights reserved.

	Copyright (C) 2006 Fernando Lamos
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
require("auth.inc");
require("guiconfig.inc");

$pgtitle = array(gettext("Diagnostics"), gettext("Routing tables"));
?>
<?php include("fbegin.inc");?>
<table width="100%" border="0" cellpadding="0" cellspacing="0">
	<tr>
		<td class="tabcont">
<?php
	$netstat = ($_POST['resolve'] == 'yes' ? 'netstat -rW' : 'netstat -nrW');
	list($dummy, $internet, $internet6) = explode("\n\n", shell_exec($netstat));

	foreach (array(&$internet, &$internet6) as $tabindex => $table) {
		$elements = ($tabindex == 0 ? 8 : 8);
		$name = ($tabindex == 0 ? 'IPv4' : 'IPv6');
?>
			<table width="100%" border="0" cellpadding="6" cellspacing="0">
				<?php html_titleline($name, $elements);?>
<?
		foreach (explode("\n", $table) as $row => $line) {
			if ($row == 0)
				continue;
			if ($line == '')
				continue;
			print("<tr>\n");
			$col = 0;
			foreach (explode(' ', $line) as $entry) {
				if ($entry == '')
					continue;
				if ($row == 1) { 
						$class = ($col == 0) ? "listhdrlr" : "listhdrr";
				} else {
					$class = ($col == 0) ? "listlr" : "listr";
				}
				print("<td class=\"$class\">$entry</td>\n");
				$col++;
			}
			// The 'Expire' field might be blank
			if ($col == ($elements - 1))
				print('<td class="listr">&nbsp;</td>' . "\n");
			print("</tr>\n");
		}
		print("</table><br />\n");
	}
?>
		</td>
	</tr>
</table>
<?php include("fend.inc");?>
