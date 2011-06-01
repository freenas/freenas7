#!/usr/local/bin/php
<?php
/*
	status_graph.php
	Modified for XHTML by Daisuke Aoyama (aoyama@peach.ne.jp)
	Copyright (C) 2010-2011 Daisuke Aoyama <aoyama@peach.ne.jp>.
	All rights reserved.

	Modified by Michael Zoon <michael.zoon@freenas.org>
	Copyright (C) 2010-2011 Michael Zoon <michael.zoon@freenas.org>.
	All rights reserved.

	Part of FreeNAS (http://www.freenas.org)
	Copyright (C) 2005-2011 Olivier Cochard <olivier@freenas.org>.
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
require("auth.inc");
require("guiconfig.inc");

$pgtitle = array(gettext("Status"), gettext("Graph"),gettext("Traffic graph"));

$curif = "lan";
if ($_GET['if'])
	$curif = $_GET['if'];
$ifnum = get_ifname($config['interfaces'][$curif]['if']);
?>
<?php include("fbegin.inc");?>
<table width="100%" border="0" cellpadding="0" cellspacing="0">
  <tr>
		<td class="tabnavtbl">
  		<ul id="tabnav">
				<li class="tabact"><a href="status_graph.php" title="<?=gettext("Reload page");?>"><span><?=gettext("Traffic graph");?></span></a></li>
				<li class="tabinact"><a href="status_graph_cpu.php"><span><?=gettext("CPU load");?></span></a></li>
  		</ul>
  	</td>
	</tr>
  <tr>
    <td class="tabcont">
<?php
$ifdescrs = array('lan' => 'LAN');
for ($j = 1; isset($config['interfaces']['opt' . $j]); $j++) {
	$ifdescrs['opt' . $j] = $config['interfaces']['opt' . $j]['descr'];
}
?>
<form name="form1" action="status_graph.php" method="get" style="padding-bottom: 10px; margin-bottom: 14px; border-bottom: 1px solid #999999">
Interface: 
<select name="if" class="formfld" onchange="document.form1.submit()">
  <option value="lan" selected="selected">LAN</option>
</select>
<?php include("formend.inc");?>
</form><?=gettext("Graph shows last 120 seconds");?>
<div align="center">
<object id="graph"
        data="graph.php?ifnum=<?=$ifnum;?>&amp;ifname=<?=rawurlencode($ifdescrs[$curif]);?>"
        type="image/svg+xml"
        width="550"
        height="275">
  <param name="src" value="graph.php?ifnum=<?=$ifnum;?>&amp;ifname=<?=rawurlencode($ifdescrs[$curif]);?>" />
  Your browser does not support this object type!<br /><span class="red"><strong>Note:</strong></span> The <a href="http://de.brothersoft.com/Renesis-Player-download-141155.html" target="_blank">RENESIS Player</a> is required to view the graph.
</object>

</div>
</td></tr></table>
<?php include("fend.inc");?>
