#!/usr/local/bin/php
<?php
/*
	status_graph_cpu.php
	Modified for XHTML by Daisuke Aoyama (aoyama@peach.ne.jp)
	Copyright (C) 2010 Daisuke Aoyama <aoyama@peach.ne.jp>.
	All rights reserved.

	part of FreeNAS (http://www.freenas.org)
	Copyright (C) 2005-2010 Olivier Cochard <olivier@freenas.org>.
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

$pgtitle = array(gettext("Status"), gettext("Graph"), gettext("CPU load"));
?>
<?php include("fbegin.inc");?>
<table width="100%" border="0" cellpadding="0" cellspacing="0">
  <tr>
		<td class="tabnavtbl">
  		<ul id="tabnav">
        <li class="tabinact"><a href="status_graph.php"><span><?=gettext("Traffic graph");?></span></a></li>
        <li class="tabact"><a href="status_graph_cpu.php" title="<?=gettext("Reload page");?>"><span><?=gettext("CPU load");?></span></a></li>
      </ul>
    </td>
  </tr>
  <tr>
    <td class="tabcont">
      <div align="center">
        <object id="graph" data="graph_cpu.php" type="image/svg+xml" width="550" height="275">
          <param name="src" value="graph_cpu.php" />
          Your browser does not support this object type!<br /><span class="red"><strong>Note:</strong></span> The <a href="http://www.examotion.com/index.php?id=product_player_download" target="_blank">RENESIS Player</a> is required to view the graph.
        </object>
      </div>
    </td>
  </tr>
</table>
<?php include("fend.inc");?>
