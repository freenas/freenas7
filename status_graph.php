#!/usr/local/bin/php
<?php
/*
	status_graph.php
	part of FreeNAS (http://www.freenas.org)
	Copyright (C) 2005-2009 Olivier Cochard <olivier@freenas.org>.
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
<form name="form1" action="" method="get" style="padding-bottom: 10px; margin-bottom: 14px; border-bottom: 1px solid #999999">
Interface:
<select name="if" class="formfld" onchange="document.form1.submit()">
<?php
foreach ($ifdescrs as $ifn => $ifd) {
	echo "<option value=\"$ifn\"";
	if ($ifn == $curif) echo " selected";
	echo ">" . htmlspecialchars($ifd) . "</option>\n";
}
?>
</select>
</form>

	<link rel="stylesheet" type="text/css" href="jsxgraph.css" />
	<script type="text/javascript" src="javascript/prototype.js"></script>
	<script type="text/javascript" src="javascript/jsxgraphcore.js"></script>

<div id="box" class="jxgbox" style="width:100%; height:350px;"></div>
<script type="text/javascript">
	var board = JXG.JSXGraph.initBoard('box', {originX: 20, originY: 330, unitX: 10, unitY: 25, axis:true});
	var graph1,graph2,graph3,graph4,graph5;
	var periodical;
 
 
 
 var dataX = [1,2,3,4,5,6,7,8];
var dataY = [0.3,4.0,-1,2.3,7,9,8,9];
board.createElement('curve', [dataX,dataY],{strokeColor:'red',strokeWidth:3});


 
 
 
 function doIt() {
            new Ajax.Request('stats.php?if=em0', {
                onComplete: function(transport) {
                    if (200 == transport.status) {
                        var t = transport.responseText;
                        var a = t.split(';');
                        var x = [];
                        var y = [];
                        for (var i=0;i<a.length-1;i++) { // The last array entry is empty
                            var b = a[i].split(',');
                            x[i]=b[0]*1.0;  
                            y[i]=b[1]*1.0;
                        }
                        var m = board.mathStatistics.mean(y);
                        var sd = board.mathStatistics.sd(y);
                        var med = board.mathStatistics.median(y);
                        if (!graph1) { 
                            graph1 = board.createElement('curve', [x,y],{strokeWidth:3}); 
                            graph2 = board.createElement('curve', [[x[0],x[x.length-1]],[m,m]], {strokecolor:'red'}); 
                        } else {
                            graph1.dataX = x;                    graph1.dataY = y;
                            graph2.dataX = [x[0],x[x.length-1]]; graph2.dataY = [m,m];
                        }
                        board.update();
                    };
                }});
        }
 
 	//periodical = setInterval(doIt,1000);
</script>


</td></tr></table>
<?php include("fend.inc");?>
